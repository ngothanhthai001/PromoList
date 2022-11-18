<?php

namespace Marvelic\PromoLists\Ui\Promotion\Form;

use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory;

class CouponDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    protected $collection;

    public function __construct(CollectionFactory $collection, $name, $primaryFieldName, $requestFieldName, array $meta = [], array $data = [])
    {
        $this->collection = $collection->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

}
