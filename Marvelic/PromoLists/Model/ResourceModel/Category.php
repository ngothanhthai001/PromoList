<?php

namespace Marvelic\PromoLists\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\Filter\FilterManager;
use Marvelic\PromoLists\Api\Data\CategoryInterface;
use Marvelic\PromoLists\Model\Config;
use Marvelic\PromoLists\Api\PromotionRepositoryInterface;
use Zend_Db_Expr;

/**
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 */
class Category extends AbstractEntity
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FilterManager
     */
    protected $filter;

    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @param Config        $config
     * @param FilterManager $filter
     * @param PromotionRepositoryInterface $promotionRepository
     * @param Context       $context
     * @param array         $data
     */
    public function __construct(
        Config $config,
        FilterManager $filter,
        PromotionRepositoryInterface $promotionRepository,
        Context $context,
        $data = []
    ) {
        $this->config = $config;
        $this->promotionRepository = $promotionRepository;
        $this->filter = $filter;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Marvelic\PromoLists\Model\Category::ENTITY);
        }

        return parent::getEntityType();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getDefaultAttributes()
    {
        $attributes   = parent::_getDefaultAttributes();
//        $attributes[] = 'sort_order';

        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeSave(DataObject $category)
    {
        /** @var \Marvelic\PromoLists\Model\Category $category */

        parent::_beforeSave($category);

        if (!$category->getChildrenCount()) {
            $category->setChildrenCount(0);
        }
        if (!$category->getData('url_key')) {
            $category->setData('url_key', $this->filter->translitUrl($category->getName()));
        }

        if ($category->isObjectNew()) {
            if (!$category->hasParentId()) {
                $category->setParentId(1);
            }

            /** @var \Marvelic\PromoLists\Model\Category $parent */
            $parent = ObjectManager::getInstance()
                ->create('Marvelic\PromoLists\Model\Category')
                ->load($category->getParentId());

            $category->setPath($parent->getPath());

            if ($category->getPosition() === null) {
                $category->setPosition($this->getMaxPosition($category->getPath()) + 1);
            }

            $path          = explode('/', $category->getPath());
            $level         = count($path) - ($category->getId() ? 1 : 0);
            $toUpdateChild = array_diff($path, [$category->getId()]);

            if (!$category->hasPosition()) {
                $category->setPosition($this->getMaxPosition(implode('/', $toUpdateChild)) + 1);
            }

            if (!$category->hasLevel()) {
                $category->setLevel($level);
            }

            if (!$category->getId() && $category->getPath()) {
                $category->setPath($category->getPath() . '/');
            }

            $this->getConnection()->update(
                $this->getEntityTable(),
                ['children_count' => new Zend_Db_Expr('children_count+1')],
                ['entity_id IN(?)' => $toUpdateChild]
            );
        }

        return $this;
    }

    /**
     * Get maximum position of child categories by specific tree path
     *
     * @param string $path
     *
     * @return int
     */
    protected function getMaxPosition($path)
    {
        $connection    = $this->getConnection();
        $positionField = $connection->quoteIdentifier('position');
        $level         = count(explode('/', $path));
        $bind          = ['c_level' => $level, 'c_path' => $path . '/%'];
        $select        = $connection->select()->from(
            $this->getTable('promolist_category_entity'),
            'MAX(' . $positionField . ')'
        )->where(
            $connection->quoteIdentifier('path') . ' LIKE :c_path'
        )->where($connection->quoteIdentifier('level') . ' = :c_level');

        $position = $connection->fetchOne($select, $bind);
        if (!$position) {
            $position = 0;
        }

        return $position;
    }
    protected function _afterLoad(DataObject $category)
    {
        /** @var CategoryInterface $category */

        $category->setStoreIds($this->getStoreIds($category));

        return parent::_afterLoad($category);
    }
    /**
     * {@inheritdoc}
     */
    protected function _afterSave(DataObject $object)
    {
        /** @var \Marvelic\PromoLists\Model\Category $object */

        if (substr($object->getPath(), -1) == '/' || !$object->getPath()) {
            $object->setPath($object->getPath() . $object->getId());
            $this->savePath($object);
        }
        $this->saveStoreIds($object);
        if ($object->dataHasChangedFor('parent_id')) {
            $newParent = ObjectManager::getInstance()
                ->create('Marvelic\PromoLists\Model\Category')
                ->load($object->getParentId());
            $this->changeParent($object, $newParent);
        }

        return parent::_afterSave($object);
    }

    private function saveStoreIds(CategoryInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('promolist_category_store');

        /**
         * If store ids data is not declared we haven't do manipulations
         */
        if (!$model->getStoreIds()) {
            return $this;
        }

        $storeIds    = $model->getStoreIds();
        $oldStoreIds = $this->getStoreIds($model);

        $insert = array_diff($storeIds, $oldStoreIds);
        $delete = array_diff($oldStoreIds, $storeIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $storeId) {
//                if (empty($storeId)) {
//                    continue;
//                }
                $data[] = [
                    'store_id' => (int)$storeId,
                    'category_id'  => (int)$model->getId(),
                ];
            }
            $model->setStoreIds($data);
            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $storeId) {
                $where = ['category_id = ?' => (int)$model->getId(), 'store_id = ?' => (int)$storeId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }
    /**
     * @param CategoryInterface $model
     *
     * @return array
     */
    private function getStoreIds(CategoryInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('promolist_category_store'),
            'store_id'
        )->where(
            'category_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }
    /**
     * Update path field
     *
     * @param \Marvelic\PromoLists\Model\Category $object
     *
     * @return $this
     */
    protected function savePath($object)
    {
        if ($object->getId()) {
            $this->getConnection()->update(
                $this->getEntityTable(),
                ['path' => $object->getPath()],
                ['entity_id = ?' => $object->getId()]
            );
            $object->unsetData('path_ids');
        }

        return $this;
    }

    /**
     * Move category to another parent node
     *
     * @param \Marvelic\PromoLists\Model\Category $category
     * @param \Marvelic\PromoLists\Model\Category $newParent
     * @param null|int                      $afterCategoryId
     *
     * @return $this
     */
    public function changeParent(
        \Marvelic\PromoLists\Model\Category $category,
        \Marvelic\PromoLists\Model\Category $newParent,
        $afterCategoryId = null
    ) {
        $childrenCount = $this->getChildrenCount($category->getId()) + 1;
        $table         = $this->getEntityTable();
        $connection    = $this->getConnection();
        $levelFiled    = $connection->quoteIdentifier('level');
        $pathField     = $connection->quoteIdentifier('path');

        /**
         * Decrease children count for all old category parent categories
         */
        $connection->update(
            $table,
            ['children_count' => new Zend_Db_Expr('children_count - ' . $childrenCount)],
            ['entity_id IN(?)' => $category->getParentIds()]
        );

        /**
         * Increase children count for new category parents
         */
        $connection->update(
            $table,
            ['children_count' => new Zend_Db_Expr('children_count + ' . $childrenCount)],
            ['entity_id IN(?)' => $newParent->getPathIds()]
        );

        $position = $this->processPositions($category, $newParent, $afterCategoryId);

        $newPath          = sprintf('%s/%s', $newParent->getPath(), $category->getId());
        $newLevel         = $newParent->getLevel() + 1;
        $levelDisposition = $newLevel - $category->getLevel();

        /**
         * Update children nodes path
         */
        $connection->update(
            $table,
            [
                'path'  => new Zend_Db_Expr(
                    'REPLACE(' . $pathField . ',' . $connection->quote(
                        $category->getPath() . '/'
                    ) . ', ' . $connection->quote(
                        $newPath . '/'
                    ) . ')'
                ),
                'level' => new Zend_Db_Expr($levelFiled . ' + ' . $levelDisposition),
            ],
            [$pathField . ' LIKE ?' => $category->getPath() . '/%']
        );

        /**
         * Update moved category data
         */
        $data = [
            'path'      => $newPath,
            'level'     => $newLevel,
            'position'  => $position,
            'parent_id' => $newParent->getId(),
        ];
        $connection->update($table, $data, ['entity_id = ?' => $category->getId()]);

        // Update category object to new data
        $category->addData($data);
        $category->unsetData('path_ids');

        return $this;
    }

    /**
     * Get child categories count
     *
     * @param int $categoryId
     *
     * @return int
     */
    public function getChildrenCount($categoryId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getEntityTable(),
            'children_count'
        )->where(
            'entity_id = :entity_id'
        );
        $bind   = ['entity_id' => $categoryId];

        return $this->getConnection()->fetchOne($select, $bind);
    }

    /**
     * @param \Marvelic\PromoLists\Model\Category $category
     * @param \Marvelic\PromoLists\Model\Category $newParent
     * @param null|int                      $afterCategoryId
     *
     * @return int
     */
    protected function processPositions($category, $newParent, $afterCategoryId)
    {
        $table         = $this->getEntityTable();
        $connection    = $this->getConnection();
        $positionField = $connection->quoteIdentifier('position');

        $bind  = ['position' => new Zend_Db_Expr($positionField . ' - 1')];
        $where = [
            'parent_id = ?'         => $category->getParentId(),
            $positionField . ' > ?' => $category->getPosition(),
        ];
        $connection->update($table, $bind, $where);

        /**
         * Prepare position value
         */
        if ($afterCategoryId) {
            $select   = $connection->select()->from($table, 'position')->where('entity_id = :entity_id');
            $position = $connection->fetchOne($select, ['entity_id' => $afterCategoryId]);
            $position += 1;
        } else {
            $position = 1;
        }

        $bind  = ['position' => new Zend_Db_Expr($positionField . ' + 1')];
        $where = ['parent_id = ?' => $newParent->getId(), $positionField . ' >= ?' => $position];
        $connection->update($table, $bind, $where);

        return $position;
    }

    public function getCountPromotionById(CategoryInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('promolist_category_promotion'),
            'promotion_id'
        )->where(
            'category_id = ?',
            (int)$model->getId()
        );
        $promotionEnable =  $this->promotionRepository->getCollection()->addVisibilityFilter()->getItems();
        return count(array_intersect(array_keys($promotionEnable), $connection->fetchCol($select)));
    }

}
