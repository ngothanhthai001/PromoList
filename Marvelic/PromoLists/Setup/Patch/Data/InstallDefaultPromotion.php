<?php

namespace Marvelic\PromoLists\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Marvelic\PromoLists\Setup\PromotionSetupFactory;

class InstallDefaultPromotion implements DataPatchInterface
{
    /**
     * ModuleDataSetupInterface
     *
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var PromotionSetupFactory
     */
    protected $promotionSetupFactory;

    /**
     * AddProductAttribute constructor.
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PromotionSetupFactory $promotionSetupFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        PromotionSetupFactory $promotionSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->promotionSetupFactory = $promotionSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function apply()
    {
        $promotionSetup = $this->promotionSetupFactory->create(['setup' => $this->moduleDataSetup]);
        $promotionSetup->installEntities();

    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }
}