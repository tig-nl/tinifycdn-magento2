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
    public function retrieveForCurrentStore()
    {
        $result = $this->retrieve();

        $baseUrl = 'https://example.com';
        // TODO: endpoint not yet included in Tinify API.
        return $this->findEndpointForCurrentStore($result, $baseUrl);
    }

    /**
     * @return array
     */
    public function retrieve()
    {
        $result = $this->doGetRequest();

        return json_decode($result);
    }

    /**
     * @param $results
     * @param $baseUrl
     *
     * @return string|null
     */
    private function findEndpointForCurrentStore($results, $baseUrl)
    {
        foreach ($results as $index => $properties) {
            $originUrl = is_object($properties) ? $properties->origin_url : $properties['origin_url'];
            if (is_object($properties) && $originUrl == $baseUrl) {
                // TODO: Change to actual input. Not available through API yet.
                return 'https://' . $results[$index]->key . '.tinify.com/';
            }
        }

        return null;
    }

    /**
     * @return string
     */
    private function doGetRequest()
    {
        return $this->call(static::TINIFY_API_CDN_SITES, 'get', true);
    }
}
