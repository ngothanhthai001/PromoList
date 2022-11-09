<?php

namespace Marvelic\PromoLists\Api;

use Marvelic\PromoLists\Api\Data\CategoryInterface;
use Marvelic\PromoLists\Model\ResourceModel\Category\Collection;

interface CategoryRepositoryInterface
{
    /**
     * @return Collection | CategoryInterface[]
     */
    public function getCollection();

    /**
     * @return CategoryInterface
     */
    public function create();

    /**
     * @param CategoryInterface $model
     *
     * @return CategoryInterface
     */
    public function save(CategoryInterface $model);

    /**
     * @param int $id
     *
     * @return CategoryInterface|false
     */
    public function get($id);

    /**
     * @param CategoryInterface $model
     *
     * @return bool
     */
    public function delete(CategoryInterface $model);
}
