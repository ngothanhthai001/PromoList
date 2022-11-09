<?php

namespace Marvelic\PromoLists\Controller\Category;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Marvelic\PromoLists\Controller\Category;

class View extends Category
{
    /**
     * @return Page
     */
    public function execute()
    {
        $category = $this->initCategory();
        if ($category) {
            /* @var Page $resultPage */
            $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

//            $resultPage->addPageLayoutHandles(['type' => 'layered'], null, false);
//            $resultPage->addPageLayoutHandles(['id' => $category->getId()]);
            return $resultPage;
        } else {
            $this->_forward('no_route');
        }
    }
}
