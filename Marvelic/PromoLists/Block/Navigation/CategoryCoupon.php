<?php

namespace Marvelic\PromoLists\Block\Navigation;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Marvelic\PromoLists\Api\Data\CategoryInterface;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Block\Html\Pager;
use Marvelic\PromoLists\Block\Promotion\AbstractBlock;
use Marvelic\PromoLists\Helper\PromotionHelper;
use Marvelic\PromoLists\Model\Config;
use Marvelic\PromoLists\Model\ResourceModel\Category\Collection as CategoryCollection;
use Marvelic\PromoLists\Model\ResourceModel\Category\CollectionFactory as CategoryFactory;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\Collection as PromotionCollection;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\CollectionFactory as PromotionFactory;

class CategoryCoupon extends AbstractBlock
{
    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 0;

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
     * @param CategoryFactory $categoryFactory
     * @param CategoryCollection $categoryCollection
     * @param PromotionFactory $promotionFactory
     * @param PromotionCollection $promotionCollection
     * @param Pager $pager
     * @param PromotionHelper $promotionHelper
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
        Registry $registry,
        Context $context
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryCollection = $categoryCollection;
        $this->promotionFactory = $promotionFactory;
        $this->promotionCollection = $promotionCollection;
        $this->pager = $pager;
        $this->promotionHelper = $promotionHelper;
        parent::__construct($config, $registry, $context);
    }

    /**
     * @throws LocalizedException
     */
    public function getCategoryPromotion($parentId = null)
    {
        $categoryTree = $this->categoryFactory->create();
        $parents = $categoryTree
            ->addAttributeToSelect([CategoryInterface::NAME,CategoryInterface::URL_KEY])
            ->addVisibilityFilter()
            ->excludeRoot()
            ->setOrder(CategoryInterface::LEVEL, self::ORDER_ASC);
        return $parents;
    }
    public function getAttributeAllow()
    {
        $data = [];
        $promotionFactory = $this->promotionFactory->create();
        $mergeAttribute = [];
        $allPromotions = $promotionFactory
            ->addAttributeToFilter("entity_id", $this->getRequest()->getParam('entity_id'))
            ->addVisibilityFilter()
            ->setOrder(PromotionInterface::ID, self::ORDER_DESC);
        foreach ($allPromotions as $promotion) {
            if ($promotion->getAttributeAllow()) {
                $arrayAttribute[] = explode(',', $promotion->getAttributeAllow());
            }
        }
        if (!empty($arrayAttribute)) {
            foreach ($arrayAttribute as $attribute) {
                $mergeAttribute = array_merge($mergeAttribute, $attribute);
            }
        }
        foreach (array_unique($mergeAttribute) as $item) {
            $codeAttribute = $this->promotionHelper->getEavAttributeByCode(self::EAV_ATTRIBUTE_PRODUCT, $item);
            $data[] = $codeAttribute;
        }
        return $data;
    }
    public function getCountAttributeByItem($attribute)
    {
//      ksort by key => query like in db
        $url =  $this->pager->trimSuffixAttr($this->getRequest()->getParams());
        unset($url[PromotionInterface::ID]);
        $url[$attribute->getAttributeCode()] =  $attribute->getId();
        $sortKey = array_keys($url);
        ksort($sortKey);
        $attributeAdd = implode("%", $sortKey);

        $promotionFactory = $this->promotionFactory->create();
        $currentPromolistCategory = $this->getCurrentCategory();
        if ($currentPromolistCategory) {
            $allPromotions = $promotionFactory
                ->addCategoryFilter($currentPromolistCategory)
                ->addFieldToFilter(PromotionInterface::ATTRIBUTE_ALLOW, ['like' => '%' . $attributeAdd . '%' ])
                ->addVisibilityFilter()
                ->setOrder(PromotionInterface::ID, self::ORDER_DESC);
        } else {
            $allPromotions = $promotionFactory
                ->addFieldToFilter(PromotionInterface::ATTRIBUTE_ALLOW, ['like' => '%' . $attributeAdd . '%' ])
                ->addVisibilityFilter()
                ->setOrder(PromotionInterface::ID, self::ORDER_DESC);
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
}
