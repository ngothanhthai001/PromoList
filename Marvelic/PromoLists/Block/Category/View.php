<?php

namespace Marvelic\PromoLists\Block\Category;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Breadcrumbs;
use Magento\Theme\Block\Html\Title;
use Marvelic\PromoLists\Model\Category;
use Marvelic\PromoLists\Model\Config;
use Marvelic\PromoLists\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Marvelic\PromoLists\Model\ResourceModel\Promotion\CollectionFactory as PromotionCollectionFactory;

class View extends Template implements IdentityInterface
{
    /**
     * @var PromotionCollectionFactory
     */
    protected $promotionCollectionFactory;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param PromotionCollectionFactory     $promotionCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Config                    $config
     * @param Registry                  $registry
     * @param Context                   $context
     * @param array                     $data
     */
    public function __construct(
        PromotionCollectionFactory $promotionCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Config $config,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->promotionCollectionFactory     = $promotionCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->config                    = $config;
        $this->registry                  = $registry;
        $this->context                   = $context;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        if ($this->getCategory()) {
            return $this->getCategory()->getIdentities();
        }

        return [Category::CACHE_TAG];
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $category = $this->getCategory();

        $title = $category ? $category->getName() : $this->config->getMenuTitle();

        $metaTitle = $category
            ? ($category->getMetaTitle() ? $category->getMetaTitle() : $category->getName())
            : 'Promotion Lists';

        $metaDescription = $category
            ? ($category->getMetaDescription() ? $category->getMetaDescription() : $category->getName())
            : $this->config->getBaseMetaDescription();

        $metaKeywords = $category
            ? ($category->getMetaKeywords() ? $category->getMetaKeywords() : $category->getName())
            : '';

        $this->pageConfig->getTitle()->set($metaTitle);
        $this->pageConfig->setDescription($metaDescription);
        $this->pageConfig->setKeywords($metaKeywords);

        /** @var Title $pageMainTitle */
        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($title);
        }

        /** @var Breadcrumbs $breadcrumbs */
        if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Go to Home Page'),
                'link'  => $this->context->getUrlBuilder()->getBaseUrl(),
            ])->addCrumb('marvelic_promolists', [
                'label' => $this->config->getMenuTitle(),
                'title' => $this->config->getMenuTitle(),
                'link'  => $this->config->getBaseUrl(),
            ]);

            if ($category) {
                $ids = $category->getParentIds();

                $ids[]   = 0;
                $parents = $this->categoryCollectionFactory->create()
                    ->addFieldToFilter('entity_id', $ids)
                    ->addNameToSelect()
                    ->excludeRoot()
                    ->setOrder('level', 'asc');

                /** @var Category $cat */
                foreach ($parents as $cat) {
                    $breadcrumbs->addCrumb($cat->getId(), [
                        'label' => $cat->getName(),
                        'title' => $cat->getName(),
                        'link'  => $cat->getUrl(),
                    ]);
                }

                $breadcrumbs->addCrumb($category->getId(), [
                    'label' => $category->getName(),
                    'title' => $category->getName(),
                ]);
            }
        }

        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->registry->registry('current_promolist_category');
    }
}
