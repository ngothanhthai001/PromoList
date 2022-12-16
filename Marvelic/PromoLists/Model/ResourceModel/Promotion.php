<?php

namespace Marvelic\PromoLists\Model\ResourceModel;

use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filter\FilterManager;
use Magento\SalesRule\Api\CouponRepositoryInterface;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Model\Config;
use Marvelic\PromoLists\Model\Config\FileProcessor;

class Promotion extends AbstractEntity
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
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @var CouponRepositoryInterface
     */
    protected $couponRepositoryInterface;

    public function __construct(
        Config $config,
        FilterManager $filter,
        FileProcessor $fileProcessor,
        CouponRepositoryInterface $couponRepositoryInterface,
        Context $context,
        $data = []
    ) {
        $this->config     = $config;
        $this->filter     = $filter;
        $this->fileProcessor = $fileProcessor;
        $this->couponRepositoryInterface = $couponRepositoryInterface;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Marvelic\PromoLists\Model\Promotion::ENTITY);
        }

        return parent::getEntityType();
    }

    protected function _afterLoad(DataObject $promotion)
    {
        /** @var PromotionInterface $promotion */

        $promotion->setCategoryIds($this->getCategoryIds($promotion));
        $promotion->setStoreIds($this->getStoreIds($promotion));
        $promotion->setProductIds($this->getProductIds($promotion));
        $promotion->setCouponIds($this->getCouponIds($promotion));
        $promotion->setCouponTitle($this->getCouponTitle($promotion));
        $promotion->setCouponDescription($this->getCouponDescription($promotion));
        return parent::_afterLoad($promotion);
    }

    /**
     * @param PromotionInterface $model
     *
     * @return array
     */
    private function getCouponDescription(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('promolist_promotion_rule'),
            'coupon_description'
        )->where(
            'promotion_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PromotionInterface $model
     *
     * @return array
     */
    private function getCouponTitle(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('promolist_promotion_rule'),
            'coupon_title'
        )->where(
            'promotion_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PromotionInterface $model
     *
     * @return array
     */
    private function getCategoryIds(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('promolist_category_promotion'),
            'category_id'
        )->where(
            'promotion_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PromotionInterface $model
     *
     * @return array
     */
    private function getStoreIds(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('promolist_promotion_store'),
            'store_id'
        )->where(
            'promotion_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PromotionInterface $model
     *
     * @return array
     */
    private function getProductIds(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('promolist_promotion_product'),
            'product_id'
        )->where(
            'promotion_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }

    /**
     * @param PromotionInterface $model
     *
     * @return array
     */
    private function getCouponIds(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $select = $connection->select()->from(
            $this->getTable('promolist_promotion_rule'),
            'coupon_id'
        )->where(
            'promotion_id = ?',
            (int)$model->getId()
        );

        return $connection->fetchCol($select);
    }
    /**
     * {@inheritdoc}
     */
    protected function _afterSave(DataObject $promotion)
    {
        /** @var PromotionInterface $promotion */
        $this->saveCategoryIds($promotion);
        $this->saveStoreIds($promotion);
        $this->saveProductIds($promotion);
        $this->saveCouponIds($promotion);
        return parent::_afterSave($promotion);
    }

//    /**
//     * @param PromotionInterface $model
//     *
//     * @return $this
//     * @throws LocalizedException
//     */
//    private function saveCoverImage(PromotionInterface $model)
//    {
//        /** @var PromotionInterface $model */
//        $connection = $this->getConnection();
//        $table = $this->getTable('promolist_promotion_entity_gallery');
//        $attributeId = $this->getAttribute(\Marvelic\PromoLists\Model\Promotion::COVER_IMAGE)->getAttributeId();
//        if ($model->getCoverImage() == 'delete') {
//            $fileName = $this->getCoverImage($model);
//            $this->fileProcessor->deleteFile(reset($fileName));
//            $where = ['entity_id = ?' => (int)$model->getId()];
//            $connection->delete($table, $where);
//        } elseif ($_FILES[key($_FILES)]['name']) {
//            $fileName =  $this->fileProcessor->save(key($_FILES));
//            $model->setCoverImage($fileName['name']);
//            $categoryIds   = $model->getCoverImage();
//            $data[] = [
//                'attribute_id' => $attributeId,
//                'store_id'     => 0,
//                'entity_id'   => (int)$model->getId(),
//                'position'     => 0,
//                'value'        => $categoryIds
//            ];
//            $connection->insertMultiple($table, $data);
//        }
//        return $this;
//    }

    /**
     * @param PromotionInterface $model
     *
     * @return $this
     */
    private function saveCategoryIds(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('promolist_category_promotion');

        if (!$model->getCategoryIds()) {
            return $this;
        }

        $categoryIds    = $model->getCategoryIds();
        $oldCategoryIds = $this->getCategoryIds($model);

        $insert = array_diff($categoryIds, $oldCategoryIds);
        $delete = array_diff($oldCategoryIds, $categoryIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $categoryId) {
                if (empty($categoryId)) {
                    continue;
                }

                $data[] = [
                    'category_id' => (int)$categoryId,
                    'promotion_id'     => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $categoryId) {
                $where = ['promotion_id = ?' => (int)$model->getId(), 'category_id = ?' => (int)$categoryId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }

    /**
     * @param PromotionInterface $model
     *
     * @return $this
     */
    private function saveStoreIds(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('promolist_promotion_store');

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
                    'promotion_id'  => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $storeId) {
                $where = ['promotion_id = ?' => (int)$model->getId(), 'store_id = ?' => (int)$storeId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }

    /**
     * @param PromotionInterface $model
     *
     * @return $this
     */
    private function saveProductIds(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('promolist_promotion_product');

        if (!$model->getProductIds()) {
            return $this;
        }

        $productIds    = $model->getProductIds();
        $oldProductIds = $this->getProductIds($model);

        $insert = array_diff($productIds, $oldProductIds);
        $delete = array_diff($oldProductIds, $productIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $productId) {
                if (empty($productId)) {
                    continue;
                }
                $data[] = [
                    'product_id' => (int)$productId,
                    'promotion_id'    => (int)$model->getId(),
                ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $productId) {
                $where = ['promotion_id = ?' => (int)$model->getId(), 'product_id = ?' => (int)$productId];
                $connection->delete($table, $where);
            }
        }

        return $this;
    }

    /**
     * @param PromotionInterface $model
     *
     * @return $this
     */
    private function saveCouponIds(PromotionInterface $model)
    {
        $connection = $this->getConnection();

        $table = $this->getTable('promolist_promotion_rule');

        if (!$model->getCouponIds()) {
            return $this;
        }
        $ruleIds      = [];
        $couponIds    = $model->getCouponIds();
        $oldCouponIds = $this->getCouponIds($model);

        $insert = array_diff($couponIds, $oldCouponIds);
        $delete = array_diff($oldCouponIds, $couponIds);
        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $couponId) {
                if (empty($couponId)) {
                    continue;
                }
                $data[] = [
                        'rule_id' => (int)$this->couponRepositoryInterface->getById($couponId)->getRuleId(),
                        'coupon_id' => (int)$couponId,
                        'promotion_id'    => (int)$model->getId(),
                    ];
            }

            if ($data) {
                $connection->insertMultiple($table, $data);
            }
        }
        $isUpdateCoupon = false;
        if (!empty($delete)) {
            foreach ($delete as $couponId) {
                $where = ['promotion_id = ?' => (int)$model->getId(), 'coupon_id = ?' => (int)$couponId];
                $connection->delete($table, $where);
                $isUpdateCoupon = true;
            }
        }
        if (!$isUpdateCoupon) {
            if ($model->getCouponDescription()) {
                for ($i = 0; $i < count($model->getCouponDescription()); $i++) {
                    $where = ['promotion_id = ?' => (int)$model->getId(), 'coupon_id = ?' => (int)$model->getCouponIds()[$i]];
                    $data = [
                            'coupon_title' => (!empty($model->getCouponTitle()[$i]['coupon_title'])) ? $model->getCouponTitle()[$i]['coupon_title'] : "",
                            'coupon_description' => (!empty($model->getCouponDescription()[$i]['coupon_description'])) ? $model->getCouponDescription()[$i]['coupon_description'] : "",
                        ];
                    $connection->update($table, $data, $where);
                }
            }
        }

        return $this;
    }


}
