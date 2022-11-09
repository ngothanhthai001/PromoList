<?php

namespace Marvelic\PromoLists\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Backend\Model\View\Result\Page\Interceptor;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Api\PromotionRepositoryInterface;
use Marvelic\PromoLists\Model\PromotionFactory;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Promotion extends Action
{
    /**
     * @var PromotionFactory
     */
    protected $promotionRepository;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var StoreManagerInterface
     */
    //    protected $storeManager;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var JsonFactory
     */
    //    protected $jsonFactory;

    /**
     * @var JsHelper
     */
    //    protected $jsHelper;

    public function __construct(
        PromotionRepositoryInterface $promotionRepository,
        //        StoreManagerInterface $storeManager,
        //        JsonFactory $jsonFactory,
        //        JsHelper $jsHelper,
        Registry $registry,
        //        TimezoneInterface $localeDate,
        Context $context
    ) {
        $this->promotionRepository = $promotionRepository;
        //        $this->storeManager = $storeManager;
        //        $this->jsonFactory = $jsonFactory;
        //        $this->jsHelper = $jsHelper;
        $this->registry = $registry;
        //        $this->localeDate = $localeDate;
        $this->context = $context;

        $this->resultFactory = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return PromotionInterface
     */
    public function initModel()
    {
        $model = $this->promotionRepository->create();
        $id    = $this->getRequest()->getParam(PromotionInterface::ID);

        if ($id && !is_array($id)) {
            $model = $this->promotionRepository->get($id);
        }

        $this->registry->register('promotion', $model);

        return $model;
    }

    /**
     * {@inheritdoc}
     * @param Interceptor $resultPage
     *
     * @return Interceptor
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Marvelic_PromoLists::promolist');
        $resultPage->getConfig()->getTitle()->prepend(__('Promotion List'));

        return $resultPage;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Marvelic_PromoLists::promo');
    }
}
