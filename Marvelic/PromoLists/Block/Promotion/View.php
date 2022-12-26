<?php

namespace Marvelic\PromoLists\Block\Promotion;

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template\Context;
use Magento\Theme\Block\Html\Breadcrumbs;
use Magento\Theme\Block\Html\Title;
use Marvelic\PromoLists\Api\PromotionRepositoryInterface;
use Marvelic\PromoLists\Model\Config;
use Marvelic\PromoLists\Model\Promotion;

use Marvelic\PromoLists\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

use Marvelic\PromoLists\Model\ResourceModel\Promotion\Collection;

/**
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class View extends AbstractBlock implements IdentityInterface
{
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;
    /**
     * @var string
     */
    protected $defaultToolbarBlock = 'Marvelic\PromoLists\Block\Promotion\PromotionList\Toolbar';
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @param CategoryCollectionFactory $promotionCollectionFactory
     * @param Config                    $config
     * @param FilterProvider            $filterProvider
     * @param Registry                  $registry
     * @param Context                   $context
     */
    public function __construct(
        CategoryCollectionFactory $promotionCollectionFactory,
        PromotionRepositoryInterface $promotionRepository,
        Config $config,
        FilterProvider $filterProvider,
        Registry $registry,
        Context $context
    ) {
        $this->categoryCollectionFactory = $promotionCollectionFactory;
        $this->promotionRepository = $promotionRepository;
        $this->config                    = $config;
        $this->filterProvider            = $filterProvider;

        parent::__construct($config, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        return $this->getPromotion()->getIdentities();
    }

    /**
     * @return string
     */
    public function getPromotionContent()
    {
        return $this->filterProvider->getPageFilter()->filter(
            $this->getPromotion()->getShortContent() . $this->getPromotion()->getContent()
        );
    }

    /**
     * {@inheritdoc}
     */
    // protected function _prepareLayout()
    // {
    //     parent::_prepareLayout();

    //     $promotion = $this->getPromotion();

    //     $title = $promotion ? $promotion->getName() : $this->config->getBlogName();

    //     $metaTitle = $promotion
    //         ? ($promotion->getMetaTitle() ? $promotion->getMetaTitle() : $promotion->getName())
    //         : $this->config->getBaseMetaTitle();

    //     $metaDescription = $promotion
    //         ? ($promotion->getMetaDescription() ? $promotion->getMetaDescription() : $promotion->getName())
    //         : $this->config->getBaseMetaDescription();

    //     $metaKeywords = $promotion
    //         ? ($promotion->getMetaKeywords() ? $promotion->getMetaKeywords() : $promotion->getName())
    //         : $this->config->getBaseMetaKeywords();

    //     $this->pageConfig->getTitle()->set($metaTitle);
    //     $this->pageConfig->setDescription($metaDescription);
    //     $this->pageConfig->setKeywords($metaKeywords);

    //     /** @var Title $pageMainTitle */
    //     $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
    //     if ($pageMainTitle) {
    //         $pageMainTitle->setPageTitle($title);
    //     }

    //     /** @var Breadcrumbs $breadcrumbs */
    //     if ($breadcrumbs = $this->getLayout()->getBlock('breadcrumbs')) {
    //         $breadcrumbs->addCrumb('home', [
    //             'label' => __('Home'),
    //             'title' => __('Go to Home Page'),
    //             'link'  => $this->context->getUrlBuilder()->getBaseUrl(),
    //         ])->addCrumb('blog', [
    //             'label' => $this->config->getBlogName(),
    //             'title' => $this->config->getBlogName(),
    //             'link'  => $this->config->getBaseUrl(),
    //         ]);

    //         $breadcrumbs->addCrumb('postname', [
    //             'label' => $title,
    //             'title' => $title,
    //         ]);
    //     }
    // }

    public function sortFunction($a, $b)
    {
        return strtotime($a["date"]) - strtotime($b["date"]);
    }
    public function getToolbarBlock()
    {
        $blockName = $this->getToolbarBlockName();

        if ($blockName) {
            $block = $this->getLayout()->getBlock($blockName);
            if ($block) {
                return $block;
            }
        }

        $block = $this->getLayout()->createBlock($this->defaultToolbarBlock, uniqid(microtime()));

        return $block;
    }

    public function getPromotionCollection()
    {
        $toolbar = $this->getToolbarBlock();

        if (empty($this->collection)) {
            $collection = $this->promotionRepository->getCollection()
                ->addAttributeToSelect([
                    '*',
                ])
                ->addStoreFilter($this->context->getStoreManager()->getStore()->getId())
                ->addVisibilityFilter()
                ->addAttributeToSort('updated_at', 'DESC');

            if ($category = $this->getCategory()) {
                $collection->addCategoryFilter($category);
            } elseif ($q = $this->getRequest()->getParam('q')) {
                $collection->addSearchFilter($q);
            }

            $collection->setCurPage($this->getCurrentPage());

            $limit = (int)$toolbar->getLimit();
            if ($limit) {
                $collection->setPageSize($limit);
            }

            $page = (int)$toolbar->getCurrentPage();
            if ($page) {
                $collection->setCurPage($page);
            }

            if ($order = $toolbar->getCurrentOrder()) {
                $collection->setOrder($order, $toolbar->getCurrentDirection());
            }
            $collection->defaultOrder();

            $this->collection = $collection;
        }

        return $this->collection;
    }

    /**
     * @return Promotion
     */
    public function getPromotion()
    {
        return $this->registry->registry('current_promolist_promotion');
    }
}
