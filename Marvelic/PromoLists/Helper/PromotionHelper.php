<?php

namespace Marvelic\PromoLists\Helper;

use Magento\Eav\Model\Config as EavConfig;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class PromotionHelper extends AbstractHelper
{
    /**
     * @var EavConfig
     */
    protected EavConfig $eavConfig;

    /**
     * @param EavConfig $eavConfig
     * @param Context $context
     */
    public function __construct(
        EavConfig $eavConfig,
        Context $context
    ) {
        $this->eavConfig = $eavConfig;
        parent::__construct($context);
    }
    public function getEavAttributeByCode($entityType, $attributeCode)
    {
        return $this->eavConfig->getAttribute($entityType, $attributeCode);
    }
    public function getCategoryCurrent()
    {
        return $this->registry->registry('current_promolist_category');
    }
    public function getParamsByUrl($url)
    {
        $urlParams = "";
        if (strpos($url, "?")) {
            $urlParams =  substr($url, strpos($url, "?"));
        }
        return $urlParams;
    }

}
