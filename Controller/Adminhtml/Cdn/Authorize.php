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
use Magento\Framework\Message\ManagerInterface;
use TIG\TinyCDN\Controller\Adminhtml\AbstractAdminhtmlController;
use TIG\TinyCDN\Exception;
use TIG\TinyCDN\Model\Config\Provider\CDN\Configuration;
use Tinify\OAuth2\Client\Provider\TinifyProviderFactory;

class Authorize extends AbstractAdminhtmlController
{
    /** @var ConfigWriter $configWriter */
    private $configWriter;
    
    /** @var Exception $exception */
    private $exception;
    
    /**
     * Authorize constructor.
     *
     * @param Context               $context
     * @param ManagerInterface      $messageManager
     * @param Configuration         $config
     * @param TinifyProviderFactory $tinifyFactory
     * @param ConfigWriter          $configWriter
     * @param Exception             $exception
     */
    public function __construct(
        Context $context,
        ManagerInterface $messageManager,
        Configuration $config,
        TinifyProviderFactory $tinifyFactory,
        ConfigWriter $configWriter,
        Exception $exception
    ) {
        $this->messageManager = $messageManager;
        $this->configWriter   = $configWriter;
        $this->exception      = $exception;
        parent::__construct(
            $context,
            $config,
            $tinifyFactory
        );
    }
    
    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $provider = $this->createTinifyFactory();
        $authCode = $this->getRequest()->getParam('code');
        
        if (!$authCode) {
            return $this->exception->throwException('No authorization code provided. Direct access not allowed.');
        }
    
        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath('adminhtml/system_config/edit/section/tig_tinycdn');
        
        try {
            $accessToken = $provider->getAccessToken(
                'authorization_code',
                ['code' => $authCode]
            );
            $this->configWriter->saveConfig(Configuration::TINYCDN_CDN_ACCESS_TOKEN, $accessToken);
        } catch (IdentityProviderException $error) {
            $this->messageManager->addErrorMessage('An error occurred: ' . $error->getMessage());
            
            return $redirect;
        }
        
        $this->messageManager->addSuccessMessage('Your access token was saved successfully.');
        
        return $redirect;
    }
}
