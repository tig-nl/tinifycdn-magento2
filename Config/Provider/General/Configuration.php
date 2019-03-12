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

namespace TIG\TinyCDN\Config\Provider\General;

use TIG\TinyCDN\Config\Provider\AbstractConfigProvider;

class Configuration extends AbstractConfigProvider
{
    const XPATH_CONFIGURATION_MODE        = 'tig_tinycdn/general/mode';
    
    /**
     * Checks if the extension is on status live
     *
     * @param null|int $store
     *
     * @return bool
     */
    public function isModeLive($store = null)
    {
        if ($this->getMode($store) == '1') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Checks if the extension is on status test
     *
     * @param null|int $store
     *
     * @return bool
     */
    public function isModeTest($store = null)
    {
        if ($this->getMode($store) == '2') {
            return true;
        }
        
        return false;
    }
    
    /**
     * Checks if the extension is on status off.
     *
     * @param null|int $store
     *
     * @return bool
     */
    public function isModeOff($store = null)
    {
        if ($this->getMode($store) == '0' || false == $this->getMode()) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Should return one of these values
     *  '1' => live ||
     *  '2' => test ||
     *  '0' => off
     *
     * @param null|int $store
     *
     * @return mixed
     */
    public function getMode($store = null)
    {
        if (!$this->isModuleOutputEnabled()) {
            return '0';
        }
        
        return $this->getConfigFromXpath(static::XPATH_CONFIGURATION_MODE, $store);
    }
    
    /**
     * @return bool
     */
    private function isModuleOutputEnabled()
    {
        return $this->getModuleManager()->isOutputEnabled('TIG_TinyCDN');
    }
}