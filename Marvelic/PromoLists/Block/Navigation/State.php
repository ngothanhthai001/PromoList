<?php

namespace Marvelic\PromoLists\Block\Navigation;

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Framework\View\Element\Template\Context;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Block\Html\Pager;
use Marvelic\PromoLists\Block\Promotion\PromotionList;
use Marvelic\PromoLists\Helper\PromotionHelper;
use Marvelic\PromoLists\Helper\Category as CategoryHelper;
use Marvelic\PromoLists\Model\CategoryRepository;

class State extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Marvelic_PromoLists::layer/state.phtml';

    /**
     * Catalog layer
     *
     * @var Layer
     */
    protected Layer $_catalogLayer;

    /**
     *
     * @var PromotionHelper
     */
    protected PromotionHelper $promotionHelper;

    /**
     *
     * @var Pager
     */
    protected Pager $pager;

    /**
     *
     * @var PromotionList
     */
    protected PromotionList $promotionList;

    /**
     *
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     *
     * @var CategoryHelper
     */
    protected CategoryHelper $categoryHelper;

    /**
     * @param PromotionHelper $promotionHelper
     * @param Pager $pager
     * @param PromotionList $promotionList
     * @param CategoryRepository $categoryRepository
     * @param CategoryHelper $categoryHelper
     * @param Context $context
     * @param Resolver $layerResolver
     * @param array $data
     */
    public function __construct(
        PromotionHelper $promotionHelper,
        Pager $pager,
        PromotionList $promotionList,
        CategoryRepository $categoryRepository,
        CategoryHelper $categoryHelper,
        Context $context,
        Resolver $layerResolver,
        array $data = []
    ) {
        $this->promotionHelper = $promotionHelper;
        $this->pager = $pager;
        $this->promotionList = $promotionList;
        $this->categoryRepository = $categoryRepository;
        $this->categoryHelper = $categoryHelper;
        $this->_catalogLayer = $layerResolver->get();
        parent::__construct($context, $data);
    }

    /**
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $data = [];
        $filters = array_keys($this->pager->trimSuffixAttr($this->getRequest()->getParams()));
        foreach ($filters as $filter) {
            $data[] = $this->promotionHelper->getEavAttributeByCode(Category::EAV_ATTRIBUTE_PRODUCT, $filter);
        }
        return $data;
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        $filterState = [];
        foreach ($this->getActiveFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }
        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;
        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }

    /**
     * Retrieve Layer object
     *
     * @return \Magento\Catalog\Model\Layer
     */
    public function getLayer()
    {
        if (!$this->hasData('layer')) {
            $this->setLayer($this->_catalogLayer);
        }
        return $this->_getData('layer');
    }
    public function getParamsUnsetCategory()
    {
        $params = $this->getRequest()->getParams();
        unset($params[PromotionInterface::ID]);
        return array_keys($params);
    }
    public function getUrlRemoveAttribute($attribute)
    {
        $suffix = "attr_";
        $params = $this->getRequest()->getParams();
        unset($params[$suffix . $attribute->getAttributeCode()]);
        if (count($params)) {
            $url = $this->pager->getPagerUrl($params);
        } else {
            $url = $this->pager->getPagerUrl();
        }
        return substr($url, strpos($url, "?"));
    }
    public function getUrlClearAll()
    {
        $url =  $this->pager->getPagerUrl();
        if (!empty($this->promotionList->getCategory()) && $this->promotionList->getCategory()->getParentId() != $this->categoryHelper->getRootCategory()->getId()) {
            $url = $this->categoryRepository->get($this->promotionList->getCategory()->getParentId())->getUrl();
        }
        return $url;
    }
    public function getParentCategory()
    {
        $productList = $this->promotionList->getCategory();
        if ($productList) {
            $parentId = $productList->getParentId();
            $data  = $this->categoryRepository->get($parentId);
            $params = $this->getRequest()->getParams();
            unset($params[PromotionInterface::ID]);
            $pager = $this->pager;
            $url = $data->getUrl() . $this->promotionHelper->getParamsByUrl($pager->getPagerUrl());
            if (!$data->getParentId()) {
                $url = $pager->getUrlNotCurrent($params);
            }
            return $url;
        }
        return null;
    }
}
