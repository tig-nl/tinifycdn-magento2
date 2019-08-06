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

namespace TIG\TinyCDN\Controller\Adminhtml\Cdn;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Magento\Config\Model\ResourceModel\Config as ConfigWriter;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ScopeInterface as FrameworkScopeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Store\Model\ScopeInterface as StoreScopeInterface;
use TIG\TinyCDN\Controller\Adminhtml\AbstractAdminhtmlController;
use TIG\TinyCDN\Model\Api\Site;
use TIG\TinyCDN\Model\Config\Provider\CDN\Configuration;
use Tinify\OAuth2\Client\Provider\TinifyProvider;
use Tinify\OAuth2\Client\Provider\TinifyProviderFactory;

class Authorize extends AbstractAdminhtmlController
{
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
     * @param Context               $context
     * @param ManagerInterface      $messageManager
     * @param Configuration         $config
     * @param TinifyProviderFactory $tinifyFactory
     * @param ConfigWriter          $configWriter
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $session,
        ManagerInterface $messageManager,
        Configuration $config,
        TinifyProviderFactory $tinifyFactory,
        ConfigWriter $configWriter,
        Site $site
    ) {
        $this->messageManager = $messageManager;
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
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     * @throws LocalizedException
     */
    public function execute()
    {
        $provider      = $this->createTinifyProviderInstance();
        $authCode      = $this->getRequest()->getParam('code');
        $this->storeId = $this->getSessionData('id');
        $this->scope   = $this->getSessionData('scope');

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath(static::SYSTEM_CONFIG_TIG_TINYCDN_SECTION, [$this->scope => $this->storeId]);

        if (!$authCode) {
            $this->messageManager->addErrorMessage(
                __('No authorization code provided. Direct access not allowed.')
            );

            return $redirect;
        }

        $this->scope = $this->resolveScope($this->scope);
        $site = $this->site->fetchSite($this->storeId);

        try {
            $this->saveAccessToken($provider, $authCode);
            $this->saveEndpoint($site);
            $this->saveSiteId($site);
        } catch (\Exception $error) {
            $this->messageManager->addErrorMessage($error->getMessage());
        }

        $this->unsetSessionData('id');
        $this->unsetSessionData('scope');

        return $redirect;
    }

    private function resolveScope($scope)
    {
        if ($scope == 'website') {
            return StoreScopeInterface::SCOPE_WEBSITES;
        }

        if ($scope == 'store') {
            return StoreScopeInterface::SCOPE_STORES;
        }

        return FrameworkScopeInterface::SCOPE_DEFAULT;
    }

    /**
     * @param TinifyProvider $provider
     * @param                $authCode
     *
     * @return void|ManagerInterface
     */
    private function saveAccessToken(TinifyProvider $provider, $authCode)
    {
        try {
            $accessToken = $provider->getAccessToken('authorization_code', ['code' => $authCode]);
            $this->configWriter->saveConfig(
                Configuration::XPATH_TINYCDN_CDN_ACCESS_TOKEN,
                $accessToken,
                $this->scope,
                $this->storeId
            );
        } catch (IdentityProviderException $error) {
            return $this->messageManager->addErrorMessage($error->getMessage());
        }

        // If Authorization is successful, remove oAuth Credentials from session.
        $this->unsetSessionData(static::TINYCDN_OAUTH_CREDENTIALS_PARAM);
    }

    /**
     * @return ManagerInterface
     */
    private function saveEndpoint($currentSite)
    {
        try {
            $this->configWriter->saveConfig(
                Configuration::XPATH_TINYCDN_CDN_ENDPOINT,
                $this->retrieveEndpoint($currentSite),
                $this->scope,
                $this->storeId
            );
        } catch (\Exception $error) {
            return $this->messageManager->addErrorMessage($error->getMessage());
        }

        $this->messageManager->addNoticeMessage(__('Do not forget to save your configuration!'));

        return $this->messageManager->addSuccessMessage(__('Your TinyCDN endpoint was successfully set.'));
    }

    /**
     * @param $currentSite
     *
     * @return ManagerInterface|void
     */
    private function saveSiteId($currentSite)
    {
        try {
            $this->configWriter->saveConfig(
                Configuration::XPATH_TINYCDN_CDN_SITE_ID,
                $this->retrieveSiteId($currentSite),
                $this->scope,
                $this->storeId
            );
        } catch (\Exception $error) {
            return $this->messageManager->addErrorMessage($error->getMessage());
        }
    }

    /**
     * @return string|null
     */
    private function retrieveEndpoint($site)
    {
        $endpoint = $site->endpoint ?: null;

        if (!$endpoint) {
            $this->messageManager->addErrorMessage(
                __('No endpoint found for this store. Are you sure it\'s configured in your TinyCDN account?')
            );
        }

        return $endpoint;
    }

    /**
     * @return string|null
     */
    private function retrieveSiteId($site)
    {
        $siteId = $site->id ?: null;

        if (!$siteId) {
            $this->messageManager->addErrorMessage(
                __('No site ID found for this Store View. Are you sure it\'s configured in your TinyCDN account?')
            );
        }

        return $siteId;
    }
}
