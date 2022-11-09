<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Promotion;

use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Page\Interceptor;
use Magento\Framework\Controller\ResultFactory;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Controller\Adminhtml\Promotion;

class Edit extends Promotion
{
    /**
     * @return Page
     */
    public function execute()
    {
        /** @var Interceptor $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id    = $this->getRequest()->getParam(PromotionInterface::ID);
        $model = $this->initModel();

        if ($id && !is_array($id) && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('This promotion no longer exists.'));

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(
            $model->getName() ? $model->getName() : __('New Promotion')
        );

        return $resultPage;
    }
}
