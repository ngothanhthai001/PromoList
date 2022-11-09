<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Promotion;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Api\PromotionRepositoryInterface;
use Marvelic\PromoLists\Controller\Adminhtml\Promotion;
use Marvelic\PromoLists\Model\Config\FileProcessor;

class FileUpload extends Promotion
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    public function __construct(
        FileProcessor $fileProcessor,
        PromotionRepositoryInterface $promotionRepository,
        Registry $registry,
        Context $context
    ) {
        $this->fileProcessor = $fileProcessor;

        parent::__construct($promotionRepository, $registry, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $result = $this->fileProcessor->save(key($_FILES));

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}
