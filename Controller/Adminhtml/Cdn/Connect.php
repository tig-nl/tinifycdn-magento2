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

namespace Tinify\TinifyCDN\Controller\Adminhtml\Cdn;

use Magento\Backend\App\Action;
use Magento\Framework\Session\SessionManagerInterface;
use Tinify\TinifyCDN\Client\Provider\TinifyProvider;
use Tinify\TinifyCDN\Client\Provider\TinifyProviderFactory;
use Tinify\TinifyCDN\Controller\Adminhtml\AbstractAdminhtmlController;
use Tinify\TinifyCDN\Model\Config\Provider\CDN\Configuration;
use Tinify\TinifyCDN\Model\Config\Source\Cdn\Url;

class Connect extends AbstractAdminhtmlController
{
    /** @var Url $urlBuilder */
    private $urlBuilder;

    /**
     * Connect constructor.
     *
     * @param Action\Context          $context
     * @param SessionManagerInterface $session
     * @param Configuration           $config
     * @param TinifyProviderFactory   $tinifyFactory
     * @param Url                     $urlBuilder
     */
    public function __construct(
        Action\Context $context,
        SessionManagerInterface $session,
        Configuration $config,
        TinifyProviderFactory $tinifyFactory,
        Url $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct(
            $context,
            $session,
            $config,
            $tinifyFactory
        );
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $provider = $this->createTinifyProviderInstance();
        /**
         * We need to grab the Key from the current URL, because otherwise Magento 2 will auto-
         * generate a wrong key later on which will not pass validation.
         */
        $formKey = $this->urlBuilder->grabKeyFromUrl($this->urlBuilder->createAuthorizeUrl());
        $scopeId = $this->getRequest()->getParam('id');
        $scope   = $this->getRequest()->getParam('scope');

        $this->addSessionData('id', $scopeId);
        $this->addSessionData('scope', $scope);
        $authUrl = $provider->getAuthorizationUrl(['state' => $formKey]);

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath($authUrl);

        return $redirect;
    }
}
