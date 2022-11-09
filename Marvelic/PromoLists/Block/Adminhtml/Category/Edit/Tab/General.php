<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Category\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Store\Model\System\Store;
use Marvelic\PromoLists\Model\Category;
use Marvelic\PromoLists\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class General extends Form
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
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @param CategoryCollectionFactory $promotionCollectionFactory
     * @param FormFactory $formFactory
     * @param Registry $registry
     * @param Context $context
     * @param Store $systemStore
     * @param array $data
     */
    public function __construct(
        CategoryCollectionFactory $promotionCollectionFactory,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        Store $systemStore,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $promotionCollectionFactory;
        $this->formFactory               = $formFactory;
        $this->registry                  = $registry;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function _prepareForm()
    {
        $form = $this->formFactory->create();
        $this->setForm($form);

        /** @var Category $category */
        $category = $this->registry->registry('promotion');

        $fieldset = $form->addFieldset('edit_fieldset', [
            'legend' => __('General Information'),
        ]);

        if ($category->getId()) {
            $fieldset->addField('entity_id', 'hidden', [
                'name'  => 'entity_id',
                'value' => $category->getId(),
            ]);
        }

        $fieldset->addField('name', 'text', [
            'label'    => __('Title'),
            'name'     => 'name',
            'value'    => $category->getName(),
            'required' => true,
        ]);

        if (!$this->isHideParent($category)) {
            $categories = $this->categoryCollectionFactory->create()
                ->addAttributeToSelect('name')
                ->toOptionArray();

            $fieldset->addField('parent_id', 'radios', [
                'label'    => __('Parent Category'),
                'name'     => 'parent_id',
                'value'    => $category->getParentId() ? $category->getParentId() : 1,
                'values'   => $categories,
                'required' => true,
            ]);
        }

        $fieldset->addField('status', 'select', [
            'label'  => __('Status'),
            'name'   => 'status',
            'value'  => $category->getStatus(),
            'values' => ['0' => __('Disabled'), '1' => __('Enabled')],
        ]);

        $fieldset->addField('position', 'text', [
            'label' => __('Order'),
            'name'  => 'position',
            'value' => $category->getPosition(),
        ]);

        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name' => 'store_ids',
                'label' => __('Store Views'),
                'title' => __('Store Views'),
                'required' => true,
                'value' => $category->getStoreIds(),
                'values' => $this->_systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);
        } else {
            $fieldset->addField('store_ids', 'hidden', [
                'name' => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        }

        return parent::_prepareForm();
    }

    /**
     * @param Category $category
     *
     * @return bool
     * @throws LocalizedException
     */
    protected function isHideParent($category)
    {
        $categories = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->toOptionArray();

        return $category->getParentId() === "0" || ($category->getParentId() === null && !count($categories));
    }
}
