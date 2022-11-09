<?php

namespace Marvelic\PromoLists\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface as MagentoUrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Marvelic\PromoLists\Api\Data\CategoryInterface;
use Marvelic\PromoLists\Api\Data\PromotionInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Url
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var PromotionFactory
     */
    protected $promotionFactory;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @var MagentoUrlInterface
     */
    protected $urlManager;

    /**
     * @var Config
     */
    protected $config;

    public function __construct(
        StoreManagerInterface $storeManager,
        Config $config,
        ScopeConfigInterface $scopeConfig,
        PromotionFactory $promotionFactory,
        CategoryFactory $categoryFactory,
        MagentoUrlInterface $urlManager
    ) {
        $this->storeManager    = $storeManager;
        $this->config          = $config;
        $this->scopeConfig     = $scopeConfig;
        $this->promotionFactory     = $promotionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->urlManager      = $urlManager;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->urlManager->getUrl($this->config->getBaseRoute());
    }

    /**
     * @param Promotion $promotion
     * @param bool $useSid
     *
     * @return string
     */
    public function getPromotionUrl($promotion, $useSid = true)
    {
        return $this->getUrl('/' . $promotion->getUrlKey(), 'promotion', ['_nosid' => !$useSid]);
    }

    /**
     * @param string $route
     * @param string $type
     * @param array  $urlParams
     *
     * @return string
     */
    protected function getUrl($route, $type, $urlParams = [])
    {
        $url = $this->urlManager->getUrl($this->config->getBaseRoute() . $route, $urlParams);

        if ($this->config->getUrlSuffix()) {
            if ($type == 'category' || $type == 'promotion') {
                $url = $this->addSuffix($url, $this->config->getUrlSuffix());
            }
        }

        return $url;
    }

    /**
     * @param string $url
     * @param string $suffix
     *
     * @return string
     */
    private function addSuffix($url, $suffix)
    {
        $parts    = explode('?', $url, 2);
        $parts[0] = rtrim($parts[0], '/') . $suffix;

        return implode('?', $parts);
    }

    /**
     * @param Category $category
     * @param array    $urlParams
     *
     * @return string
     */
    public function getCategoryUrl($category, $urlParams = [])
    {
        return $this->getUrl('/' . $category->getUrlKey(), 'category', $urlParams);
    }


    /**
     * @param array $urlParams
     *
     * @return string
     */
    public function getSearchUrl($urlParams = [])
    {
        return $this->getUrl('/search/', 'search', $urlParams);
    }

    /**
     * @param string $pathInfo
     *
     * @return bool|DataObject
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function match($pathInfo)
    {
        $identifier = trim($pathInfo, '/');
        $parts      = explode('/', $identifier);

        if (count($parts) >= 1) {
            $parts[count($parts) - 1] = $this->trimSuffix($parts[count($parts) - 1]);
        }

        if ($parts[0] != $this->config->getBaseRoute()) {
            return false;
        }

        if (count($parts) > 1) {
            unset($parts[0]);
            $parts  = array_values($parts);
            $urlKey = implode('/', $parts);
            $urlKey = urldecode($urlKey);
            $urlKey = $this->trimSuffix($urlKey);
        } else {
            $urlKey = '';
        }

        if ($urlKey == '') {
            return new DataObject([
                'module_name'     => 'marvelic_promolists',
                'controller_name' => 'category',
                'action_name'     => 'index',
                'params'          => [],
            ]);
        }

        if ($parts[0] == 'search') {
            return new DataObject([
                'module_name'     => 'marvelic_promolists',
                'controller_name' => 'search',
                'action_name'     => 'result',
                'params'          => [],
            ]);
        }

        $promotion = $this->promotionFactory->create()->getCollection()
            ->addAttributeToFilter('url_key', $urlKey)
            ->addStoreFilter($this->storeManager->getStore()->getId())
            ->getFirstItem();

        if ($promotion->getId()) {
            return new DataObject([
                'module_name'     => 'marvelic_promolists',
                'controller_name' => 'promotion',
                'action_name'     => 'view',
                'params'          => [PromotionInterface::ID => $promotion->getId()],
            ]);
        }

        $category = $this->categoryFactory->create()->getCollection()
            ->addAttributeToFilter('url_key', $urlKey)
            ->getFirstItem();

        if ($category->getId()) {
            return new DataObject([
                'module_name'     => 'marvelic_promolists',
                'controller_name' => 'category',
                'action_name'     => 'view',
                'params'          => [CategoryInterface::ID => $category->getId()],
            ]);
        }

        return false;
    }

    /**
     * Return url without suffix
     *
     * @param string $key
     *
     * @return string
     */
    protected function trimSuffix($key)
    {
        $suffix = $this->config->getUrlSuffix();
        //user can enter .html or html suffix
        if ($suffix != '' && $suffix[0] != '.') {
            $suffix = '.' . $suffix;
        }

        $key = str_replace($suffix, '', $key);

        return $key;
    }
}
