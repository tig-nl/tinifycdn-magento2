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
use Magento\Framework\Session\SessionManagerInterface as SessionManager;
use TIG\TinyCDN\Controller\Adminhtml\AbstractAdminhtmlController;
use TIG\TinyCDN\Model\Config\Provider\CDN\Configuration;
use TIG\TinyCDN\Model\Config\Source\Url;
use Tinify\OAuth2\Client\Provider\TinifyProviderFactory;

class Connect extends AbstractAdminhtmlController
{
    /** @var SessionManager $session */
    private $session;
    
    /** @var Url $urlBuilder */
    private $urlBuilder;
    
    /**
     * Connect constructor.
     *
     * @param SessionManager        $sessionManager
     * @param Configuration         $config
     * @param Url                   $urlBuilder
     * @param TinifyProviderFactory $tinifyFactory
     * @param Action\Context        $context
     */
    public function __construct(
        SessionManager $sessionManager,
        Configuration $config,
        Url $urlBuilder,
        TinifyProviderFactory $tinifyFactory,
        Action\Context $context
    ) {
        $this->session    = $sessionManager;
        $this->urlBuilder = $urlBuilder;
        parent::__construct(
            $context,
            $config,
            $tinifyFactory
        );
    }
    
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $provider = $this->createTinifyFactory();
        /**
         * We need to grab the Key from the current URL, because otherwise Magento 2 will auto-
         * generate a wrong key later on.
         */
        $formKey  = $this->urlBuilder->grabKeyFromUrl($this->urlBuilder->createAuthorizeUrl());
        $authUrl  = $provider->getAuthorizationUrl(['state' => $formKey]);
        $redirect = $this->resultRedirectFactory->create();
        
        // Get the state generated for you and store it to the session.
        $this->session->setData('oauth2state', $provider->getState());
        $redirect->setPath($authUrl);
        
        return $redirect;
    }
}
