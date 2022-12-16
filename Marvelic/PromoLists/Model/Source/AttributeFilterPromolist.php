<?php

namespace Marvelic\PromoLists\Model\Source;

use Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\Helper\Context;

class AttributeFilterPromolist extends \Magento\Framework\App\Helper\AbstractHelper implements \Magento\Framework\Option\ArrayInterface
{
    const STORE_CONFIG_ATTRIBUTE_PROMO = "promo_lists/attributes_filter/apply_to";
    /** @var  CollectionFactory */
    protected $collectionFactory;

    public function __construct(Context $context, CollectionFactory $collectionFactory)
    {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        foreach ($this->_getOptions() as $optionValue => $optionLabel) {
            $options[] = ['value' => $optionValue, 'label' => $optionLabel];
        }
        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_getOptions();
    }

    protected function _getOptions()
    {
        $attributePromolist = $this->scopeConfig->getValue(self::STORE_CONFIG_ATTRIBUTE_PROMO);
        $dataAttribute = explode(",", $attributePromolist);
        $options = [];
        foreach ($dataAttribute as $attribute) {
            $options[$attribute] = $attribute;
        }
        return $options;
    }
}
