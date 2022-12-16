<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Promotion;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Api\PromotionRepositoryInterface;
use Marvelic\PromoLists\Controller\Adminhtml\Promotion;

class Save extends Promotion
{

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    public function __construct(
        JsonFactory $jsonFactory,
        PromotionRepositoryInterface $promotionRepository,
        Registry $registry,
        Context $context
    ) {
        $this->jsonFactory   = $jsonFactory;
        parent::__construct($promotionRepository, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam(PromotionInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();
        $data           = $this->filterPromotionData($this->getRequest()->getParams());

        if ($data) {
            /** @var \Marvelic\PromoLists\Model\Promotion $model */
            $model = $this->initModel();
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This promotion no longer exists.'));

                return $resultRedirect->setPath('*/*/');
            }
            $model->addData($data);
            $model->setAttributeAllow(json_encode($data['attribute_allow']));
            if (!empty($data['links']['coupons'])) {
                $model->setCouponTitle($data['links']['coupons']);
                $model->setCouponDescription($data['links']['coupons']);
            }
            if (is_array($data['cover_image'])) {
                $model->setCoverImage($data['cover_image'][0]['result']['name']);
            }
            try {
                if ($this->getRequest()->getParam('isAjax')) {
                    return $this->handlePreviewRequest($model);
                } else {
                    $this->promotionRepository->save($model);
                    $this->messageManager->addSuccessMessage(__('You saved the promotion.'));
                    if ($this->getRequest()->getParam('back') == 'edit') {
                        return $resultRedirect->setPath('*/*/edit', [PromotionInterface::ID => $model->getId()]);
                    }

                    return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
                }
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath(
                    '*/*/edit',
                    [PromotionInterface::ID => $this->getRequest()->getParam(PromotionInterface::ID)]
                );
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }

    /**
     * @param array $rawData
     *
     * @return array
     */
    private function filterPromotionData(array $rawData)
    {
        $data = $rawData;
        foreach ([PromotionInterface::COVER_IMAGE] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                if (!empty($data[$key]['delete'])) {
                    $data[$key] = 'delete';
                } else {
                    if (isset($data[$key][0]['name'])) {
                        $data[$key] = $data[$key][0]['name'];
                    }
                }
            }
        }
        if (!isset($data[PromotionInterface::COVER_IMAGE])) {
            $data[PromotionInterface::COVER_IMAGE] = '';
        }

        if (!isset($data['category_ids'])) {
            $data['category_ids'] = [null];
        }
        if (isset($data['promolist_promotion_form_product_listing'])) {
            $productIds = [];
            foreach ($data['promolist_promotion_form_product_listing'] as $item) {
                $productIds[] = $item['entity_id'];
            }
            $data[PromotionInterface::PRODUCT_IDS] = $productIds;
        } else {
            $data[PromotionInterface::PRODUCT_IDS] = [null];
        }

        if (isset($data['promolist_promotion_form_coupon_listing'])) {
            $couponIds = [];
            foreach ($data['promolist_promotion_form_coupon_listing'] as $item) {
                $couponIds[] = $item['coupon_id'];
            }
            $data[PromotionInterface::COUPON_IDS] = $couponIds;
        } else {
            $data[PromotionInterface::COUPON_IDS] = [null];
        }

        return $data;
    }

    private function handlePreviewRequest(PromotionInterface $model)
    {
        $om            = ObjectManager::getInstance();
        $scopeResolver = $om->create('Magento\Framework\Url\ScopeResolverInterface', [
            'areaCode' => Area::AREA_FRONTEND,
        ]);
        # preview mode save as revision
        $model->setId(false);
        $model->setType(PromotionInterface::TYPE_REVISION);
        $this->promotionRepository->save($model);
        $resultJson = $this->jsonFactory->create();
        $url        = $om->create('Magento\Framework\Url', ['scopeResolver' => $scopeResolver])
            ->getUrl('marvelic_promolists/promotion/view', [
                PromotionInterface::ID => $model->getId(),
                '_scope_to_url'   => false,
                '_nosid'          => true,
            ]);

        return $resultJson->setData([
            PromotionInterface::ID => $model->getId(),
            'url'             => $url,
        ]);
    }
}
