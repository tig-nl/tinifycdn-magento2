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

namespace TIG\TinyCDN\Controller\Adminhtml;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use TIG\TinyCDN\Model\Config\Provider\CDN\Configuration;
use Tinify\OAuth2\Client\Provider\TinifyProvider;
use Tinify\OAuth2\Client\Provider\TinifyProviderFactory;

abstract class AbstractAdminhtmlController extends Action
{
    /** @var Configuration $config */
    private $config;
    
    /**
     * AbstractAdminhtmlController constructor.
     *
     * @param Context               $context
     * @param Configuration         $config
     * @param TinifyProviderFactory $tinifyFactory
     */
    public function __construct(
        Context $context,
        Configuration $config,
        TinifyProviderFactory $tinifyFactory
    ) {
        $this->config = $config;
        $this->tinifyFactory = $tinifyFactory;
        parent::__construct($context);
    }
    
    /**
     * @return TinifyProvider;
     */
    public function createTinifyFactory()
    {
        $credentials = $this->config->formatCredentials();
        
        return $this->tinifyFactory->create(['options' => $credentials]);
    }
}
