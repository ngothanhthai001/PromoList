<?php

namespace Marvelic\PromoLists\Block\Promotion\View;

use Magento\Customer\Model\Session;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\SalesRule\Api\RuleRepositoryInterface;
use Magento\SalesRule\Model\ResourceModel\Coupon\Collection as CouponCollection;
use Marvelic\PromoLists\Model\ResourceModel\WishlistItem\CollectionFactory as WishlistItemFactory;

class CouponPromotion extends Template
{
    /**
     * @var CouponCollection
     */
    protected CouponCollection $couponCollection;

    /**
     * @var string
     */
    protected $defaultToolbarBlock = "Marvelic\PromoLists\Block\Promotion\PromotionList\ToolbarCoupon";

    /**
     * @var Registry
     */
    protected Registry $registry;

    /**
     * @var RuleRepositoryInterface
     */
    protected RuleRepositoryInterface $ruleRepository;

    /**
     * @var PostHelper
     */
    protected PostHelper $postHelper;

    /**
     * @var Escaper
     */
    protected Escaper $escaper;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var WishlistItemFactory
     */
    protected WishlistItemFactory $wishlistItemFactory;

    /**
     * @param Registry $registry
     * @param RuleRepositoryInterface $ruleRepository
     * @param PostHelper $postHelper
     * @param Escaper $escaper
     * @param Context $context
     * @param Session $customerSession
     * @param WishlistItemFactory $wishlistItemFactory
     * @param array $data
     */
    public function __construct(
        Registry $registry,
        RuleRepositoryInterface $ruleRepository,
        PostHelper $postHelper,
        Escaper $escaper,
        Template\Context $context,
        Session $customerSession,
        WishlistItemFactory $wishlistItemFactory,
        array $data = []
    ) {
        $this->registry              = $registry;
        $this->ruleRepository        = $ruleRepository;
        $this->postHelper            = $postHelper;
        $this->escaper               = $escaper;
        $this->customerSession       = $customerSession;
        $this->wishlistItemFactory   = $wishlistItemFactory;
        parent::__construct($context, $data);
    }

    public function getCurrentPromotion()
    {
        return $this->registry->registry('current_promolist_promotion');
    }
    public function getTitleDescriptionCoupon($couponId)
    {
        $currentPromotion = $this->getCurrentPromotion();
        return  $currentPromotion->getResource()->getCouponTitleById($currentPromotion->getId(), $couponId);
    }
    public function getFromDate($ruleId)
    {
        return $this->ruleRepository->getById($ruleId)->getFromDate();
    }
    public function getToDate($ruleId)
    {
        return $this->ruleRepository->getById($ruleId)->getToDate();
    }
    public function getTotalAmountHtml($collection)
    {
        return $this->getLayout()->createBlock($this->defaultToolbarBlock)
            ->setTemplate('Marvelic_PromoLists::promotion/list/toolbar/amount.phtml')
            ->setCollection($collection)
            ->toHtml();
    }
    public function getSorterHtml()
    {
        return $this->getLayout()->createBlock($this->defaultToolbarBlock)
            ->setTemplate('Marvelic_PromoLists::promotion/list/toolbar/sorter.phtml')
            ->toHtml();
    }
    protected function _beforeToHtml()
    {
        $toolbar = $this->getToolbarBlock();
        $toolbar->setCollection($this->getCouponPromotion());
        $this->setChild('pager_coupon', $toolbar);
        $this->setCollection($toolbar->getCollection());
        return parent::_beforeToHtml();
    }
    /**
     * @param CouponCollection $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->couponCollection = $collection;

        return $this;
    }
    public function getCouponPromotion()
    {
        $toolbar = $this->getToolbarBlock();
        if (empty($this->collection)) {
            $currentPromotion = $this->getCurrentPromotion();
            $collection = $currentPromotion->getRelatedCouponsFilter($toolbar->getCurrentOrder(), $toolbar->getCurrentDirection());

            $collection->setCurPage($this->getCurrentPage());
            $limit = (int)$toolbar->getLimit();
            if ($limit) {
                $collection->setPageSize($limit);
            }
            $page = (int)$toolbar->getCurrentPage();
            if ($page) {
                $collection->setCurPage($page);
            }
            $this->couponCollection = $collection;
        }
        return $this->couponCollection;
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
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager_coupon');
    }
    public function getUrlWishlist(array $params)
    {
        $url = $this->getUrl('marvelic_promolists/wishlist/add');
        $data = [];
        $data = $params;
        if ($this->customerSession->isLoggedIn()) {
            $data['customer_id'] = $this->customerSession->getId();
        }
        return $this->postHelper->getPostData($this->escaper->escapeUrl($url), $data);
    }
    public function isFavoriteCoupon($couponId)
    {
        if ($this->wishlistItemFactory->create()->addFieldToFilter('coupon_id', $couponId)->getSize()) {
            return true;
        }
        return false;
    }
    public function isLoggin(): bool
    {
        return $this->customerSession->isLoggedIn();
    }
}
