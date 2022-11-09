<?php

namespace Marvelic\PromoLists\Ui\Promotion\Form;

use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Marvelic\PromoLists\Api\Data\PromotionInterface;
use Marvelic\PromoLists\Api\PromotionRepositoryInterface;
use Marvelic\PromoLists\Model\Config;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        PromotionRepositoryInterface $promotionRepository,
        Config $config,
        Status $status,
        ImageHelper $imageHelper,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->promotionRepository = $promotionRepository;
        $this->collection     = $this->promotionRepository->getCollection();
        $this->config         = $config;
        $this->status         = $status;
        $this->imageHelper    = $imageHelper;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }


    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->collection as $promotion) {
            $promotion = $this->promotionRepository->get($promotion->getId());

            $result[$promotion->getId()] = [
                PromotionInterface::ID               => $promotion->getId(),
                PromotionInterface::STATUS           => $promotion->getStatus(),
                PromotionInterface::CREATED_AT       => $promotion->getCreatedAt(),
                PromotionInterface::NAME             => $promotion->getName(),
//                PromotionInterface::SHORT_CONTENT    => $promotion->getShortContent(),
//                PromotionInterface::CONTENT          => $promotion->getContent(),
                PromotionInterface::URL_KEY          => $promotion->getUrlKey(),
                PromotionInterface::META_TITLE       => $promotion->getMetaTitle(),

                PromotionInterface::META_KEYWORDS       => $promotion->getMetaKeywords(),
                PromotionInterface::META_DESCRIPTION    => $promotion->getMetaDescription(),
                PromotionInterface::COUPON_TITLE        => $promotion->getcouponTitle(),
                PromotionInterface::COUPON_DESCRIPTION  => $promotion->getCouponDescription(),
                PromotionInterface::COUPON_CODE         => $promotion->getCouponCode(),
                PromotionInterface::CATEGORY_IDS        => $promotion->getCategoryIds(),
                PromotionInterface::STORE_IDS           => $promotion->getStoreIds(),
                PromotionInterface::PUBLISHED_ON        => $promotion->getPublishedOn(),
                PromotionInterface::EXPIRATION_ON       => $promotion->getExpirationOn(),

//                'is_short_content' => $promotion->getShortContent() ? true : false,
            ];

            if ($promotion->getCoverImage()) {
                $result[$promotion->getId()]['cover_image'] = [
                    [
                        'name' => $promotion->getCoverImage(),
                        'url'  => $this->config->getMediaUrl($promotion->getCoverImage()),
                        'size' => filesize($this->config->getMediaPath($promotion->getCoverImage())),
                        'type' => 'image',
                    ],
                ];
            }

            $result[$promotion->getId()]['links']['products'] = [];
            foreach ($promotion->getRelatedProducts() as $product) {
                $result[$promotion->getId()]['links']['products'][] = [
                    'id'        => $product->getId(),
                    'name'      => $product->getName(),
                    'sku'      => $product->getSku(),
                    'status'    => $this->status->getOptionText($product->getStatus()),
                    'thumbnail' => $this->imageHelper->init($product, 'product_listing_thumbnail')->getUrl(),
                ];
            }

//            $result[$promotion->getId()]['links']['coupons'] = [];
//            foreach ($promotion->getRelatedCoupons() as $coupon) {
//                $result[$promotion->getId()]['links']['coupons'][] = [
//                    'id'        => $coupon->getId(),
//                    'name'      => $coupon->getName(),
//                    'from_date'      => $coupon->getFromDate(),
//                    'to_date'      => $coupon->getToDate(),
//                    'coupon_code'      => $coupon->getCode(),
//                ];
//            }
        }

        return $result;
    }
}
