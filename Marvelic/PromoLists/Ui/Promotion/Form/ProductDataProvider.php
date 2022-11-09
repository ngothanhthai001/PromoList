<?php

namespace Marvelic\PromoLists\Ui\Promotion\Form;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

class ProductDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();

        return $collection->addAttributeToSelect('status');
    }
}
