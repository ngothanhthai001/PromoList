<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Promotion\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Catalog\Model\Product\Visibility as ProductVisibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Model\Promotion;

class Products extends Extended
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var ProductVisibility
     */
    protected $visibility;

    /**
     * @var ProductStatus
     */
    protected $status;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory]
     */
    protected $setsFactory;


    /**
     * @param Registry $registry
     * @param ProductCollectionFactory $productCollectionFactory
     * @param ProductVisibility $visibility
     * @param ProductStatus $status
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        ProductCollectionFactory $productCollectionFactory,
        ProductVisibility $visibility,
        ProductStatus $status,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setsFactory,
        Context $context,
        BackendHelper $backendHelper,
        array $data = []
    ) {
        $this->registry                 = $registry;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->visibility               = $visibility;
        $this->status                   = $status;
        $this->productFactory           = $productFactory;
        $this->setsFactory = $setsFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Retrive grid URL
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'marvelic_promolists/promotion/relatedProductsGrid',
            ['_current' => true]
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('related_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);

        if ($this->getPromotion() && $this->getPromotion()->getId()) {
            $this->setDefaultFilter(['in_products' => 1]);
        }
        if ($this->isReadonly()) {
            $this->setFilterVisibility(false);
        }
    }

    /**
     * @return Promotion
     */
    public function getPromotion()
    {
        return $this->registry->registry('promotion');
    }

    /**
     * Retrieve currently edited product model
     *
     * @return array|null
     */
    public function getProduct()
    {
        return $this->registry->registry('promotion');
    }

    /**
     * {@inheritdoc}
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $productIds]);
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $productIds]);
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }

        return $this;
    }

    /**
     * Retrieve selected related products
     * @return array
     */
    protected function getSelectedProducts()
    {
        $products = $this->getProductsRelated();
        if (!is_array($products)) {
            $products = array_keys($this->getSelectedRelatedProducts());
        }

        return $products;
    }

    /**
     * Retrieve related products
     * @return array
     */
    public function getSelectedRelatedProducts()
    {
        $products = [];
        foreach ($this->getPromotion()->getRelatedProducts() as $product) {
            $products[$product->getId()] = ['position' => 0];
        }

        return $products;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect('*');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return bool
     */
    public function isReadonly()
    {
        return $this->getProduct() && $this->getProduct()->getRelatedReadonly();
    }

//    /**
//     * {@inheritdoc}
//     */
//    protected function _prepareColumns()
//    {
//        $this->addColumn('in_products', [
//            'type'             => 'checkbox',
//            'name'             => 'in_products',
//            'values'           => $this->getSelectedProducts(),
//            'align'            => 'center',
//            'index'            => 'entity_id',
//            'header_css_class' => 'col-select',
//            'column_css_class' => 'col-select',
//        ]);

//        $sets = $this->setsFactory->create()->setEntityTypeFilter(
//            $this->productFactory->create()->getResource()->getTypeId()
//        )->load()->toOptionHash();
//
//        $this->addColumn(
//            'set_name',
//            [
//                'header' => __('Attribute Set'),
//                'index' => 'attribute_set_id',
//                'type' => 'options',
//                'options' => $sets,
//                'header_css_class' => 'col-attr-name',
//                'column_css_class' => 'col-attr-name'
//            ]
//        );
//        $this->addColumn('position', [
//            'header'           => __('Position'),
//            'name'             => 'position',
//            'type'             => 'number',
//            'validate_class'   => 'validate-number',
//            'index'            => 'position',
//            'editable'         => true,
//            'is_system'        => 1,
//            'header_css_class' => 'col-hidden',
//            'column_css_class' => 'col-hidden',
//        ]);
//
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
//        $this->addColumn(
//            'sku',
//            [
//                'header' => __('SKU'),
//                'index' => 'sku',
//                'header_css_class' => 'col-sku',
//                'column_css_class' => 'col-sku'
//            ]
//        );
//
//        $this->addColumn(
//            'status',
//            [
//                'header' => __('Status'),
//                'index' => 'status',
//                'type' => 'options',
//                'options' => $this->status->getOptionArray(),
//                'header_css_class' => 'col-status',
//                'column_css_class' => 'col-status'
//            ]
//        );
//
//        $this->addColumn(
//            'visibility',
//            [
//                'header' => __('Visibility'),
//                'index' => 'visibility',
//                'type' => 'options',
//                'options' => $this->visibility->getOptionArray(),
//                'header_css_class' => 'col-visibility',
//                'column_css_class' => 'col-visibility'
//            ]
//        );
//    }
}
