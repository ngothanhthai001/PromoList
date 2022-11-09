<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Promotion\Edit;

/**
 * @method Tabs setTitle(string $title)
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('promotion_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Promotion Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', [
            'label'   => __('General Information'),
            'content' => $this->getLayout()
                ->createBlock('\Marvelic\PromoLists\Block\Adminhtml\Promotion\Edit\Tab\General')->toHtml(),
        ]);

//        $this->addTab('meta_section', [
//            'label'   => __('Search Engine Optimization'),
//            'content' => $this->getLayout()
//                ->createBlock('\Mirasvit\Blog\Block\Adminhtml\Category\Edit\Tab\Meta')->toHtml(),
//        ]);

        return parent::_beforeToHtml();
    }

}
