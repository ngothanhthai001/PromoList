<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Category;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Marvelic\PromoLists\Controller\Adminhtml\Category;

class Index extends Category
{
    /**
     * @return Page
     */
    public function execute()
    {

        /** @var Page $resultPage */
        $resultPage = $this->context->getResultFactory()->create(ResultFactory::TYPE_PAGE);

        $this->initPage($resultPage)
            ->getConfig()->getTitle()->prepend(__('Categories'));

        $this->_addContent($resultPage->getLayout()
            ->createBlock('\Marvelic\PromoLists\Block\Adminhtml\Category'));

        return $resultPage;
    }
}
