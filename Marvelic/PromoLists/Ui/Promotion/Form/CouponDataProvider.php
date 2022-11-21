<?php

namespace Marvelic\PromoLists\Ui\Promotion\Form;

use Magento\SalesRule\Model\ResourceModel\Coupon\CollectionFactory as CouponCollectionFactory;
//use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

class CouponDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;
    protected $ruleCollectionFactory;

    public function __construct(CouponCollectionFactory $collection, CollectionFactory $ruleCollectionFactory, $name, $primaryFieldName, $requestFieldName, array $meta = [], array $data = [])
    {
        $this->collection = $collection->create();
        $this->ruleCollectionFactory = $ruleCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }
    public function getData(): array
    {
        $result = [
            'totalRecords' => $this->collection->count(),
            'items' => [],
        ];
        foreach ($this->collection as $coupon) {
            foreach ($this->ruleCollectionFactory as $rule) {
                if ($coupon->getRuleId() == $rule->getRuleId()) {
                    $data = [
                        'coupon_id' => $coupon->getId(),
                        'code' => $coupon->getCode(),
                        'rule_name' => $rule->getData('name'),
                        'to_date' => $rule->getData('to_date'),
                        'from_date' => $rule->getData('from_date')
                    ];
                    $result['items'][] = $data;
                }
            }
        }
        return $result;
    }

}
