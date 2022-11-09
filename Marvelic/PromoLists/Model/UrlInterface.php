<?php

namespace Marvelic\PromoLists\Model;

interface UrlInterface
{
    /**
     * @param array $urlParams
     *
     * @return string
     */
    public function getUrl($urlParams = []);
}
