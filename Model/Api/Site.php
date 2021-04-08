<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\TinifyCDN\Model\Api;

use TIG\TinifyCDN\Model\AbstractApi;

class Site extends AbstractApi
{
    const TINIFY_API_CDN_SITES = 'cdn/sites';

    /**
     * @param null $storeId
     *
     * @return array|mixed|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function fetchSite($storeId = null)
    {
        $sites = $this->getAvailableSites();
        /** @var \Magento\Store\Model\Store $store */
        $store   = $this->getStore($storeId);
        $baseUrl = $store->getBaseUrl();

        if (isset($sites->error) && !empty($sites->error)) {
            return null;
        }

        $site = array_filter(
            $sites,
            function ($properties) use ($baseUrl) {
                return strtolower($properties->origin_url) == $baseUrl;
            }
        );
        $site = reset($site);

        return $site;
    }

    /**
     * @return array
     */
    private function getAvailableSites()
    {
        $result = $this->doGetRequest();

        return json_decode($result['body']);
    }

    /**
     * @return array
     */
    private function doGetRequest()
    {
        return $this->call(static::TINIFY_API_CDN_SITES, 'get', true);
    }
}
