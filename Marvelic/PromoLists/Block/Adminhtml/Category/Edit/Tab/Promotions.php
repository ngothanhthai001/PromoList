<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Model\Category;
use Magento\Backend\Helper\Data as BackendHelper;
use Marvelic\PromoLists\Model\PromotionFactory;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\Collection;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\CollectionFactory as PromotionCollectionFactory;

class Promotions extends Extended implements TabInterface
{
    /**
     * @var Registry
     */
    protected $coreRegistry;
    /**
     * @var PromotionFactory
     */
    protected $promotionFactory;
    /**
     * @var PromotionCollectionFactory
     */
    protected $promotionCollectionFactory;

    /**
     * Promotion constructor.
     *
     * @param PromotionCollectionFactory $promotionCollectionFactory
     * @param Registry $coreRegistry
     * @param PromotionFactory $bannerFactory
     * @param Context $context
     * @param backendHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        PromotionCollectionFactory $promotionCollectionFactory,
        Registry $coreRegistry,
        PromotionFactory $promotionFactory,
        Context $context,
        BackendHelper $backendHelper,
        array $data = []
    ) {
        $this->promotionCollectionFactory = $promotionCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->promotionFactory = $promotionFactory;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_category_promotion');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
    }

    /**
     * @return Extended
     */
    protected function _prepareColumns()
    {
//        $this->addColumn(
//            'in_category',
//            [
//                'type' => 'checkbox',
//                'name' => 'in_category',
//                'values' => $this->_getSelectedPromotions(),
//                'index' => 'entity_id',
//                'header_css_class' => 'col-select col-massaction',
//                'column_css_class' => 'col-select col-massaction'
//            ]
//        );
//        $this->addColumn(
//            'entity_id',
//            [
//                'header' => __('ID'),
//                'sortable' => true,
//                'index' => 'entity_id',
//                'header_css_class' => 'col-id',
//                'column_css_class' => 'col-id'
//            ]
//        );
//        $this->addColumn('name', ['header' => __('Name'), 'index' => 'name']);
//
//        $this->addColumn(
//            'status',
//            [
//                'header' => __('Status'),
//                'index' => 'status',
//                'type' => 'options',
//                'options' => $this->status->getOptionArray()
//            ]
//        );


        return parent::_prepareColumns();
    }

    /**
     * get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            '*/*/bannersGrid',
            [
                'slider_id' => $this->getSlider()->getId()
            ]
        );
    }

    public function getTabLabel()
    {
        return __('Promotions');
    }

    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
