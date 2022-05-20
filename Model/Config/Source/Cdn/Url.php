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

namespace Tinify\TinifyCDN\Model\Config\Source\Cdn;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\UrlInterface as StandardUrlInterface;
use Tinify\TinifyCDN\Model\AbstractConfigSource;

class Url extends AbstractConfigSource
{
    const TINIFYCDN_CDN_AUTHORIZE_URL = 'tinify/cdn/authorize';

    const TINIFYCDN_CDN_REDIRECT_URL  = 'tinify/cdn/redirect';

    const TINIFYCDN_CDN_CONNECT_URL   = 'tinify/cdn/connect';

    const TINIFYCDN_CDN_PURGE_URL     = 'tinify/cdn/purge';

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
     * The backendUrlBuilder-class always includes the admin-URI and needed
     * parameters when building URLs.
     *
     * @param      $uri
     * @param $admin bool
     * @param null $params
     *
     * @return string
     */
    private function buildUrl($uri, bool $admin = true, $params = null)
    {
        if ($admin) {
            return $this->backendUrlBuilder->getUrl($uri, $params);
        }

        return $this->standardUrlBuilder->getDirectUrl($uri, $params);
    }

    /**
     * Custom function to grab the key from the referring URL. Magento creates a
     * new form key upon each request. But Tinify CDN needs the key to stay intact
     * until after referral.
     *
     * @param $url string
     *
     * @return bool|string
     */
    public function grabKeyFromUrl(string $url)
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
        return $this->buildUrl(static::TINIFYCDN_CDN_REDIRECT_URL, false);
    }

    /**
     * @param array|null $params
     *
     * @return string
     */
    public function createAuthorizeUrl(array $params = null)
    {
        return $this->buildUrl(static::TINIFYCDN_CDN_AUTHORIZE_URL, true, $params);
    }

    /**
     * @return string
     */
    public function createConnectUrl()
    {
        return $this->buildUrl(static::TINIFYCDN_CDN_CONNECT_URL);
    }

    /**
     * @param array|null $params
     *
     * @return string
     */
    public function createPurgeUrl(array $params = null)
    {
        return $this->buildUrl(static::TINIFYCDN_CDN_PURGE_URL, true, $params);
    }
}
