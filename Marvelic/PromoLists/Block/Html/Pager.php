<?php

namespace Marvelic\PromoLists\Block\Html;

use Magento\Framework\App\ObjectManager;
use Marvelic\PromoLists\Model\UrlInterface;

/**
 * Html pager block
 * @SuppressWarnings(PHPMD.ExcessivePublicCount)
 */
class Pager extends \Magento\Theme\Block\Html\Pager
{
    /**
     * @var UrlInterface
     */
    protected $entity;

    /**
     * Retrieve page URL by defined parameters
     *
     * @param array $params
     *
     * @return string
     */
    public function getPagerUrl($params = [])
    {
        $urlParams             = [];
        $urlParams['_current'] = false;
        $urlParams['_escape']  = true;
        $urlParams['_query']   = ($params);

        $path = $this->getUrl($this->getPath(), $urlParams);
        if ($this->getEntity()) {
            $path = $this->getEntity()->getUrl($urlParams);
        } elseif ($this->getRequest()->getControllerName() == 'search') {
            $url                   = ObjectManager::getInstance()->get('Marvelic\PromoLists\Model\Url');
            $urlParams['_current'] = false;
            $path                  = $url->getSearchUrl($urlParams);
        }

        return $path;
    }

    /**
     * @return string
     */
    protected function getPath()
    {
        $config = ObjectManager::getInstance()->get('Marvelic\PromoLists\Model\Config');

        return $config->getBaseRoute();
    }

    /**
     * @return UrlInterface|null
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param UrlInterface $entity
     *
     * @return $this
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }
    public function getUrlNotCurrent(array $params = [])
    {
        $urlParams             = [];
        $urlParams['_current'] = true;
        $urlParams['_escape']  = true;
        $urlParams['_query']   = ($params);

        $path = $this->getUrl($this->getPath(), $urlParams);
        if ($this->getEntity()) {
            $urlParams['_current'] = false;
            $attribute = $this->getAttributeParamsFilterPager();
            if (!empty($attribute)) {
                $urlParams['_query']   = (array_merge($attribute, $params));
            }
            $path = $this->getEntity()->getUrl($urlParams);
        } elseif ($this->getRequest()->getControllerName() == 'search') {
            $url                   = ObjectManager::getInstance()->get('Marvelic\PromoLists\Model\Url');
            $urlParams['_current'] = true;
            $path                  = $url->getSearchUrl($urlParams);
        }

        return $path;
    }
    public function getNextPageUrl()
    {
        $page = $this->getCollection()->getCurPage(+ 1);
        return $this->getUrlNotCurrent(
            [
                $this->getPageVarName() => $page,
            ]
        );
    }
    public function getPreviousPageUrl()
    {
        $page = $this->getCollection()->getCurPage(- 1);
        return $this->getUrlNotCurrent(
            [
                $this->getPageVarName() => ($page != 1) ? $page : null,
            ]
        );
    }
    public function getPageUrl($page)
    {
        return $this->getUrlNotCurrent(
            [
                $this->getPageVarName() => $page > 1 ? $page : null,
            ]
        );
    }
    public function getAttributeParamsFilterPager()
    {
        $attribute = [];
        $param = $this->getRequest()->getParams();
        foreach ($param as $key => $value) {
            if (str_contains($key, "attr_")) {
                $attribute[$key] =  $value;
            }
        }
        return $attribute;
    }
    public function trimSuffixAttr(array $attributes, $suffix = "attr_")
    {
        $convert = [];
        foreach ($attributes as $key => $attribute) {
            if (str_contains($key, $suffix)) {
                $convert[str_replace("attr_", "", $key)] = $attribute;
            }
        }
        return $convert;
    }
}
