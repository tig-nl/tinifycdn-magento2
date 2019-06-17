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

namespace TIG\TinyCDN\Observer;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface as ConfigWriter;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use TIG\TinyCDN\Config\Provider\General\Configuration as GeneralConfiguration;

/**
 * TODO: Replace this class with Plugins as mentioned in the Functional Design.
 */
class Config implements ObserverInterface
{
    /** @var string */
    const CDN_ENDPOINT_CONFIG_PATH = 'tig_tinycdn/cdn/endpoint';
    
    /** @var array */
    private $baseUrlConfigPaths = [
        "web/unsecure/base_static_url",
        "web/unsecure/base_media_url"
    ];
    
    /**
     * Config constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface    $scopeConfig
     * @param \Magento\Framework\App\Config\Storage\WriterInterface $configWriter
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigWriter $configWriter,
        GeneralConfiguration $generalConfig
    ) {
        $this->scopeConfig   = $scopeConfig;
        $this->configWriter  = $configWriter;
        $this->generalConfig = $generalConfig;
    }
    
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        $configValue = $this->scopeConfig->getValue(static::CDN_ENDPOINT_CONFIG_PATH);
        $liveMode    = $this->generalConfig->isModeLive();
        
        if (!$liveMode) {
            $this->removeEndpoints();
            
            return;
        }
        
        foreach ($this->baseUrlConfigPaths as $configPath) {
            $this->saveEndpoint($configPath, $configValue);
        }
    }
    
    /**
     * Removes the endpoints from web/(un)secure.
     */
    private function removeEndpoints()
    {
        foreach ($this->baseUrlConfigPaths as $configPath) {
            $this->saveEndPoint($configPath, null);
        }
    }
    
    /**
     * @param $path
     * @param $endpoint
     */
    private function saveEndpoint($path, $endpoint)
    {
        $this->configWriter->save($path, $endpoint, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
    }
}
