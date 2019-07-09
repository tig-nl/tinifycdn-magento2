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

namespace TIG\TinyCDN\Model;

use Magento\Framework\HTTP\Client\Curl;
use TIG\TinyCDN\Model\Config\Provider\CDN\Configuration;

abstract class AbstractApi
{
    /** @var Curl $curl */
    private $curl;

    /** @var Configuration $config */
    private $config;

    /**
     * AbstractApi constructor.
     *
     * @param Curl          $curl
     * @param Configuration $config
     */
    public function __construct(
        Curl $curl,
        Configuration $config
    ) {
        $this->curl   = $curl;
        $this->config = $config;
    }

    /**
     * @param string $uri
     * @param string $method Can be either 'get' or 'post'
     * @param bool   $includeToken
     *
     * @return mixed
     */
    public function call(string $uri, string $method, bool $includeToken)
    {
        $url = $this->config->getApiUrl($uri);

        if ($includeToken) {
            $token = $this->config->getAccessToken();

            $this->curl->addHeader(Configuration::TINYCDN_CDN_AUTH_PARAM, 'Bearer ' . $token);
        }

        $this->curl->$method($url);
        $body = $this->curl->getBody();

        return $body;
    }
}
