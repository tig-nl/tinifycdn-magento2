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

namespace TIG\TinyCDN\Model\Config\Provider\CDN;

use TIG\TinyCDN\Model\AbstractConfigProvider;
use TIG\TinyCDN\Model\Challenge;
use TIG\TinyCDN\Model\Config\Provider\General\Configuration as GeneralConfiguration;
use TIG\TinyCDN\Model\Config\Source\Url;

class Configuration extends AbstractConfigProvider
{
    const XPATH_TINYCDN_CDN_TEST         = 'tig_tinycdn/cdn/test';

    const XPATH_TINYCDN_CDN_LIVE         = 'tig_tinycdn/cdn/live';

    const XPATH_TINYCDN_CDN_ACCESS_TOKEN = 'tig_tinycdn/cdn/access_token';

    const XPATH_TINYCDN_CDN_SITE_ID      = 'tig_tinycdn/cdn/site_id';

    const XPATH_TINYCDN_CDN_ENDPOINT     = 'tig_tinycdn/cdn/endpoint';

    const TINYCDN_CDN_TOKEN_PARAM        = 'token';

    const TINYCDN_CDN_AUTH_PARAM         = 'authorization';

    /** @var Challenge $generate */
    private $challenge;

    /** @var GeneralConfiguration $generalConfig */
    private $generalConfig;

    /** @var Url $urlBuilder */
    private $urlBuilder;

    /**
     * Configuration constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     * @param Challenge                                                    $challenge
     * @param GeneralConfiguration                                         $generalConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        Challenge $challenge,
        GeneralConfiguration $generalConfig,
        Url $urlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
    ) {
        $this->challenge     = $challenge;
        $this->generalConfig = $generalConfig;
        $this->urlBuilder    = $urlBuilder;
        parent::__construct($context, $registry, $scopeConfig, $request, $resource, $resourceCollection);
    }

    /**
     * @return array
     */
    public function formatCredentials()
    {
        $credentials = $this->retrieveCredentialsForCurrentMode();
        $randomValue = $this->challenge->generateRandomValue();
        $verifier    = $this->challenge->generateVerifier($randomValue);

        return [
            'clientId'       => $credentials['client_id'],
            'codeChallenge'  => $this->challenge->generateChallenge($verifier),
            'codeVerifier'   => $verifier,
            'scopes'         => $credentials['scopes'],
            'redirectUri'    => $this->urlBuilder->createRedirectUrl(),
            'urlAuthorize'   => $credentials['url_authorize'],
            'urlAccessToken' => $this->getApiUrl(self::TINYCDN_CDN_TOKEN_PARAM)
        ];
    }

    /**
     * Retrieves credentials for the currently enabled mode. If module is disabled
     * test credentials are returned.
     *
     * @return array
     */
    public function retrieveCredentialsForCurrentMode()
    {
        if ($this->generalConfig->liveModeEnabled()) {
            return $this->getLiveCredentials();
        }

        return $this->getTestCredentials();
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    public function getApiUrl(string $uri = '')
    {
        $credentials = $this->retrieveCredentialsForCurrentMode();

        return $credentials['url_api'] . $uri;
    }

    /**
     * @return array
     */
    public function getTestCredentials()
    {
        return $this->getConfigValue(static::XPATH_TINYCDN_CDN_TEST);
    }

    /**
     * @return array
     */
    public function getLiveCredentials()
    {
        return $this->getConfigValue(static::XPATH_TINYCDN_CDN_LIVE);
    }

    /**
     * @return string
     */
    public function getAccessToken($storeId = null)
    {
        return $this->getConfigValue(static::XPATH_TINYCDN_CDN_ACCESS_TOKEN. $storeId);
    }

    /**
     * @return string
     */
    public function getCdnEndpoint($storeId = null)
    {
        return $this->getConfigValue(static::XPATH_TINYCDN_CDN_ENDPOINT, $storeId);
    }

    /**
     * @return string
     */
    public function getSiteId($storeId = null)
    {
        return $this->getConfigValue(static::XPATH_TINYCDN_CDN_SITE_ID, $storeId);
    }
}
