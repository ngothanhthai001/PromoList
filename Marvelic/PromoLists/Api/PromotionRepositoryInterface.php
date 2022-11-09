<?php


namespace Marvelic\PromoLists\Api;

use Magento\Framework\Exception\StateException;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\Collection;

interface PromotionRepositoryInterface
{
    /**
     * @return Collection | PromotionInterface[]
     */
    public function getCollection();

    /**
     * @return PromotionInterface
     */
    public function create();

    /**
     * @param PromotionInterface $model
     *
     * @return PromotionInterface
     */
    public function save(PromotionInterface $model);

    /**
     * @return PromotionInterface[]
     */
    public function getList();

    /**
     * @param int $id
     *
     * @return PromotionInterface|false
     */
    public function get($id);

    /**
     * @param int $id
     *
     * @return bool
     * @throws StateException
     */
    public function apiDelete($id);

    /**
     * @param int $id
     * @param PromotionInterface $promotion
     *
     * @return PromotionInterface
     * @throws StateException
     */
    public function update($id, PromotionInterface $promotion);

    /**
     * @param PromotionInterface $model
     *
     * @return bool
     */
    public function delete(PromotionInterface $model);
}
