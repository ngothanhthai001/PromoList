<?php

namespace Marvelic\PromoLists\Ui\Component\Listing\Column;

use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Marvelic\PromoLists\Api\Data\PromotionInterface;

class PromotionActions extends Column
{
    /** Url path */
    const PROMOTION_URL_PATH_EDIT = 'marvelic_promolists/promotion/edit';

    /**
     * @var UrlBuilder
     */
    protected $actionUrlBuilder;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var string
     */
    private $editUrl;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::PROMOTION_URL_PATH_EDIT
    ) {
        $this->urlBuilder       = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl          = $editUrl;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item[PromotionInterface::ID])) {
                    $item[$name]['edit'] = [
                        'href'  => $this->urlBuilder->getUrl($this->editUrl, [
                            PromotionInterface::ID => $item[PromotionInterface::ID],
                        ]),
                        'label' => __('Edit'),
                    ];
                }
            }
        }

        return $dataSource;
    }
}
