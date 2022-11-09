<?php

namespace Marvelic\PromoLists\Model\OptionProvider\Provider;

/**
 * Provider
 */
class RulesOptionProvider implements \Magento\Framework\Data\OptionSourceInterface
{
    const PAGE_SIZE = 7500;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->logger = $logger;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $rules = [
            [
                'value' => '0',
                'label' => ' '
            ]
        ];
        $pageNum = 1;

        /** @var \Magento\SalesRule\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('coupon_type', 2)
            ->setPageSize(self::PAGE_SIZE)
            ->setCurPage($pageNum);

        while ($pageNum <= $collection->getLastPageNumber()) {
            foreach ($collection->getData() as $rule) {
                $rules[] = [
                    'value' => $rule['rule_id'],
                    'label' => $rule['name']." ( coupon_code : ".$rule['code'].") "
                ];
            }
            $collection->setCurPage(++$pageNum)->resetData();
        }

        return $rules;
    }
}
