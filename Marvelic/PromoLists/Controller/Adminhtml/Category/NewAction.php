<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Category;

use Marvelic\PromoLists\Controller\Adminhtml\Category;

class NewAction extends Category
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultRedirectFactory->create()->setPath('*/*/edit');
    }
}
