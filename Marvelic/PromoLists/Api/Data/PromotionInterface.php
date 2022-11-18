<?php

namespace Marvelic\PromoLists\Api\Data;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as CouponCollection;

interface PromotionInterface
{
    const ID = 'entity_id';

    const NAME             = 'name';
    const TYPE             = 'type';
    const STATUS           = 'status';
    const SHORT_CONTENT    = 'short_content';
    const CONTENT          = 'content';
    const URL_KEY          = 'url_key';
    const META_TITLE       = 'meta_title';
    const META_DESCRIPTION = 'meta_description';

    const META_KEYWORDS    = 'meta_keywords';
    const COVER_IMAGE      = 'cover_image';
    const COVER_ALT     = 'cover_alt';

    const CREATED_AT = 'created_at';

    const CATEGORY_IDS = 'category_ids';
    const STORE_IDS    = 'store_ids';
    const PRODUCT_IDS  = 'product_ids';
    const COUPON_IDS  = 'coupon_ids';

    const TYPE_PROMOTION     = 'promotion';
    const TYPE_REVISION = 'revision';
    const COUPON_DESCRIPTION = 'coupon_description';
    const COUPON_TITLE = 'coupon_title';
    const COUPON_CODE = 'coupon_code';

    const PUBLISHED_ON = 'published_on';
    const EXPIRATION_ON = 'expiration_on';
    const ORDER = 'position';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setType($value);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStatus($value);


    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setName($value);


    /**
     * @return string
     */
    public function getUrlKey();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUrlKey($value);

    /**
     * @return string
     */
    public function getMetaTitle();


    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaTitle($value);

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCouponTitle($value);

    /**
     * @return string
     */
    public function getCouponTitle();
    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCouponCode($value);

    /**
     * @return string
     */
    public function getCouponCode();

    /**
     * @return string
     */
    public function getMetaDescription();
    /**
     * @return string
     */
    public function getMetaKeywords();

    /**
     * @return string
     */
    public function getCouponDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaDescription($value);
    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaKeywords($value);
    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCouponDescription($value);

    /**
     * @return string
     */
    public function getCoverImage();

    /**
     * @return string
     */
    public function getCoverImageUrl();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCoverImage($value);

    /**
     * @return string
     */
    public function getCoverAlt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCoverAlt($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getPublishedOn();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPublishedOn($value);
    /**
     * @return string
     */
    public function getExpirationOn();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setExpirationOn($value);

    /**
     * @return mixed
     */
    public function getCategoryIds();

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setCategoryIds(array $value);

    /**
     * @return mixed
     */
    public function getStoreIds();

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setStoreIds(array $value);

    /**
     * @return mixed
     */
    public function getProductIds();

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setProductIds(array $value);

    /**
     * @return mixed|ProductCollection
     */
    public function getRelatedProducts();

    /**
     * @return mixed
     */
    public function getCouponIds();

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setCouponIds(array $value);

    /**
     * @return mixed|CouponCollection
     */
    public function getRelatedCoupons();

    /**
     * @return int
     */
    public function getPosition();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setPosition($value);

}
