<?php

namespace Marvelic\PromoLists\Api\Data;

interface CategoryInterface
{
    const ID = 'entity_id';

    const URL_KEY   = 'url_key';
    const PATH      = 'path';
    const LEVEL     = 'level';
    const POSITION  = 'position';
    const PARENT_ID = 'parent_id';

    const NAME             = 'name';
    const CONTENT          = 'content';
    const META_TITLE       = 'meta_title';
    const META_DESCRIPTION = 'meta_description';
    const META_KEYWORDS    = 'meta_keywords';
    const STATUS = 'status';
    const STORE_IDS    = 'store_ids';

    const CURRENT_PROMOTION_CATEGORY = "current_promolist_category";

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
     * @return int
     */
    public function getId();

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
    public function getContent();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setContent($value);

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
     * @return string
     */
    public function getMetaDescription();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaDescription($value);

    /**
     * @return string
     */
    public function getMetaKeywords();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setMetaKeywords(string $value): CategoryInterface;

    /**
     * @return string
     */
    public function getPath(): string;

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPath(string $value): CategoryInterface;

    /**
     * @return int
     */
    public function getLevel(): int;

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setLevel($value): CategoryInterface;

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
