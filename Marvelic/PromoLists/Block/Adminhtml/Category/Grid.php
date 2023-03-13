<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Category;

use Exception;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Helper\Data as BackendHelper;
use Marvelic\PromoLists\Model\Category;
use Marvelic\PromoLists\Model\CategoryRepository;
use Marvelic\PromoLists\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class Grid extends ExtendedGrid
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var Category
     */
    protected $category;

    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param CategoryRepository        $categoryRepository
     * @param Context                   $context
     * @param BackendHelper             $backendHelper
     * @param array                     $data
     */
    public function __construct(
        CategoryCollectionFactory $categoryCollectionFactory,
        Category $category,
        CategoryRepository $categoryRepository,
        Context $context,
        BackendHelper $backendHelper,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->category = $category;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @param Category $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', ['id' => $row->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('promotions_category_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection = $collection->addAttributeToSelect('*')->setOrder('path', "asc");
//        $this->categoryRepository->getCollection()->setOrder('path','asc');
//        $collection= $this->categoryCollectionFactory->create()->setOrder('path', 'asc');
//        $collection = $collection->getItems();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Sort category tree
     * {@inheritdoc}
     */
    protected function _afterLoadCollection()
    {
        $categories = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->toOptionArray();

        $collection = clone $this->getCollection();
        $ordered    = $this->getCollection()->removeAllItems();
        foreach ($categories as $category) {
            if ($item = $collection->getItemById($category['value'])) {
                $ordered->addItem($item);
            }
        }

        $this->setCollection($ordered);

        return parent::_afterLoadCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', [
            'header'   => __('Title'),
            'index'    => 'name',
            'filter'   => false,
            'sortable' => false,
            'renderer' => 'Marvelic\PromoLists\Block\Adminhtml\Category\Grid\Renderer\Title',
        ]);

        $this->addColumn('status', [
            'header'   => __('Status'),
            'index'    => 'status',
            'type'     => 'options',
            'sortable' => false,
            'options'  => [
                0 => __('Disabled'),
                1 => __('Enabled'),
            ],
        ]);

        return parent::_prepareColumns();
    }
}
