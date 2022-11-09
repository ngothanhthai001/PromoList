<?php

namespace Marvelic\PromoLists\Controller;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Api\Data\CategoryInterface;
use Marvelic\PromoLists\Model\CategoryFactory;

abstract class Category extends Action
{
    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Registry
     */
    protected $registry;

    public function __construct(
        CategoryFactory $categoryFactory,
        Registry $registry,
        Context $context
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->registry        = $registry;
        $this->context         = $context;
        $this->resultFactory   = $context->getResultFactory();

        parent::__construct($context);
    }

    /**
     * @return \Marvelic\PromoLists\Model\Category
     */
    protected function initCategory()
    {
        if ($id = $this->getRequest()->getParam(CategoryInterface::ID)) {
            $promotion = $this->categoryFactory->create()->load($id);
            if ($promotion->getId() > 0) {
                $this->registry->register('current_promolist_category', $promotion);

                return $promotion;
            }
        }
    }
}
