<?php

namespace Marvelic\PromoLists\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Marvelic\PromoLists\Model\ResourceModel\Category\CollectionFactory;

class Category extends AbstractHelper
{

    /**
     * @param CollectionFactory $categoryCollectionFactory
     * @param Context                         $context
     */
    public function __construct(
        CollectionFactory $categoryCollectionFactory,
        Context $context
    ) {
        $this->categoryCollectionFactory = $categoryCollectionFactory;

        parent::__construct($context);
    }

    /**
     * @return \Marvelic\PromoLists\Model\Category|false
     */
    public function getRootCategory()
    {
        $category   = false;
        $collection = $this->categoryCollectionFactory->create()
            ->addFieldToFilter('parent_id', 0);

        if ($collection->count()) {
            $category = $collection->getFirstItem();
        }

        return $category;
    }
    public function getAllCategoryIds($categoryId)
    {
        $categoryIds = null;
        if (!empty($categoryId)) {
            $categoryIds = array_keys($this->categoryCollectionFactory->create()->getTree($categoryId));
            $categoryIds[] = (int)$categoryId;
        }
        return $categoryIds;
    }
}
