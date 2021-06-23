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

namespace TIG\TinifyCDN\Controller\Adminhtml\Cdn;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Magento\Backend\App\Action\Context;
use Magento\Config\Model\ResourceModel\Config as ConfigWriter;
use Magento\Framework\App\ScopeInterface as FrameworkScopeInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;
use TIG\TinifyCDN\Client\Provider\TinifyProvider;
use TIG\TinifyCDN\Client\Provider\TinifyProviderFactory;
use TIG\TinifyCDN\Controller\Adminhtml\AbstractAdminhtmlController;
use TIG\TinifyCDN\Model\Api\Site;
use TIG\TinifyCDN\Model\Config\Provider\CDN\Configuration;
use TIG\TinifyCDN\Model\Config\Provider\General\Configuration as GeneralConfiguration;

class Authorize extends AbstractAdminhtmlController
{
    /** @var GeneralConfiguration $generalConfig */
    private $generalConfig;

    /** @var ConfigWriter $configWriter */
    private $configWriter;

    /** @var Site $site */
    private $site;

    /** @var string */
    private $scope;

    /** @var int */
    private $storeId;

    /**
     * Authorize constructor.
     *
     * @param Context                 $context
     * @param SessionManagerInterface $session
     * @param Configuration           $config
     * @param GeneralConfiguration    $generalConfig
     * @param TinifyProviderFactory   $tinifyFactory
     * @param ConfigWriter            $configWriter
     * @param Site                    $site
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $session,
        Configuration $config,
        GeneralConfiguration $generalConfig,
        TinifyProviderFactory $tinifyFactory,
        ConfigWriter $configWriter,
        Site $site
    ) {
        $this->generalConfig  = $generalConfig;
        $this->configWriter   = $configWriter;
        $this->site           = $site;
        parent::__construct(
            $context,
            $session,
            $config,
            $tinifyFactory
        );
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $provider      = $this->createTinifyProviderInstance();
        $authCode      = $this->getRequest()->getParam('code');
        $this->storeId = $this->getSessionData('id');
        $this->scope   = $this->getSessionData('scope');

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath(static::SYSTEM_CONFIG_TIG_TINIFYCDN_SECTION, [$this->scope => $this->storeId]);

        if (!$authCode) {
            $this->messageManager->addErrorMessage(
                __('No authorization code provided. Direct access not allowed.')
            );

            return $redirect;
        }

        $this->saveAccessToken($provider, $authCode);
        $this->scope = $this->resolveScope($this->scope);
        $site = $this->site->fetchSite($this->storeId);

        if (!$site) {
            $this->messageManager->addErrorMessage(
                __('Site not found. Did you select the correct Site URL?')
            );

            return $redirect;
        }

        try {
            $this->saveEndpoint($site);
            $this->saveSiteId($site);
        } catch (\Exception $error) {
            $this->messageManager->addErrorMessage($error->getMessage());
        }

        $this->unsetSessionData('id');
        $this->unsetSessionData('scope');

        return $redirect;
    }

    /**
     * @param $scope
     *
     * @return string
     */
    private function resolveScope($scope)
    {
        switch ($scope) {
            case 'website':
                return StoreScopeInterface::SCOPE_WEBSITES;
            case 'store':
                return StoreScopeInterface::SCOPE_STORES;
            default:
                return FrameworkScopeInterface::SCOPE_DEFAULT;
        }
    }

    /**
     * @param TinifyProvider $provider
     * @param                $authCode
     *
     * @return \Magento\Framework\Message\ManagerInterface
     */
    private function saveAccessToken(TinifyProvider $provider, $authCode)
    {
        try {
            $accessToken = $provider->getAccessToken('authorization_code', ['code' => $authCode]);
            $this->configWriter->saveConfig(
                Configuration::XPATH_TINIFYCDN_CDN_ACCESS_TOKEN,
                $accessToken,
                $this->scope,
                $this->storeId
            );
        } catch (IdentityProviderException $error) {
            return $this->messageManager->addErrorMessage($error->getMessage());
        }

        // If Authorization is successful, remove oAuth Credentials from session.
        $this->unsetSessionData(static::TINIFYCDN_OAUTH_CREDENTIALS_PARAM);
    }

    /**
     * @param $currentSite
     *
     * @return \Magento\Framework\Message\ManagerInterface
     */
    private function saveEndpoint($currentSite)
    {
        try {
            $this->configWriter->saveConfig(
                Configuration::XPATH_TINIFYCDN_CDN_ENDPOINT,
                $this->retrieveEndpoint($currentSite),
                $this->scope,
                $this->storeId
            );
        } catch (\Exception $error) {
            return $this->messageManager->addErrorMessage($error->getMessage());
        }

        if (!$this->generalConfig->isEnabled()) {
            $this->messageManager->addNoticeMessage(__('Extension is currently disabled.'));
        }

        $this->messageManager->addNoticeMessage(__('Don\'t forget to save your configuration!'));

        return $this->messageManager->addSuccessMessage(__('Your TinifyCDN endpoint was successfully set.'));
    }

    /**
     * @param $currentSite
     *
     * @return \Magento\Framework\Message\ManagerInterface
     */
    private function saveSiteId($currentSite)
    {
        try {
            $this->configWriter->saveConfig(
                Configuration::XPATH_TINIFYCDN_CDN_SITE_ID,
                $this->retrieveSiteId($currentSite),
                $this->scope,
                $this->storeId
            );
        } catch (\Exception $error) {
            return $this->messageManager->addErrorMessage($error->getMessage());
        }
    }

    /**
     * @param $site
     *
     * @return |null
     */
    private function retrieveEndpoint($site)
    {
        $endpoint = isset($site) ? $site->endpoint : null;

        if (!$endpoint) {
            $this->messageManager->addErrorMessage(
                __('No endpoint found for this store. Are you sure it\'s configured in your TinifyCDN account?')
            );
        }

        // The returned endpoint could be missing a trailing slash which is required in the base_url.
        if (substr($endpoint, -1) !== '/') {
            $endpoint .= '/';
        }

        return $endpoint;
    }

    /**
     * @param $site
     *
     * @return |null
     */
    private function retrieveSiteId($site)
    {
        $siteId = isset($site) ? $site->id : null;

        if (!$siteId) {
            $this->messageManager->addErrorMessage(
                __('No site ID found for this Store View. Are you sure it\'s configured in your TinifyCDN account?')
            );
        }

        return $siteId;
    }
}
