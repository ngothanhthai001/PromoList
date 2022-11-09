<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Promotion;

use Exception;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Controller\Adminhtml\Promotion;

class Delete extends Promotion
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
                $this->promotionRepository->delete($model);

                $this->messageManager->addSuccessMessage(__('The promotion has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [PromotionInterface::ID => $model->getId()]);
            }
        } else {
            $this->messageManager->addErrorMessage(__('This promotion no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }
    }
}
