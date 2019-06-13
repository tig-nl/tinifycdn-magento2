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

use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractModel
{
    const TINYCDN_GENERAL_MODE = 'tig_tinycdn/general/mode';
    
    const TINYCDN_CDN_TEST     = 'tig_tinycdn/cdn/test';
    
    const TINYCDN_CDN_LIVE     = 'tig_tinycdn/cdn/live';
    
    private $scopeConfig;
    
    /**
     * Config constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param ScopeConfig                                                  $scopeConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfig $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }
    
    /**
     * @param $path
     *
     * @return mixed
     */
    private function getConfigValue($path)
    {
        return $this->scopeConfig->getValue($path, ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * @return mixed
     */
    public function getMode()
    {
        return $this->getConfigValue(static::TINYCDN_GENERAL_MODE);
    }
    
    /**
     * @return bool
     */
    public function liveModeEnabled()
    {
        if ($this->getMode() == 1) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @return bool
     */
    public function testModeEnabled()
    {
        if ($this->getMode() == 2) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->testModeEnabled() || $this->liveModeEnabled()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * @return mixed
     */
    public function getCredentialsForCurrentMode()
    {
        if ($this->testModeEnabled()) {
            return $this->getTestCredentials();
        }
        
        return $this->getLiveCredentials();
    }
    
    /**
     * @return mixed
     */
    public function getTestCredentials()
    {
        return $this->getConfigValue(static::TINYCDN_CDN_TEST);
    }
    
    /**
     * @return mixed
     */
    public function getLiveCredentials()
    {
        return $this->getConfigValue(static::TINYCDN_CDN_LIVE);
    }
}
