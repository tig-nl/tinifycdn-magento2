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

namespace TIG\TinyCDN\Model\Config\Source;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\UrlInterface as StandardUrlInterface;
use TIG\TinyCDN\Model\AbstractConfigSource;

class Url extends AbstractConfigSource
{
    const TINYCDN_CDN_AUTHORIZE_URL = 'tinify/cdn/authorize';
    
    const TINYCDN_CDN_REDIRECT_URL  = 'tinify/cdn/redirect';
    
    const TINYCDN_CDN_CONNECT_URL   = 'tinify/cdn/connect';
    
    /** @var BackendUrlInterface $backendUrlBuilder */
    private $backendUrlBuilder;
    
    /** @var StandardUrlInterface $standardUrlBuilder */
    private $standardUrlBuilder;
    
    /**
     * Url constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param BackendUrlInterface                                          $backendUrlBuilder
     * @param StandardUrlInterface                                         $standardUrlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        BackendUrlInterface $backendUrlBuilder,
        StandardUrlInterface $standardUrlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
    ) {
        $this->backendUrlBuilder  = $backendUrlBuilder;
        $this->standardUrlBuilder = $standardUrlBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }
    
    /**
     * getUrl() includes the form-key and admin-uri. getDirectUrl() does not.
     * The backendUrlBuilder-class always follows the admin-structure when
     * building URLs.
     *
     * @param      $uri
     * @param bool $admin
     *
     * @return string
     */
    private function buildUrl($uri, $admin = true)
    {
        if ($admin) {
            return $this->backendUrlBuilder->getUrl($uri);
        }
        
        return $this->standardUrlBuilder->getDirectUrl($uri);
    }
    
    /**
     * Since Magento 2 doesn't allow returning an Admin URL without the form-key
     * we strip the key manually when needed.
     *
     * @param $url
     *
     * @return bool|string
     */
    private function stripKey($url)
    {
        return substr($url, 0, strpos($url, BackendUrlInterface::SECRET_KEY_PARAM_NAME));
    }
    
    /**
     * @param $url
     *
     * @return bool|string
     */
    public function grabKeyFromUrl($url)
    {
        $keyParamName = '/' . BackendUrlInterface::SECRET_KEY_PARAM_NAME . '/';
        
        if (strpos($url, $keyParamName) === false) {
            return '';
        }
        
        $url = rtrim($url, '/');
        $key = explode($keyParamName, $url)[1];
        
        return $key;
    }
    
    /**
     * @return string
     */
    public function createRedirectUrl()
    {
        return $this->buildUrl(static::TINYCDN_CDN_REDIRECT_URL, false);
    }
    
    /**
     * @param bool $stripKey
     *
     * @return bool|string
     */
    public function createAuthorizeUrl($stripKey = false)
    {
        $url = $this->buildUrl(static::TINYCDN_CDN_AUTHORIZE_URL);
        
        if ($stripKey) {
            $url = $this->stripKey($url);
        }
        
        return $url;
    }
    
    /**
     * @return string
     */
    public function createConnectUrl()
    {
        return $this->buildUrl(static::TINYCDN_CDN_CONNECT_URL);
    }
}
