<?php

namespace Marvelic\PromoLists\Block\Adminhtml\Promotion\Edit\Tab;

use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Model\Promotion;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Marvelic\PromoLists\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Store\Model\System\Store;
use Marvelic\PromoLists\Block\Adminhtml\Promotion\Edit\Tab\Render\Image;
use Marvelic\PromoLists\Model\Config;
use Marvelic\PromoLists\Ui\Promotion\Source\CategoryTree;


class General extends Form
{
    /**
     * @var Store
     */
    protected $_systemStore;

//    /**
//     * @var CategoryCollectionFactory
//     */
//    protected $categoryCollectionFactory;
//
//    /**
//     * @var FormFactory
//     */
//    protected $formFactory;
//
//    /**
//     * @var Registry
//     */
//    protected $registry;
//
    /**
     * @var WysiwygConfig
     */
    protected $wysiwygConfig;

//    /**
//     * @param CategoryCollectionFactory $promotionCollectionFactory
//     * @param WysiwygConfig $wysiwygConfig
//     * @param FormFactory   $formFactory
//     * @param Registry      $registry
//     * @param Context       $context
//     */
//    public function __construct(
//        CategoryCollectionFactory $promotionCollectionFactory,
//        WysiwygConfig $wysiwygConfig,
//        FormFactory $formFactory,
//        Registry $registry,
//        Context $context
//    ) {
//        $this->categoryCollectionFactory = $promotionCollectionFactory;
//        $this->wysiwygConfig = $wysiwygConfig;
//        $this->formFactory   = $formFactory;
//        $this->registry      = $registry;
//
//        parent::__construct($context);
//    }

//    /**
//     * @param CategoryCollectionFactory $promotionCollectionFactory
//     * @param \Magento\Backend\Block\Template\Context $context
//     * @param Registry $registry
//     * @param FormFactory $formFactory
//     * @param array $data
//     */
//    public function __construct(
//        Store $systemStore,
//        CategoryCollectionFactory $promotionCollectionFactory,
//        \Magento\Backend\Block\Template\Context $context,
//        \Magento\Framework\Registry $registry,
//        \Magento\Framework\Data\FormFactory $formFactory,
//        array $data = []
//    ) {
//        $this->formFactory               = $formFactory;
//        $this->_systemStore = $systemStore;
//        $this->categoryCollectionFactory = $promotionCollectionFactory;
//        parent::__construct($context, $data);
//    }


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
     * @var Config
     */
    protected $config;

    /**
     * @var CategoryTree
     */
    protected $categoryTree;


    /**
     * @param CategoryCollectionFactory $postCollectionFactory
     * @param Store $systemStore
     * @param WysiwygConfig $wysiwygConfig
     * @param FormFactory $formFactory
     * @param Registry $registry
     * @param Context $context
     * @param Config $config
     * @param CategoryTree $categoryTree
     * @param array $data
     */
    public function __construct(
        CategoryCollectionFactory $postCollectionFactory,
        Store $systemStore,
        WysiwygConfig $wysiwygConfig,
        FormFactory $formFactory,
        Registry $registry,
        Context $context,
        Config $config,
        CategoryTree $categoryTree,
        array $data = []
    ) {
        $this->categoryCollectionFactory = $postCollectionFactory;
        $this->formFactory               = $formFactory;
        $this->registry                  = $registry;
        $this->_systemStore = $systemStore;
        $this->wysiwygConfig = $wysiwygConfig;
        $this->config = $config;
        $this->categoryTree = $categoryTree;

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
        $form->setHtmlIdPrefix('promotion_');

        /** @var Promotion $promotion */
        $promotion = $this->registry->registry('promotion');

        $fieldset = $form->addFieldset('edit_fieldset', [
            'legend' => __('Promotion Information')
        ]);

        if ($promotion->getId()) {
            $fieldset->addField('entity_id', 'hidden', [
                'name'  => 'entity_id',
                'value' => $promotion->getId(),
            ]);
        }

        $fieldset->addField('name', 'text', [
            'label'    => __('Title'),
            'name'     => 'name',
            'value'    => $promotion->getName(),
            'required' => true,
        ]);

        $fieldset->addField('status', 'select', [
            'label'  => __('Status'),
            'name'   => 'status',
            'value'  => $promotion->getStatus(),
            'options' => [0 => __('Disabled'), 1 => __('Enabled')],
        ]);

        $fieldset->addField('position', 'text', [
            'label'    => __('Order'),
            'name'     => 'position',
            'value'    => $promotion->getPosition(),
        ]);

        if (!$this->_storeManager->isSingleStoreMode()) {
            /** @var RendererInterface $rendererBlock */
            $rendererBlock = $this->getLayout()->createBlock(Element::class);
            $fieldset->addField('store_ids', 'multiselect', [
                'name' => 'store_ids',
                'label' => __('Store Views'),
                'title' => __('Store Views'),
                'required' => true,
                'value' => $promotion->getStoreIds(),
                'values' => $this->_systemStore->getStoreValuesForForm(false, true)
            ])->setRenderer($rendererBlock);
        } else {
            $fieldset->addField('store_ids', 'hidden', [
                'name' => 'store_ids',
                'value' => $this->_storeManager->getStore()->getId()
            ]);
        }

        $fieldset->addField('published_on', 'date', [
            'label'       => __('Published on'),
            'name'        => 'published_on',
            'value'       => $promotion->getPublishedOn(),
            'date_format' => 'MMM d, y',
            'time_format' => 'h:mm a',
        ]);

        $fieldset->addField('expiration_on', 'date', [
            'label'       => __('Expiration on'),
            'name'        => 'expiration_on',
            'value'       => $promotion->getExpirationOn(),
            'date_format' => 'MMM d, y',
            'time_format' => 'h:mm a',
        ]);

        $categoryCollection = $this->categoryCollectionFactory->create()
            ->addAttributeToSelect(['name']);

        $fieldset->addField('category_ids', 'checkboxes', [
            'label'       => __('Categories'),
            'name'   => 'category_ids[]',
            'value'  => $promotion->getCategoryIds(),
            'values' => $categoryCollection->toOptionArray(),
            'options' => $categoryCollection->toOptionArray(),
        ]);

        $fieldset->addField('cover_image', Image::class, [
            'label'       => __('Cover Image'),
            'required' => false,
            'name'     => 'cover_image',
            'value'    => $promotion->getCoverImageUrl(),
            'values'    => $promotion->getCoverImageUrl(),
        ]);

        $editorConfig = $this->wysiwygConfig->getConfig(['tab_id' => $this->getTabId()]);

        $fieldset->addField('content', 'editor', [
            'label'       => __('Content'),
            'name'    => 'content',
            'value'   => $promotion->getContent(),
            'wysiwyg' => true,
            'style'   => 'height:35em',
            'config'  => $editorConfig,
        ]);

        $fieldset->addField('short_content', 'editor', [
            'label'   => __('Excerpt'),
            'name'    => 'short_content',
            'value'   => $promotion->getShortContent(),
            'wysiwyg' => true,
            'style'   => 'height:5em',
            'config'  => $editorConfig,
        ]);

        $attributesFilter = $this->config->getAttributesFilter();



//        $form->setValues($promotion->getData());
//        $this->setForm($form);
        return parent::_prepareForm();
    }
    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel(): string
    {
        return __('General Setting');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle(): string
    {
        return $this->getTabLabel();
    }
    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return boolean
     */
    public function canShowTab(): bool
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return boolean
     */
    public function isHidden(): bool
    {
        return false;
    }

}
