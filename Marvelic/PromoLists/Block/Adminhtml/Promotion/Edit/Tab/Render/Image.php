<?php
namespace Marvelic\PromoLists\Block\Adminhtml\Promotion\Edit\Tab\Render;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Math\Random;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Helper\SecureHtmlRenderer;

class Image extends \Magento\Framework\Data\Form\Element\Image
{

    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        UrlInterface $urlBuilder,
        $data = [],
        ?SecureHtmlRenderer $secureRenderer = null,
        ?Random $random = null
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $urlBuilder, $data, $secureRenderer,
            $random);
    }

    /**
     * Get image preview url
     *
     * @return string
     */
    protected function _getUrl()
    {
        $url = '';
        if ($this->getValues()) {
            $url = $this->getValues();
        }

        return $url;
    }

    /**
     * Return element html code
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';

        if ((string)$this->getValue()) {
            $url = $this->_getUrl();

            if (!preg_match("/^http\:\/\/|https\:\/\//", $url)) {
                $url = $this->_urlBuilder->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]) . $url;
            }

            $html = '<a href="' .
                $url .
                '"' .
                ' onclick="imagePreview(\'' .
                $this->getHtmlId() .
                '_image\'); return false;" ' .
                $this->_getUiId(
                    'link'
                ) .
                '>' .
                '<img src="' .
                $url .
                '" id="' .
                $this->getHtmlId() .
                '_image" title="' .
                $this->getValue() .
                '"' .
                ' alt="' .
                $this->getValue() .
                '" height="156" width="350px" class="small-image-preview v-middle"  ' .
                $this->_getUiId() .
                ' />' .
                '</a> ';
        }
        $this->setClass('input-file');
        $html .= AbstractElement::getElementHtml();
        $html .= $this->_getDeleteCheckbox();

        return $html;
    }
}