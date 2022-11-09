<?php

namespace Marvelic\PromoLists\Ui\Promotion\Source;

use Magento\Framework\Option\ArrayInterface;
use Marvelic\PromoLists\Api\Data\CategoryInterface;
use Marvelic\PromoLists\Api\CategoryRepositoryInterface;

class CategoryTree implements ArrayInterface
{
    private $categoryRepository;

    public function __construct(
        CategoryRepositoryInterface $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $collection = $this->categoryRepository->getCollection();
        $rootId     = $collection->getRootId();

        return [$this->getOptions($rootId)];
    }

    /**
     * @param int $parentId
     *
     * @return array
     */
    private function getOptions($parentId)
    {
        $category = $this->categoryRepository->get($parentId);

        $data = [
            'label' => $category->getName(),
            'value' => $category->getId(),
        ];

        $collection = $this->categoryRepository->getCollection()
            ->addFieldToFilter(CategoryInterface::PARENT_ID, $category->getId())
            ->setOrder(CategoryInterface::POSITION, 'asc');

        /** @var CategoryInterface $item */
        foreach ($collection as $item) {
            $data['optgroup'][] = $this->getOptions($item->getId());
        }

        return $data;
    }
}
