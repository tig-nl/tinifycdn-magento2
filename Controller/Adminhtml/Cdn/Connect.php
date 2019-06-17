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

use Magento\Backend\App\Action;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Session\SessionManagerInterface as SessionManager;
use TIG\TinyCDN\Model\Config\Provider\CDN\Configuration;
use Tinify\OAuth2\Client\Provider\TinifyProvider;
use Tinify\OAuth2\Client\Provider\TinifyProviderFactory;

class Connect extends Action
{
    /** @var SessionManager $session */
    private $session;
    
    /** @var Configuration */
    private $config;
    
    /** @var TinifyProviderFactory */
    private $tinifyFactory;
    
    /**
     * Connect constructor.
     *
     * @param Configuration  $config
     * @param Action\Context $context
     */
    // @codingStandardsIgnoreLine
    public function __construct(
        SessionManager $sessionManager,
        Configuration $config,
        TinifyProviderFactory $tinifyFactory,
        Action\Context $context
    ) {
        $this->session       = $sessionManager;
        $this->config        = $config;
        $this->tinifyFactory = $tinifyFactory;
        parent::__construct(
            $context
        );
    }
    
    /**
     * TODO: Check given state against previously stored one to mitigate CSRF attack.
     *       Try to get an access token using the authorization code grant.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $credentials = $this->config->formatCredentials();
        $provider    = $this->tinifyFactory->create(['options' => $credentials]);
        
        // Fetch the authorization URL from the provider; this returns the
        // urlAuthorize option and generates and applies any necessary parameters
        // (e.g. state).
        $authUrl  = $provider->getAuthorizationUrl();
        $redirect = $this->resultRedirectFactory->create();
        
        // Get the state generated for you and store it to the session.
        $this->session->setData('oauth2state', $provider->getState());
        $redirect->setPath($authUrl);
        
        return $redirect;
    }
}
