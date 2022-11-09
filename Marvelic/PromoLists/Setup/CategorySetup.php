<?php
/**
 * Catalog entity setup
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Marvelic\PromoLists\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Marvelic\PromoLists\Model\CategoryFactory;

/**
 * Setup category with default entities.
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CategorySetup extends EavSetup
{
    /**
     * Category model factory
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @param ModuleDataSetupInterface $setup
     * @param Context                  $context
     * @param CacheInterface           $cache
     * @param CollectionFactory        $attrGroupCollectionFactory
     * @param CategoryFactory          $categoryFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }

    /**
     * Default entities and attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        return [
            'promolist_category' => [
                'entity_model' => 'Marvelic\PromoLists\Model\ResourceModel\Category',
                'table'        => 'promolist_category_entity',
                'attributes'   => [
                    'name' => [
                        'type'   => 'varchar',
                        'label'  => 'Name',
                        'input'  => 'text',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],

                    'url_key' => [
                        'type'   => 'varchar',
                        'label'  => 'Url Key',
                        'input'  => 'text',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],

                    'status' => [
                        'type'   => 'int',
                        'label'  => 'Status',
                        'input'  => 'select',
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                        'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    ],

                    'content' => [
                        'type'   => 'text',
                        'label'  => 'Content',
                        'input'  => 'textarea',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],

                    'meta_title'       => [
                        'type'   => 'varchar',
                        'label'  => 'Meta Title',
                        'input'  => 'text',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'meta_keywords'    => [
                        'type'   => 'text',
                        'label'  => 'Meta Keywords',
                        'input'  => 'textarea',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'store_ids'    => [
                        'type'   => 'static',
                        'label'  => 'Store Views',
                        'input'  => 'select',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'meta_description' => [
                        'type'   => 'varchar',
                        'label'  => 'Meta Description',
                        'input'  => 'textarea',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'position' => [
                        'type' => 'static',
                        'label' => 'Position',
                        'required' => false,
                        'sort_order' => 13,
                        'visible' => false,
                        'group' => 'General Information',
                    ],
                ],
            ],
        ];
    }


    /**
     * Creates category model
     *
     * @param array $data
     * @return \Marvelic\PromoLists\Model\Category
     * @codeCoverageIgnore
     */
    public function createCategory($data = [])
    {
        return $this->categoryFactory->create($data);
    }

    /**
     * @return CategoryFactory
     */
    public function getCategoryFactory()
    {
        return $this->categoryFactory;
    }

}
