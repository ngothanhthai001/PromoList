<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Promotion\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Model\Promotion;

class Coupons extends Extended

{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $setsFactory;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    protected $ruleFactory;


    /**
     * @param Registry $registry
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        Context $context,
        BackendHelper $backendHelper,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        array $data = []
    ) {
        $this->registry                 = $registry;
        $this->setsFactory = $setsFactory;
        $this->ruleFactory = $ruleFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Retrive grid URL
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'marvelic_promolists/promotion/relatedCouponsGrid',
            ['_current' => true]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDefaultSort('rule_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);

        if ($this->getPromotion() && $this->getPromotion()->getId()) {
            $this->setDefaultFilter(['in_coupons' => 1]);
        }
    }

    /**
     * @return Promotion
     */
    public function getPromotion()
    {
        return $this->registry->registry('promotion');
    }

//    /**
//     * Retrieve currently edited product model
//     *
//     * @return array|null
//     */
//    public function getProduct()
//    {
//        return $this->registry->registry('current_product');
//    }

    /**
     * {@inheritdoc}
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_coupons') {
            $couponIds = $this->getSelectedCoupons();
            if (empty($couponIds)) {
                $couponIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('rule_id', ['in' => $couponIds]);
            } else {
                if ($couponIds) {
                    $this->getCollection()->addFieldToFilter('rule_id', ['nin' => $couponIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Retrieve selected related coupons
     * @return array
     */
    protected function getSelectedCoupons()
    {
        $coupons = $this->getCouponsRelated();
        if (!is_array($coupons)) {
            $coupons = array_keys($this->getSelectedRelatedCoupons());
        }

        return $coupons;
    }

    /**
     * Retrieve related coupons
     * @return array
     */
    public function getSelectedRelatedCoupons()
    {
        $coupons = [];
        foreach ($this->getPromotion()->getRelatedCoupons() as $coupon) {
            $coupons[$coupon->getId()] = ['position' => 0];
        }

        return $coupons;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->ruleFactory->create()->getResourceCollection();
        $this->setCollection($collection);

        $this->_eventManager->dispatch(
            'adminhtml_block_promo_widget_chooser_prepare_collection',
            ['collection' => $collection]
        );

        return parent::_prepareCollection();
    }

//    /**
//     * {@inheritdoc}
//     */
//    protected function _prepareColumns()
//    {
//        $this->addColumn('in_coupons', [
//            'type'             => 'checkbox',
//            'name'             => 'in_coupons',
//            'values'           => $this->getSelectedCoupons(),
//            'align'            => 'center',
//            'index'            => 'rule_id',
//            'header_css_class' => 'col-select',
//            'column_css_class' => 'col-select',
//        ]);
//
//        $this->addColumn(
//            'rule_id',
//            [
//                'header' => __('ID'),
//                'sortable' => true,
//                'index' => 'rule_id',
//                'header_css_class' => 'col-id',
//                'column_css_class' => 'col-id'
//            ]
//        );
//
//        $this->addColumn(
//            'name',
//            [
//                'header' => __('Name'),
//                'index' => 'name',
//                'header_css_class' => 'col-name',
//                'column_css_class' => 'col-name'
//            ]
//        );
//
//    }
}
