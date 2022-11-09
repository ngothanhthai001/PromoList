<?php

namespace Marvelic\PromoLists\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Model\PromotionFactory;

abstract class Promotion extends Action
{
    /**
     * @var PromotionFactory
     */
    protected $promotionFactory;

    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(
        StoreManagerInterface $storeManager,
        Registry $registry,
        Context $context
    ) {
        $this->storeManager  = $storeManager;
        $this->registry      = $registry;
        $this->context       = $context;
        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return \Marvelic\PromoLists\Model\Promotion|boolean
     */
    protected function initModel()
    {
        $id = $this->getRequest()->getParam(PromotionInterface::ID);
        if (!$id) {
            return false;
        }

        $promotion = $this->promotionFactory->create()->load($id);

        if (!$promotion->getId()) {
            return false;
        }

        $this->registry->register('current_promolist_promotion', $promotion);

        return $promotion;
    }
}
