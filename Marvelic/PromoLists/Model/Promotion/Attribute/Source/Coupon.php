<?php

namespace Marvelic\PromoLists\Model\Promotion\Attribute\Source;

use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

/**
 * Catalog category landing page attribute source
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Coupon extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Block collection factory
     *
     * @var CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * Construct
     *
     * @param CollectionFactory $ruleCollectionFactory
     */
    public function __construct(CollectionFactory $ruleCollectionFactory)
    {
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->ruleCollectionFactory->create()->load()->toOptionArray();
            array_unshift($this->_options, ['value' => '', 'label' => __('Please select .')]);
        }
        return $this->_options;
    }
}
