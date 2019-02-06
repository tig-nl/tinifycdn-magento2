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

namespace TIG\TinyCDN\Block\Adminhtml\Config\Support;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use TIG\TinyCDN\Config\Provider\ModuleConfiguration;

class Tab extends Template implements RendererInterface
{
    const MODULE_NAME = 'TIG_TinyCDN';
    const EXTENTION_VERSION = '1.0.0';

    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_TinyCDN::config/support/tab.phtml';

    /**
     * @var ModuleConfiguration
     */

    private $moduleConfiguration;
    /**
     * Tab constructor.
     *
     * @param Template\Context    $context
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $data
     */
    public function __construct(
        Template\Context $context,
        ModuleConfiguration $moduleConfiguration,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleConfiguration = $moduleConfiguration;
    }
    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $this->setElement($element);
        return $this->toHtml();
    }
    /**
     * Retrieve the version number from the database.
     *
     * @return bool|false|string
     */
    public function getVersionNumber()
    {
        return static::EXTENTION_VERSION;
    }
    /**
     * @return string
     */
    public function getSupportedMagentoVersions()
    {
        return $this->moduleConfiguration->getSupportedMagentoVersions();
    }
}
