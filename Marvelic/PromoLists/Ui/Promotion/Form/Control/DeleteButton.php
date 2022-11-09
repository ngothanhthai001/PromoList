<?php

namespace Marvelic\PromoLists\Ui\Promotion\Form\Control;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Marvelic\PromoLists\Api\Data\PromotionInterface;

class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getId()) {
            $data = [
                'label'      => __('Delete'),
                'class'      => 'delete',
                'on_click'   => 'deleteConfirm(\'' . __(
                    'Are you sure you want to do this?'
                ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [PromotionInterface::ID => $this->getId()]);
    }
}
