<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Promotion\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Data\FormFactory;

class Form extends \Magento\Backend\Block\Widget\Form
{
    /**
     * @var FormFactory
     */
    protected $formFactory;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param FormFactory   $formFactory
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        FormFactory $formFactory,
        Context $context,
        array $data = []
    ) {
        $this->formFactory = $formFactory;
        $this->context     = $context;

        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]);
    }
    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create()->setData([
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', ['id' => $this->getRequest()->getParam('id')]),
            'method'  => 'post',
            'enctype' => 'multipart/form-data',
        ]);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
