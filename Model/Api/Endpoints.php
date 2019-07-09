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

namespace TIG\TinyCDN\Model\Api;

use TIG\TinyCDN\Model\AbstractApi;

class Endpoints extends AbstractApi
{
    const TINIFY_API_CDN_SITES = 'cdn/sites';

    /**
     * @return string|null
     */
    public function fetchEndpoint()
    {
        $sites = $this->getAvailableSites();
        // TODO: Retrieve actual base URL to retrieve correct endpoint.
        $baseUrl = 'https://example.com';

        return $this->filterEndpoints($sites, $baseUrl);
    }

    /**
     * @return array
     */
    public function getAvailableSites()
    {
        $result = $this->doGetRequest();

        return json_decode($result);
    }

    /**
     * @param $availableSites
     * @param $baseUrl
     *
     * @return string|null
     */
    private function filterEndpoints($availableSites, $baseUrl)
    {
        $site = array_filter(
            $availableSites,
            function ($properties) use ($baseUrl) {
                return $properties->origin_url == $baseUrl;
            }
        );
        $site = reset($site);

        return $site->endpoint ?: null;
    }

    /**
     * @return string
     */
    private function doGetRequest()
    {
        return $this->call(static::TINIFY_API_CDN_SITES, 'get', true);
    }
}
