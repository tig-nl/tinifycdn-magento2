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

use Magento\Backend\App\Action\Context;
use Magento\Framework\Session\SessionManagerInterface;
use TIG\TinyCDN\Client\Provider\TinifyProvider;
use TIG\TinyCDN\Client\Provider\TinifyProviderFactory;
use TIG\TinyCDN\Controller\Adminhtml\AbstractAdminhtmlController;
use TIG\TinyCDN\Model\Api\Purge as Api;
use TIG\TinyCDN\Model\Config\Provider\CDN\Configuration;

class Purge extends AbstractAdminhtmlController
{
    /** @var Api $api */
    private $api;

    /** @var $scope */
    private $scope;

    /** @var $storeId */
    private $storeId;

    /**
     * Purge constructor.
     *
     * @param Context                 $context
     * @param SessionManagerInterface $session
     * @param Configuration           $config
     * @param TinifyProviderFactory   $tinifyFactory
     * @param Api                     $api
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $session,
        Configuration $config,
        TinifyProviderFactory $tinifyFactory,
        Api $api
    ) {
        $this->api = $api;
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
        $this->scope   = $this->getRequest()->getParam('scope');
        $this->storeId = $this->getRequest()->getParam('id');

        $redirect = $this->resultRedirectFactory->create();
        $redirect->setPath(static::SYSTEM_CONFIG_TIG_TINYCDN_SECTION, [$this->scope => $this->storeId]);

        if (!$this->storeId) {
            return $redirect;
        }

        $config = $this->getConfig();
        $siteId = $config->getSiteId($this->storeId);

        $result = $this->api->purge($siteId);

        if ($result['status'] !== 200) {
            $this->messageManager->addErrorMessage(
                sprintf(__('Site CDN couldn\'t be purged. Something went wrong. Error-code: %s'), $result['status'])
            );
        }

        $this->messageManager->addSuccessMessage(__('Site CDN successfully purged.'));

        return $redirect;
    }
}
