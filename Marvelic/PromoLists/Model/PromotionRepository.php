<?php

namespace Marvelic\PromoLists\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Filter\FilterManager;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Api\Data\PromotionInterfaceFactory;
use Marvelic\PromoLists\Api\CategoryRepositoryInterface;
use Marvelic\PromoLists\Api\PromotionRepositoryInterface;
use Marvelic\PromoLists\Model\Promotion;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\Collection;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\CollectionFactory;

class PromotionRepository implements PromotionRepositoryInterface
{
    private $factory;

    private $collectionFactory;

    private $catRepository;

    private $filterManager;

    public function __construct(
        PromotionInterfaceFactory $factory,
        CollectionFactory $collectionFactory,
        CategoryRepositoryInterface $catRepository,
        FilterManager $filterManager
    ) {
        $this->factory           = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->catRepository     = $catRepository;
        $this->filterManager     = $filterManager;
    }

    /**
     * @inheritdoc
     */
    public function getList()
    {
        /** @var Collection $collection */
        $collection = $this->getCollection();

        return $collection->getItems();
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        /** @var Promotion $promotion */
        $promotion = $this->create();

        $promotion->getResource()->load($promotion, $id);

        if ($promotion->getId()) {
            return $promotion;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, PromotionInterface $promotion)
    {
        /** @var Promotion $model */
        $model = $this->create();
        $model->getResource()->load($model, $id);

        if (!$model->getId()) {
            throw new InputException(__("The promotion doesn't exist."));
        }

        $json = json_decode(file_get_contents("php://input"));

        foreach ($json->promotion as $k => $v) {
            $model->setData($k, $promotion->getData($k));
        }

        $model->getResource()->save($model);

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function apiDelete($id)
    {
        /** @var Promotion $promotion */
        $promotion = $this->create();
        $promotion->getResource()->load($promotion, $id);

        if (!$promotion->getId()) {
            throw new InputException(__("The promotion doesn't exist."));
        }

        $promotion->getResource()->delete($promotion);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PromotionInterface $model)
    {
        /** @var Promotion $model */
        return $model->getResource()->delete($model);
    }

    /**
     * {@inheritdoc}
     */
    public function save(PromotionInterface $model)
    {
        if (!$model->getType()) {
            $model->setType(PromotionInterface::TYPE_PROMOTION);
        }

        if (!$model->getUrlKey()) {
            $model->setUrlKey($this->filterManager->translitUrl($model->getName()));
        }

        if ($model->getCategoryIds()) {
            $categoryIds = array_filter($model->getCategoryIds());
            $model->setCategoryIds($categoryIds);
        }

        /** @var Promotion $model */
        $model->getResource()->save($model);

        return $model;
    }
}
