<?php

namespace Marvelic\PromoLists\Block\Promotion;

use Marvelic\PromoLists\Model\Category;
use Marvelic\PromoLists\Model\Promotion;

class Meta extends AbstractBlock
{
    /**
     * @return Promotion
     */
    public function getPromotion()
    {
        if ($this->hasData('promotion')) {
            return $this->getData('promotion');
        }

        return $this->registry->registry('current_promolist_promotion');
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_promolist_category');
    }

    /**
     * @param string $date
     *
     * @return string
     */
    public function toDateFormat($date)
    {
        return date($this->config->getDateFormat(), strtotime($date));
    }

    /**
     * @return bool
     */
    public function isAddThisEnabled()
    {
        return $this->config->isAddThisEnabled();
    }
}
