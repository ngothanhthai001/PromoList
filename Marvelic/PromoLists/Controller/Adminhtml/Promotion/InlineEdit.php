<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Promotion;

use Exception;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Api\PromotionRepositoryInterface;
use Marvelic\PromoLists\Controller\Adminhtml\Promotion;

class InlineEdit extends Promotion
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
        $this->jsonFactory = $jsonFactory;

        parent::__construct($promotionRepository, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();

        $error    = false;
        $messages = [];

        $items = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($items))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error'    => true,
            ]);
        }

        foreach (array_keys($items) as $promotionId) {
            $promotion = $this->promotionRepository->get($promotionId);

            try {
                $data = $items[$promotionId];

                //@todo
                $promotion->addData($data);

                $this->promotionRepository->save($promotion);
            } catch (Exception $e) {
                $messages[] = __('Something went wrong while saving the promotion.');
                $error      = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error'    => $error,
        ]);
    }
}
