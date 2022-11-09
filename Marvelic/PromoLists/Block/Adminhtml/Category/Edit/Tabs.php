<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Category\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDestElementId('edit_form');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', [
            'label'   => __('General'),
            'content' => $this->getLayout()
                ->createBlock('\Marvelic\PromoLists\Block\Adminhtml\Category\Edit\Tab\General')->toHtml(),
        ]);

        $this->addTab('meta_section', [
            'label'   => __('Promotions'),
            'content' => $this->getLayout()
                ->createBlock('\Marvelic\PromoLists\Block\Adminhtml\Category\Edit\Tab\Promotions')->toHtml(),
        ]);

        return parent::_beforeToHtml();
    }
}
