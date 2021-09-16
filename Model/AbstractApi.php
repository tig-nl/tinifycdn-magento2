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

namespace TIG\TinifyCDN\Model;

use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Config\Model\ResourceModel\Config\Data\CollectionF actory;
use TIG\TinifyCDN\Model\Config\Provider\CDN\Configuration;

abstract class AbstractApi
{
    /** @var Curl $curl */
    private $curl;

    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var Configuration $config */
    private $config;

    /** @var CollectionFactory */
    protected $configCollection;

    /**
     * AbstractApi constructor.
     *
     * @param Curl          $curl
     * @param Configuration $config
     * @param CollectionFactory $configCollection
     */
    public function __construct(
        Curl $curl,
        StoreManagerInterface $storeManager,
        Configuration $config,
        CollectionFactory $configCollection
    ) {
        $this->curl             = $curl;
        $this->storeManager     = $storeManager;
        $this->config           = $config;
        $this->configCollection = $configCollection;
    }

    /**
     * @param string $uri
     * @param string $method
     * @param bool   $includeToken
     * @param int|null $storeId
     *
     * @return array
     */
    public function call(string $uri, string $method, bool $includeToken, int $storeId = null)
    {
        $url = $this->config->getApiUrl($uri);

        if ($includeToken) {
            $token = $this->getAccessTokenFromConfig($storeId);
            $this->curl->addHeader(Configuration::TINIFYCDN_CDN_AUTH_PARAM, 'Bearer ' . $token);
        }

        if ($method == 'post') {
            $this->curl->addHeader('Content-Type', 'application/json');
        }

        $this->curl->$method($url, []); // The empty 2nd parameter is needed for POST calls.
        $body   = $this->curl->getBody();
        $status = $this->curl->getStatus();

        return [
            'status' => $status,
            'body'   => $body
        ];
    }

    /**
     * @param null $storeId
     *
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore($storeId = null)
    {
        return $this->storeManager->getStore($storeId);
    }

    /**
     * @return mixed|string|null
     */
    public function getAccessTokenFromConfig($storeId = null)
    {
        $token = $this->config->getAccessToken($storeId);
        if ($token != 0) {
            return $token;
        }

        // Fallback method: Read directly from core_config_table
        // Scenario: New install, token is saved in database but config cache return old value
        $collection = $this->configCollection->create();
        $collection->addFieldToFilter("path",['eq' => Configuration::XPATH_TINIFYCDN_CDN_ACCESS_TOKEN]);
        $collection->addFieldToFilter("scope_id",['eq' => intval($storeId)]);
        if($collection->count()>0){
            return $collection->getFirstItem()->getData()['value'];
        }

        return null;
    }
}
