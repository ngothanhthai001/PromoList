<?php

namespace Marvelic\PromoLists\Block\Promotion\View;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Model\Promotion;

class RelatedProducts extends AbstractProduct
{
    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->registry = $context->getRegistry();

        parent::__construct($context);
    }

    /**
     * @return Collection
     */
    public function getRelatedProducts()
    {
        return $this->getCurrentPromotion()->getRelatedProducts();
    }

    /**
     * @return Promotion
     */
    public function getCurrentPromotion()
    {
        return $this->registry->registry('current_promolist_promotion');
    }
}
