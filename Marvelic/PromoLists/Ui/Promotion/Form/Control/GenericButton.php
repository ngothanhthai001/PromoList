<?php


namespace Marvelic\PromoLists\Ui\Promotion\Form\Control;

use Magento\Backend\Block\Widget\Context;
use Marvelic\PromoLists\Api\Data\PromotionInterface;

class GenericButton
{
    /**
     * @var Context
     */
    private $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->context->getRequest()->getParam(PromotionInterface::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
