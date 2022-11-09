<?php

namespace Marvelic\PromoLists\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Category extends Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'Marvelic_PromoLists';

        parent::_construct();
    }
}
