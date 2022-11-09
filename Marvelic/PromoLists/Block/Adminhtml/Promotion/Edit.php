<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Promotion;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Marvelic\PromoLists\Model\Promotion;
use Magento\Framework\Registry;


class Edit extends Container
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * @param Registry $coreRegistry
     * @param Context $context
     * @param array $data
     */
    public function __construct(Registry $coreRegistry,Context $context, array $data = [])
    {
        $this->coreRegistry = $coreRegistry;

        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();

        $this->_objectId   = 'entity_id';
        $this->_controller = 'adminhtml_promotion';
        $this->_blockGroup = 'Marvelic_PromoLists';
        parent::_construct();
        $this->buttonList->update('save', 'label', __('Save Promotion'));
        $this->buttonList->add(
            'save-and-continue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => 'saveAndContinueEdit',
                            'target' => '#edit_form'
                        ]
                    ]
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete Promotion'));
    }

    /**
     * Retrieve text for header element depending on loaded Slider
     *
     * @return string
     */
    public function getHeaderText()
    {
        /** @var Promotion $promotion */
        $promotion = $this->getPromotion();
        if ($promotion->getId()) {
            return __("Edit Promotion '%1'", $this->escapeHtml($promotion->getName()));
        }

        return __('New Promotion');
    }

    /**
     * @return mixed
     */
    public function getPromotion()
    {
        return $this->coreRegistry->registry('promotion');
    }

}
