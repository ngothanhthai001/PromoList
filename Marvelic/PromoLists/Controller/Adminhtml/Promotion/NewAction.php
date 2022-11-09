<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Promotion;

use Marvelic\PromoLists\Controller\Adminhtml\Promotion;

class NewAction extends Promotion
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        return $this->resultRedirectFactory->create()->setPath('*/*/edit');
    }
}
