<?php

namespace Marvelic\PromoLists\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection as CouponCollection;
use Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory as CouponCollectionFactory;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Image as MagentoImage;
use Magento\Framework\Image\Factory as ImageFactory;
use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Marvelic\PromoLists\Api\CategoryRepositoryInterface;
use Marvelic\PromoLists\Api\Data\CategoryInterface;
use Marvelic\PromoLists\Api\Data\PromotionInterface;


class Promotion extends AbstractExtensibleModel implements IdentityInterface, PromotionInterface
{
    const ENTITY    = 'promolist_promotion';
    const CACHE_TAG = 'promolist_promotion';

    /**
     * @var MagentoImage
     */
    protected $_processor;

    /**
     * @var Url
     */
    protected $url;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var ImageFactory
     */
    protected $imageFactory;
    /**
     * @var CouponCollectionFactory
     */
    protected $couponCollectionFactory;


    public function __construct(
        CategoryRepositoryInterface $categoryRepository,
        ProductCollectionFactory $productCollectionFactory,
        CouponCollectionFactory $couponCollectionFactory,
        Config $config,
        Url $url,
        StoreManagerInterface $storeManager,
        ImageFactory $imageFactory,
        Context $context,
        Registry $registry,
        ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory
    ) {
        $this->categoryRepository       = $categoryRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->couponCollectionFactory = $couponCollectionFactory;
        $this->config                   = $config;
        $this->url                      = $url;
        $this->storeManager             = $storeManager;
        $this->imageFactory             = $imageFactory;

        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return [Category::CACHE_TAG, self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($value)
    {
        return $this->setData(self::TYPE, $value);
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
    public function getCouponDescription()
    {
        return $this->getData(self::COUPON_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponDescription($value)
    {
        return $this->setData(self::COUPON_DESCRIPTION, $value);
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

//    /**
//     * {@inheritdoc}
//     */
//    public function getShortContent()
//    {
//        return $this->getData(self::SHORT_CONTENT);
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function setShortContent($value)
//    {
//        return $this->setData(self::SHORT_CONTENT, $value);
//    }

//    /**
//     * {@inheritdoc}
//     */
//    public function getContent()
//    {
//        return $this->getData(self::CONTENT);
//    }
//
//    /**
//     * {@inheritdoc}
//     */
//    public function setContent($value)
//    {
//        return $this->setData(self::CONTENT, $value);
//    }

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
    public function getCouponTitle()
    {
        return $this->getData(self::COUPON_TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponTitle($value)
    {
        return $this->setData(self::COUPON_TITLE, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getCouponCode()
    {
        return $this->getData(self::COUPON_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponCode($value)
    {
        return $this->setData(self::COUPON_CODE, $value);
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
     * {@inheritdoc}
     */
    public function setMetaKeywords($value)
    {
        return $this->setData(self::META_KEYWORDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setCoverImage($value)
    {
        return $this->setData(self::COVER_IMAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCoverImage()
    {
        return $this->getData(self::COVER_IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function getCoverImageUrl()
    {
        return $this->config->getMediaUrl($this->getCoverImage());

    }

    /**
     * {@inheritdoc}
     */
    public function getCoverAlt()
    {
        return $this->getData(self::COVER_ALT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCoverAlt($value)
    {
        return $this->setData(self::COVER_ALT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }
    /**
     * {@inheritdoc}
     */
    public function getPublishedOn()
    {
        return $this->getData(self::PUBLISHED_ON);
    }

    /**
     * {@inheritdoc}
     */
    public function setPublishedOn($value)
    {
        return $this->setData(self::PUBLISHED_ON, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getExpirationOn()
    {
        return $this->getData(self::EXPIRATION_ON);
    }

    /**
     * {@inheritdoc}
     */
    public function setExpirationOn($value)
    {
        return $this->setData(self::EXPIRATION_ON, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setCategoryIds(array $value)
    {
        return $this->setData(self::CATEGORY_IDS, $value);
    }

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
     * @return ResourceModel\Category\Collection
     */
    public function getCategories()
    {
        $ids   = $this->getCategoryIds();
        $ids[] = 0;

        $collection = $this->categoryRepository->getCollection()
            ->addAttributeToSelect(['*'])
            ->addFieldToFilter(CategoryInterface::ID, $ids);

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getCategoryIds()
    {
        return $this->getData(self::CATEGORY_IDS);
    }

//    /**
//     * @param bool $useSid
//     *
//     * @return string
//     */
//    public function getUrl($useSid = true)
//    {
//        return $this->url->getPromotionUrl($this, $useSid);
//    }

    /**
     * @return mixed|ProductCollection
     */
    public function getRelatedProducts()
    {
        $ids = $this->getProductIds();
        $url = ObjectManager::getInstance()
            ->get('Magento\Framework\UrlInterface');
        if (strpos($url->getCurrentUrl(), 'rest/all/V1/promotion') > 0) {
            return $ids;
        }

        $ids[] = 0;

        return $this->productCollectionFactory->create()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductIds(array $value)
    {
        return $this->setData(self::PRODUCT_IDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductIds()
    {
        return $this->getData(self::PRODUCT_IDS);
    }

    /**
     * @return mixed|CouponCollection
     */
    public function getRelatedCoupons()
    {
        $ids = $this->getCouponIds();
        $url = ObjectManager::getInstance()
            ->get('Magento\Framework\UrlInterface');
        if (strpos($url->getCurrentUrl(), 'rest/all/V1/promotion') > 0) {
            return $ids;
        }

        $ids[] = 0;

        return $this->couponCollectionFactory->create()
            ->addFieldToFilter('rule_id', $ids);
    }

    /**
     * {@inheritdoc}
     */
    public function setCouponIds(array $value)
    {
        return $this->setData(self::COUPON_IDS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCouponIds()
    {
        return $this->getData(self::COUPON_IDS);
    }

    public function getPosition()
    {
        return $this->getData(self::ORDER);
    }

    public function setPosition($value)
    {
        return $this->setData(self::ORDER, $value);
    }
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('Marvelic\PromoLists\Model\ResourceModel\Promotion');
    }


}
