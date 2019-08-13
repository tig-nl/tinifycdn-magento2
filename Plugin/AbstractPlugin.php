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

namespace TIG\TinyCDN\Plugin;

use Magento\Store\Model\StoreManagerInterface;
use TIG\TinyCDN\Model\Config\Provider\CDN\Configuration as CDNConfiguration;
use TIG\TinyCDN\Model\Config\Provider\General\Configuration as GeneralConfiguration;

abstract class AbstractPlugin
{
    /** @var StoreManagerInterface $storeManager */
    private $storeManager;

    /** @var CDNConfiguration $cdnConfig */
    private $cdnConfig;

    /** @var GeneralConfiguration $generalConfig */
    private $generalConfig;

    /**
     * AbstractPlugin constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param CDNConfiguration      $cdnConfig
     * @param GeneralConfiguration  $generalConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CDNConfiguration $cdnConfig,
        GeneralConfiguration $generalConfig
    ) {
        $this->storeManager  = $storeManager;
        $this->cdnConfig     = $cdnConfig;
        $this->generalConfig = $generalConfig;
    }

    /**
     * @param $url
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCdnUrl($url)
    {
        if (!$this->generalConfig->isEnabled()) {
            return $url;
        }

        $endpoint    = $this->cdnConfig->getCdnEndpoint();
        $store       = $this->storeManager->getStore();
        $baseUrl     = $store->getBaseUrl();
        $modifiedUrl = str_replace($baseUrl, $endpoint, $url);

        return $modifiedUrl;
    }
}
