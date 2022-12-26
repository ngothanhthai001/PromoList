<?php

namespace Marvelic\PromoLists\Block\Promotion;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Api\PromotionRepositoryInterface;
use Marvelic\PromoLists\Model\Category;
use Marvelic\PromoLists\Model\Config;
use Marvelic\PromoLists\Model\Promotion;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\Collection;

class PromotionList extends AbstractBlock implements IdentityInterface
{
    /**
     * @var string
     */
    protected $defaultToolbarBlock = 'Marvelic\PromoLists\Block\Promotion\PromotionList\Toolbar';

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    public $_dir;

    public function __construct(
        PromotionRepositoryInterface $promotionRepository,
        Config $config,
        Registry $registry,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        Context $context
    ) {
        $this->promotionRepository = $promotionRepository;
        $this->_dir = $dir;

        parent::__construct($config, $registry, $context);
    }

    /**
     * @return string
     */
    public function getAdditionalHtml()
    {
        return $this->getChildHtml('additional');
    }

    /**
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Return identifiers for promotion content .
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];

        foreach ($this->getPromotionCollection() as $promotion) {
            $identities = array_merge($identities, $promotion->getIdentities());
        }

        return $identities;
    }

    /**
     * @return string
     */
    public function getSearchQuery()
    {
        return $this->registry->registry('current_promolist_query');
    }

    /**
     * @param Promotion $promotion
     *
     * @return string
     */
    public function getFeaturedAlt($promotion)
    {
        return $promotion->getFeaturedAlt() ?: $promotion->getName();
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();

        // called prepare sortable parameters
        $collection = $this->getPromotionCollection();

        // use sortable parameters
        $orders = $this->getAvailableOrders();
        if ($orders) {
            $toolbar->setAvailableOrders($orders);
        }

        $sort = $this->getSortBy();
        if ($sort) {
            $toolbar->setDefaultOrder($sort);
        }

        $dir = $this->getDefaultDirection();
        if ($dir) {
            $toolbar->setDefaultDirection($dir);
        }

        $toolbar->setCollection($collection);

        $this->setChild('pager', $toolbar);

        $this->setCollection($toolbar->getCollection());

        $this->getPromotionCollection()->load();

        return parent::_beforeToHtml();
    }

    /**
     * @return PromotionList\Toolbar
     */
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();

        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }

        $block = $this->getLayout()->createBlock($this->defaultToolbarBlock, uniqid(microtime()));

        return $block;
    }

    /**
     * @return Collection
     */
    public function getPromotionCollection()
    {
        $toolbar = $this->getToolbarBlock();

        if (empty($this->collection)) {
            $collection = $this->promotionRepository->getCollection()
                ->addAttributeToSelect([
                    '*',
                ])
                ->addStoreFilter($this->context->getStoreManager()->getStore()->getId())
                ->addVisibilityFilter();

            if ($category = $this->getCategory()) {
                $collection->addCategoryFilter($category);
            } elseif ($q = $this->getRequest()->getParam('q')) {
                $collection->addSearchFilter($q);
            }
            if ($attributeAllow = implode('%', $this->getAttributeParamsFilter())) {
                $collection->addFieldToFilter(PromotionInterface::ATTRIBUTE_ALLOW, ['like' => '%' . $attributeAllow . '%' ]);
            }

            $collection->setCurPage($this->getCurrentPage());

            $limit = (int)$toolbar->getLimit();
            if ($limit) {
                $collection->setPageSize($limit);
            }

            $page = (int)$toolbar->getCurrentPage();
            if ($page) {
                $collection->setCurPage($page);
            }

            if ($order = $toolbar->getCurrentOrder()) {
                $collection->setOrder($order, $toolbar->getCurrentDirection());
            } else {
                $collection->defaultOrder();
            }

            $this->collection = $collection;
        }

        return $this->collection;
    }

    /**
     * Retrieve current category model object.
     * @return Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_promolist_category');
    }

    /**
     * @param Collection $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    public function getAttributeParamsFilter()
    {
        $attribute = [];
        $param = $this->getRequest()->getParams();
        foreach ($param as $key => $value) {
            if (str_contains($key, "attr_")) {
                $attribute[] = str_replace("attr_", "", $key);
            }
        }
        return $attribute;
    }
    public function getAttributeKeyValueParams()
    {
        $attribute = [];
        $param = $this->getRequest()->getParams();
        foreach ($param as $key => $value) {
            if (str_contains($key, "attr_")) {
                $attribute[$key] = $value;
            }
        }
        return $attribute;
    }
}
