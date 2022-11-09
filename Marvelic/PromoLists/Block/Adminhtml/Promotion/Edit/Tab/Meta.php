<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Promotion\Edit\Tab;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Model\Promotion;

class Meta extends Form
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param FormFactory                           $formFactory
     * @param Registry                              $registry
     * @param Context $context
     * @param array                                 $data
     */
    public function __construct(
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->registry    = $registry;
        $this->context     = $context;

        parent::__construct($context, $data);
    }

//    /**
//     * @return $this
//     * @throws LocalizedException
//     */
//    protected function _prepareForm()
//    {
//        $form = $this->formFactory->create();
//        $this->setForm($form);
//
//        /** @var Promotion $promotion */
//        $promotion = $this->registry->registry('promotion');
//
//        $fieldset = $form->addFieldset('edit_fieldset', [
//            'class' => 'promolist__promotion-fieldset',
//        ]);
//
//        $fieldset->addField('meta_title', 'text', [
//            'label' => __('Meta Title'),
//            'name'  => 'promotion[meta_title]',
//            'value' => $promotion->getMetaTitle(),
//        ]);
//
//        $fieldset->addField('meta_description', 'textarea', [
//            'label' => __('Meta Description'),
//            'name'  => 'promotion[meta_description]',
//            'value' => $promotion->getMetaDescription(),
//        ]);
//
//        $fieldset->addField('meta_keywords', 'textarea', [
//            'label' => __('Meta Keywords'),
//            'name'  => 'promotion[meta_keywords]',
//            'value' => $promotion->getMetaKeywords(),
//        ]);
//
//        $fieldset->addField('url_key', 'text', [
//            'label' => __('URL Key'),
//            'name'  => 'promotion[url_key]',
//            'value' => $promotion->getUrlKey(),
//        ]);
//
//        return parent::_prepareForm();
//    }
}
