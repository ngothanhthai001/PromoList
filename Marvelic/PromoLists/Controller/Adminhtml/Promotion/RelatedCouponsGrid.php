<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Promotion;

use Marvelic\PromoLists\Controller\Adminhtml\Promotion;

class RelatedCouponsGrid extends Promotion
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->initModel();
        $this->_view->loadLayout()
            ->getLayout()
            ->getBlock('promolist.promotion.tab.coupons')
            ->setCouponsRelated($this->getRequest()->getPromotion('coupons_related'));
        $this->_view->renderLayout();
    }
}
