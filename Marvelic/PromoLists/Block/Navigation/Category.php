<?php

namespace Marvelic\PromoLists\Block\Navigation;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Marvelic\PromoLists\Api\CategoryRepositoryInterface;
use Marvelic\PromoLists\Api\Data\CategoryInterface;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Block\Html\Pager;
use Marvelic\PromoLists\Block\Promotion\AbstractBlock;
use Marvelic\PromoLists\Block\Promotion\PromotionList;
use Marvelic\PromoLists\Helper\Category as HelperCategory;
use Marvelic\PromoLists\Helper\PromotionHelper;
use Marvelic\PromoLists\Model\Config;
use Marvelic\PromoLists\Model\ResourceModel\Category\Collection as CategoryCollection;
use Marvelic\PromoLists\Model\ResourceModel\Category\CollectionFactory as CategoryFactory;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\Collection as PromotionCollection;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\CollectionFactory as PromotionFactory;

class Category extends AbstractBlock
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;
    const FILTER_CATEGORY = 'attr_cat';

    const ORDER_DESC = "DESC";

    const ORDER_ASC  = "ASC";

    const EAV_ATTRIBUTE_PRODUCT = "catalog_product";

    protected $fromRoot = true;

    /**
     * @var CategoryFactory
     */
    protected CategoryFactory $categoryFactory;

    /**
     * @var CategoryCollection
     */
    protected CategoryCollection $categoryCollection;

    /**
     * @var PromotionFactory
     */
    protected PromotionFactory $promotionFactory;

    /**
     * @var PromotionCollection
     */

    protected PromotionCollection $promotionCollection;

    /**
     * @var Pager
     */
    protected Pager $pager;

    /**
     * @var PromotionHelper
     */
    protected PromotionHelper $promotionHelper;

    /**
     * @var CategoryRepositoryInterface
     */
    protected CategoryRepositoryInterface $categoryRepository;

    /**
     * @var PromotionList
     */
    protected PromotionList $promotionList;

    /**
     * @var HelperCategory
     */
    protected HelperCategory $helperCategory;

    /**
     * @param CategoryFactory $categoryFactory
     * @param CategoryCollection $categoryCollection
     * @param PromotionFactory $promotionFactory
     * @param PromotionCollection $promotionCollection
     * @param Pager $pager
     * @param PromotionHelper $promotionHelper
     * @param CategoryRepositoryInterface $categoryRepository
     * @param PromotionList $promotionList
     * @param HelperCategory $helperCategory
     * @param Config   $config
     * @param Registry $registry
     * @param Context  $context
     */

    public function __construct(
        CategoryFactory $categoryFactory,
        CategoryCollection $categoryCollection,
        PromotionFactory $promotionFactory,
        PromotionCollection $promotionCollection,
        Pager $pager,
        Config $config,
        PromotionHelper $promotionHelper,
        CategoryRepositoryInterface $categoryRepository,
        PromotionList $promotionList,
        HelperCategory $helperCategory,
        Registry $registry,
        Context $context
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollection = $categoryCollection;
        $this->promotionFactory = $promotionFactory;
        $this->promotionCollection = $promotionCollection;
        $this->pager = $pager;
        $this->promotionHelper = $promotionHelper;
        $this->categoryRepository = $categoryRepository;
        $this->promotionList = $promotionList;
        $this->helperCategory = $helperCategory;
        parent::__construct($config, $registry, $context);
    }

    /**
     * @throws LocalizedException
     */
    public function getCategoryPromotion($parentId = null)
    {
        $categoryTree = $this->categoryFactory->create();
        $currentPromolistCategory = $this->getCurrentCategory();
        if ($currentPromolistCategory) {
            $parents = $categoryTree
                ->addAttributeToSelect([CategoryInterface::NAME,CategoryInterface::URL_KEY])
                ->addVisibilityFilter()
                ->addFieldToFilter(CategoryInterface::PARENT_ID, $currentPromolistCategory->getId())
                ->excludeRoot()
                ->setOrder(CategoryInterface::LEVEL, self::ORDER_ASC);
        } else {
            $rootId = $this->categoryCollection->getRootId();
            $parents = $categoryTree
                ->addAttributeToSelect([CategoryInterface::NAME,CategoryInterface::URL_KEY])
                ->addVisibilityFilter()
                ->addFieldToFilter(CategoryInterface::PARENT_ID, $rootId)
                ->excludeRoot()
                ->setOrder(CategoryInterface::LEVEL, self::ORDER_ASC);
        }
        $categoryRequest = $this->getRequest()->getParam(self::FILTER_CATEGORY);
        if (!empty($categoryRequest)) {
            $parents->addFieldToFilter(CategoryInterface::ID, ['nin'=> $categoryRequest]);
        }
        foreach ($parents as $item) {
            $promotionItem = $this->promotionFactory->create()->addCategoryFilter($item)->addVisibilityFilter()->count();
            if (!$promotionItem) {
                $parents->removeItemByKey($item->getId());
            }
        }
        return $parents;
    }
    public function getAttributeAllow()
    {
        $cateId = $this->getRequest()->getParam(self::FILTER_CATEGORY);
        if (!empty($cateId)) {
            $categoryIds = $this->helperCategory->getAllCategoryIds($cateId);
        }
        $promotionCollection = $this->promotionList->getPromotionCollection();
        $allPromotions = clone $promotionCollection;
        if (!empty($categoryIds)) {
            $allPromotions->clear()->addArrayCategoryFilter($categoryIds);
        }
        return $this->getAttributeAllowFilter($allPromotions);
    }
    public function getCountAttributeByItem($attribute)
    {
//      ksort by key => query like in db
        $url =  $this->pager->trimSuffixAttr($this->getRequest()->getParams());
        unset($url[PromotionInterface::ID]);
        unset($url['cat']);
        $url[$attribute->getAttributeCode()] =  $attribute->getId();
        $sortKey = array_keys($url);
        sort($sortKey);
        $attributeAdd = implode("%", $sortKey);
        $promotionFactory = $this->promotionFactory->create();
        $currentPromolistCategory = $this->getCurrentCategory();
        if ($currentPromolistCategory) {
            $promotionCollection = $this->promotionList->getPromotionCollection();
            $allPromotions = clone $promotionCollection;
            $allPromotions->clear()->addFieldToFilter(PromotionInterface::ATTRIBUTE_ALLOW, ['like' => '%' . $attributeAdd . '%' ]);
        } else {
            if ($cateId = $this->getRequest()->getParam(self::FILTER_CATEGORY)) {
                $categoryIds = array_keys($this->categoryFactory->create()->getTree($cateId));
                $categoryIds[] = (int)$cateId;
                $promotionCollection = $this->promotionList->getPromotionCollection();
                $allPromotions = clone $promotionCollection;
                $allPromotions->clear()->addFieldToFilter(PromotionInterface::ATTRIBUTE_ALLOW, ['like' => '%' . $attributeAdd . '%' ]);
            } else {
                $categoryIds = array_keys($this->categoryFactory->create()->getTree($cateId));
                $categoryIds[] = (int)$cateId;
                $promotionCollection = $this->promotionList->getPromotionCollection();
                $allPromotions = clone $promotionCollection;
                $allPromotions->clear()->addFieldToFilter(PromotionInterface::ATTRIBUTE_ALLOW, ['like' => '%' . $attributeAdd . '%' ]);
            }
        }
        return $allPromotions->count();
    }
    public function getCurrentCategory()
    {
        return $this->registry->registry(CategoryInterface::CURRENT_PROMOTION_CATEGORY);
    }
    public function getUrlFilterAttribute($attribute)
    {
        $addParam = [
            "attr_" . $attribute->getAttributeCode() => $attribute->getId()
        ];
        $url = $this->pager->getUrlNotCurrent($addParam);
        return substr($url, strpos($url, "?"));
    }
    public function getUrlFilterAttributeCatePromotion($promoId)
    {
        $addParam = [
            "attr_cat" => $promoId
        ];
        $url = $this->pager->getUrlNotCurrent($addParam);
        return substr($url, strpos($url, "?"));
    }
    public function isAttributeFilter($attribute)
    {
        $urlOld = $this->pager->getUrlNotCurrent();
        return str_contains($urlOld, "attr_" . $attribute->getAttributeCode());
    }
    public function isHiddenAttributeOption()
    {
        $count = count($this->getAttributeAllow());
        $urlOld = $this->pager->getPagerUrl();
        foreach ($this->getAttributeAllow() as $attributeAllow) {
            if (str_contains($urlOld, $attributeAllow->getAttributeCode())) {
                $count--;
            }
        }
        return $count;
    }
    public function getCountPromotionByCatagory(CategoryInterface $model)
    {
        $promotion = null;
        $params = $this->getRequest()->getParams();
        $categoryIds = $this->helperCategory->getAllCategoryIds($model->getId());
        $attribute = $this->convertFilterParams($params);
        if (!empty($categoryIds)) {
            $allPromotion = $this->promotionList->getPromotionCollection();
            $promotion = clone $allPromotion;
            $promotion->addArrayCategoryFilter($categoryIds);
        }
        if (!empty($attribute)) {
            $promotion->addFieldToFilter(PromotionInterface::ATTRIBUTE_ALLOW, ['like' => '%' . implode('%', $attribute) . '%' ]);
        }
        return $promotion;
    }
    public function getCategoryFilter()
    {
        $categoryTree = $this->categoryFactory->create();
        $currentPromolistCategory = $this->getCurrentCategory();
        if ($currentPromolistCategory) {
            $filterCategory =  $categoryTree
                ->addAttributeToSelect([CategoryInterface::NAME,CategoryInterface::URL_KEY])
                ->addVisibilityFilter()
                ->addFieldToFilter(CategoryInterface::PARENT_ID, $currentPromolistCategory->getId())
                ->excludeRoot()
                ->setOrder(CategoryInterface::LEVEL, self::ORDER_ASC);
        } else {
            $rootId = $this->categoryCollection->getRootId();
            $filterCategory =  $categoryTree
                ->addAttributeToSelect([CategoryInterface::NAME,CategoryInterface::URL_KEY])
                ->addVisibilityFilter()
                ->addFieldToFilter(CategoryInterface::PARENT_ID, $rootId)
                ->excludeRoot()
                ->setOrder(CategoryInterface::LEVEL, self::ORDER_ASC);
//            if (!empty($attributes)) {
//                $filterCategory->removeItemByKey($this->getRequest()->getParam('attr_cat'));
////                $categoryFilter = $this->categoryRepository->get($this->getRequest()->getParam('attr_cat'));
////                $t = $this->filterPromotionAttr($categoryFilter);
//            }
        }
        return $filterCategory;
    }
    public function convertFilterParams($params)
    {
        $attribute = [];
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                if (str_contains($key, 'attr_') && $key != "attr_cat") {
                    $attribute[] = str_replace('attr_', "", $key);
                }
            }
        }
        return $attribute;
    }
    public function filterPromotionAttr(CategoryInterface $model)
    {
        $params = $this->getRequest()->getParams();
        if ($params) {
            $attribute = [];
            foreach ($params as $key => $value) {
                if (str_contains($key, 'attr_') && $key != "attr_cat") {
                    $attribute[] = str_replace('attr_', "", $key);
                }
            }
        }
        if (!empty($attribute)) {
            $promotion = $this->promotionFactory->create()
                ->addFieldToFilter(PromotionInterface::ATTRIBUTE_ALLOW, ['like' => '%' . implode('%', $attribute) . '%' ])
                ->addCategoryFilter($model)->addVisibilityFilter();
        } else {
            $promotion = $this->promotionFactory->create()->addCategoryFilter($model)->addVisibilityFilter();
        }
        return $promotion;
    }
    public function isFilterCategory()
    {
        if ($this->getRequest()->getParam('attr_cat')) {
            return true;
        }
        return false;
    }

    public function getAttributeAllowFilter($allPromotions)
    {
        $data = [];
        $mergeAttribute = [];
        foreach ($allPromotions as $promotion) {
            if ($promotion->getAttributeAllow()) {
                $arrayAttribute[] = explode(',', $promotion->getAttributeAllow());
            }
        }
        if (!empty($arrayAttribute)) {
            foreach ($arrayAttribute as $promotion) {
                $mergeAttribute = array_merge($mergeAttribute, $promotion);
            }
        }
        foreach (array_unique($mergeAttribute) as $item) {
            $codeAttribute = $this->promotionHelper->getEavAttributeByCode(self::EAV_ATTRIBUTE_PRODUCT, $item);
            $data[] = $codeAttribute;
        }
        return $data;
    }

}
