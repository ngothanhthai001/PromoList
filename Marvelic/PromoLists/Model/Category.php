<?php

namespace Marvelic\PromoLists\Model;

use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Marvelic\PromoLists\Api\Data\CategoryInterface;

/**
 * Class AdminPermissions
 * @package Mageplaza\AdminPermissions\Model
 * @method int getChildrenCount()
 * @method $this setChildrenCount($count)
 * @method int getParentId()
 * @method $this setParentId($id)
 * @method bool hasParentId()
 */
class Category extends AbstractExtensibleModel implements IdentityInterface, CategoryInterface
{
    const ENTITY = 'promolist_category';

    const CACHE_TAG = 'promolist_category';

    /**
     * {@inheritdoc}
     */
    public function getStoreIds()
    {
        return $this->getData(self::STORE_IDS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreIds(array $value)
    {
        return $this->setData(self::STORE_IDS, $value);
    }

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Url
     */
    protected $url;

    public function __construct(
        Url $url,
        StoreManagerInterface $storeManager,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory
    ) {
        $this->url          = $url;
        $this->storeManager = $storeManager;

        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory);
    }

    /**
    * {@inheritdoc}
    */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
    * {@inheritdoc}
    */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG, self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel(): int
    {
        return $this->getData(self::LEVEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setLevel($value): CategoryInterface
    {
        return $this->setData(self::LEVEL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($value)
    {
        return $this->setData(self::POSITION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlKey()
    {
        return $this->getData(self::URL_KEY);
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlKey($value)
    {
        return $this->setData(self::URL_KEY, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getContent()
    {
        return $this->getData(self::CONTENT);
    }

    /**
     * {@inheritdoc}
     */
    public function setContent($value)
    {
        return $this->setData(self::CONTENT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaTitle()
    {
        return $this->getData(self::META_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaTitle($value)
    {
        return $this->setData(self::META_TITLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaDescription()
    {
        return $this->getData(self::META_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setMetaDescription($value)
    {
        return $this->setData(self::META_DESCRIPTION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMetaKeywords()
    {
        return $this->getData(self::META_KEYWORDS);
    }

    /**
     * @param string $value
     * @return CategoryInterface
     */
    public function setMetaKeywords(string $value): CategoryInterface
    {
        return $this->setData(self::META_KEYWORDS, $value);
    }

    /**
    * Get all parent categories ids
    * @return array
    */
    public function getParentIds()
    {
        return array_diff($this->getPathIds(), [$this->getId()]);
    }

    /**
     * @return array
     */
    public function getPathIds()
    {
        $ids = $this->getData('path_ids');
        if ($ids === null) {
            $ids = explode('/', $this->getPath());
            $this->setData('path_ids', $ids);
        }

        return $ids;
    }

    /**
     * {@inheritdoc}
     */
    public function getPath(): string
    {
        return $this->getData(self::PATH);
    }

    /**
    * {@inheritdoc}
    */
    public function setPath(string $value): CategoryInterface
    {
        return $this->setData(self::PATH, $value);
    }

    /**
     * @param array $urlParams
     *
     * @return string
     */
    public function getUrl($urlParams = []): string
    {
        return $this->url->getCategoryUrl($this, $urlParams);
    }
    /**
     * @param Category $category
     * @param array    $urlParams
     *
     * @return string
     */
    public function getCategoryUrl($category, $urlParams = []): string
    {
        return $this->getUrl('/' . $category->getUrlKey(), 'category', $urlParams);
    }
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Marvelic\PromoLists\Model\ResourceModel\Category');
    }
}
