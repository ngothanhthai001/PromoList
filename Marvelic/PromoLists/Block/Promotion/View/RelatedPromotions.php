<?php

namespace Marvelic\PromoLists\Block\Promotion\View;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Marvelic\PromoLists\Model\Promotion;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\Collection;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\CollectionFactory as PromotionCollectionFactory;

class RelatedPromotions extends Template
{
    /**
     * @var PromotionCollectionFactory
     */
    protected $promotionCollectionFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param PromotionCollectionFactory $promotionCollectionFactory
     * @param Registry              $registry
     * @param Context               $context
     */
    public function __construct(
        PromotionCollectionFactory $promotionCollectionFactory,
        Registry $registry,
        Context $context
    ) {
        $this->promotionCollectionFactory = $promotionCollectionFactory;
        $this->registry              = $registry;

        parent::__construct($context);
    }

    /**
     * @return Collection
     */
    public function getPromotionCollection()
    {
        $collection = $this->promotionCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['neq' => $this->getCurrentPromotion()->getId()])
            ->addVisibilityFilter()
            ->addAttributeToSelect('*');

        return $collection;
    }

    /**
     * @return Promotion
     */
    public function getCurrentPromotion()
    {
        return $this->registry->registry('current_promolist_promotion');
    }
}
