<?php

namespace Marvelic\PromoLists\Block\Promotion;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Marvelic\PromoLists\Model\Config;

class AbstractBlock extends Template
{
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
     * @param Config   $config
     * @param Registry $registry
     * @param Context  $context
     */
    public function __construct(
        Config $config,
        Registry $registry,
        Context $context
    ) {
        $this->config   = $config;
        $this->registry = $registry;
        $this->context  = $context;

        parent::__construct($context);
    }

    /**
     * @param object|null $promotion
     *
     * @return Meta
     */
    public function getPromotionMetaHeader($promotion = null)
    {
        /** @var Meta $block */
        $block = $this->getLayout()->createBlock('Marvelic\PromoLists\Block\Promotion\Meta');

        $block->setTemplate('promotion/meta/header.phtml');

        if ($promotion) {
            $block->setData('promotion', $promotion);
        }

        return $block;
    }

    /**
     * @param object|null $promotion
     *
     * @return Meta
     */
    public function getPromotionMetaFooter($promotion = null)
    {
        /** @var Meta $block */
        $block = $this->getLayout()->createBlock('Marvelic\PromoLists\Block\Promotion\Meta');

        $block->setTemplate('promotion/meta/footer.phtml');

        if ($promotion) {
            $block->setData('promotion', $promotion);
        }

        return $block;
    }
}
