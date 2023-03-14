<?php

namespace Marvelic\PromoLists\Model\ResourceModel\Category;

use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\App\ObjectManager;
use Marvelic\PromoLists\Model\Category;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\CollectionFactory as PromotionFactory;

class Collection extends AbstractCollection
{
    protected PromotionFactory $promotionFactory;
    /**
     * @param PromotionFactory $promotionFactory
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Framework\DB\Adapter\AdapterInterface|null $connection
     */
    public function __construct(
        PromotionFactory $promotionFactory,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->promotionFactory = $promotionFactory;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $connection
        );
    }

    /**
     * @var bool
     */
    protected $fromRoot = true;

    /**
     * @return $this
     */
    public function addNameToSelect()
    {
        return $this->addAttributeToSelect(['name']);
    }

    /**
     * @return $this
     */
    public function addVisibilityFilter()
    {
        $this->addAttributeToFilter('status', 1);

        return $this;
    }

    /**
     * @return $this
     */
    public function addRootFilter()
    {
        $this->addFieldToFilter('parent_id', 0);

        return $this;
    }

    /**
     * @return $this
     */
    public function excludeRoot()
    {
        $this->fromRoot = false;

        return $this->addFieldToFilter('entity_id', ['neq' => $this->getRootId()]);
    }

    /**
     * @return int
     */
    public function getRootId()
    {
        $objectManager = ObjectManager::getInstance();
        /** @var \Marvelic\PromoLists\Helper\Category $helper */
        $helper = $objectManager->get('\Marvelic\PromoLists\Helper\Category');

        return $helper->getRootCategory()->getId();
    }

    /**
     * @param int|null $parentId
     *
     * @return Category[]
     */
    public function getTree($parentId = null)
    {
        $list = [];

        if ($parentId == null) {
            $parentId = $this->fromRoot ? 0 : $this->getRootId();
        }

        $collection = clone $this;
        $collection->addFieldToFilter('parent_id', $parentId)
            ->setOrder('position', 'asc');

        foreach ($collection as $item) {
            $list[$item->getId()] = $item;
            if ($item->getChildrenCount()) {
                $items = $this->getTree($item->getId());
                foreach ($items as $child) {
                    $list[$child->getId()] = $child;
                }
            }
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Marvelic\PromoLists\Model\Category', 'Marvelic\PromoLists\Model\ResourceModel\Category');
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->getSelect()->order('e.path asc');
        return $this;
    }
}
