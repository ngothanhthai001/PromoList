<?php

namespace Marvelic\PromoLists\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;

class Config
{
    const MEDIA_FOLDER = 'promo_lists';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var MagentoUrlInterface
     */
    protected $urlManager;

    /**
     * @param ScopeConfigInterface  $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param Filesystem            $filesystem
     * @param MagentoUrlInterface   $urlManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        MagentoUrlInterface $urlManager
    ) {
        $this->scopeConfig  = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->filesystem   = $filesystem;
        $this->urlManager   = $urlManager;
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getMenuTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'promo_lists/appearance/menu_title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return bool
     */
    public function isDisplayInMenu()
    {
        return $this->scopeConfig->getValue(
            'promo_lists/appearance/main_menu',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getBaseMetaDescription($store = null)
    {
        return $this->scopeConfig->getValue(
            'promo_lists/seo/base_meta_description',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlManager->getUrl($this->getBaseRoute());
    }

    /**
     * @return string
     */
    public function getBaseRoute()
    {
        return $this->scopeConfig->getValue('promo_lists/seo/base_route');
    }

    /**
     * @param null|string $store
     *
     * @return string
     */
    public function getUrlSuffix($store = null)
    {
        return $this->scopeConfig->getValue(
            'promo_lists/seo/url_suffix',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @return string
     */
    public function getDefaultSortField()
    {
        return 'created_at';
    }

    /**
     * @return string
     */
    public function getMediaPath($image)
    {
        $path = $this->filesystem
                ->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath() . self::MEDIA_FOLDER;

        if (!file_exists($path) || !is_dir($path)) {
            $this->filesystem
                ->getDirectoryWrite(DirectoryList::MEDIA)
                ->create($path);
        }

        return $path . '/' . $image;
    }

    /**
     * @param string $image
     *
     * @return string
     */
    public function getMediaUrl($image)
    {
        if (!$image) {
            return false;
        }

        $url = $this->storeManager->getStore()
                ->getBaseUrl(MagentoUrlInterface::URL_TYPE_MEDIA) . self::MEDIA_FOLDER;

        $url .= '/' . $image;

        return $url;
    }
    public function getAttributesFilter()
    {
        return $this->scopeConfig->getValue('promo_lists/attributes_filter/apply_to');
    }

}
