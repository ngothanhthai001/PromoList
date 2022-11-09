<?php

namespace Marvelic\PromoLists\Controller\Promotion;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\NotFoundException;
use Marvelic\PromoLists\Controller\Promotion;

class View extends Promotion
{
    /**
     * @return Page
     * @throws NotFoundException
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute()
    {
        $promotion = $this->initModel();

        if (!$promotion) {
            throw new NotFoundException(__('Page not found'));
            die;
        }

        /* @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        return $resultPage;
    }
}
