<?php

namespace Marvelic\PromoLists\Controller\Category;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Marvelic\PromoLists\Controller\Category;

class Index extends Category
{
    /**
     * @return Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        return $resultPage;
    }
}
