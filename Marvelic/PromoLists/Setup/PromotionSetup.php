<?php

namespace Marvelic\PromoLists\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Marvelic\PromoLists\Model\PromotionFactory;
use Marvelic\PromoLists\Model\Promotion\Attribute\Source\Coupon;
use Marvelic\PromoLists\Model\ResourceModel\Promotion;
use Magento\Eav\Model\Entity\Attribute\Backend\Datetime;
use Magento\Catalog\Model\Attribute\Backend\Startdate;


class PromotionSetup extends EavSetup
{
    /**
     * @var PromotionFactory
     */
    private $promotionFactory;

    /**
     * Init
     *
     * @param ModuleDataSetupInterface $setup
     * @param Context                  $context
     * @param CacheInterface           $cache
     * @param CollectionFactory        $attrGroupCollectionFactory
     * @param PromotionFactory              $categoryFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        PromotionFactory $categoryFactory
    ) {
        $this->promotionFactory = $categoryFactory;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }

    /**
     * Default entities and attributes
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        return [
            'promolist_promotion' => [
                'entity_model' => Promotion::class,
                'table'        => 'promolist_promotion_entity',
                'attributes'   => [
                    'type'  => [
                        'type'   => 'static',
                        'label'  => 'Type',
                        'input'  => 'text',
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    ],
                    'name'  => [
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
                        'source' => 'Marvelic\PromoLists\Model\Promotion\Attribute\Source\Status',
                        'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                    ],

                    'content' => [
                        'type'   => 'text',
                        'label'  => 'Content',
                        'input'  => 'textarea',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],

                    'short_content' => [
                        'type'   => 'text',
                        'label'  => 'Short Content',
                        'input'  => 'textarea',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],

                    'cover_image' => [
                        'type'   => 'text',
                        'label'  => 'Cover Image',
                        'input'  => 'text',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'meta_title'       => [
                        'type'   => 'varchar',
                        'label'  => 'Meta Title',
                        'input'  => 'text',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'cover_alt'       => [
                        'type'   => 'varchar',
                        'label'  => 'Alt',
                        'input'  => 'text',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'meta_keywords'    => [
                        'type'   => 'text',
                        'label'  => 'Meta Keywords',
                        'input'  => 'textarea',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'meta_description' => [
                        'type'   => 'varchar',
                        'label'  => 'Meta Description',
                        'input'  => 'textarea',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
//                    'coupon_title'      => [
//                        'type'   => 'varchar',
//                        'label'  => 'Coupon Title',
//                        'input'  => 'text',
//                        'global' => ScopedAttributeInterface::SCOPE_STORE,
//                    ],
//                    'coupon_description'      => [
//                        'type'   => 'varchar',
//                        'label'  => 'Coupon Description',
//                        'input'  => 'textarea',
//                        'global' => ScopedAttributeInterface::SCOPE_STORE,
//                    ],
                    'attribute_allow' => [
                        'type' => 'varchar',
                        'label' => 'Attribute Allow',
                        'input' => 'text',
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'coupon_code' => [
                        'type' => 'int',
                        'label' => 'Coupon Code',
                        'input' => 'select',
                        'source' => Coupon::class,
                        'required' => false,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'published_on' => [
                        'type' => 'datetime',
                        'label' => 'Published On',
                        'input' => 'date',
                        'backend' => Startdate::class,
                        'required' => false,
                        'global' => ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'expiration_on' => [
                        'type' => 'datetime',
                        'label' => 'Expiration On',
                        'input' => 'date',
                        'backend' => Datetime::class,
                        'required' => false,
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
}
