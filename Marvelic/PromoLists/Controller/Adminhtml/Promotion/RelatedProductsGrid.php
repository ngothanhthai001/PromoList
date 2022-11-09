<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Promotion;

use Marvelic\PromoLists\Controller\Adminhtml\Promotion;

class RelatedProductsGrid extends Promotion
{
    /**
     * @return void
     */
    public function execute()
    {
        $this->initModel();
        $this->_view->loadLayout()
            ->getLayout()
            ->getBlock('promolist.promotion.tab.products')
            ->setProductsRelated($this->getRequest()->getPromotion('products_related'));
        $this->_view->renderLayout();
    }
}
