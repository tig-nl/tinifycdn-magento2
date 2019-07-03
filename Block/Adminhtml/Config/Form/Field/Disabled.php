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

namespace TIG\TinyCDN\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use TIG\TinyCDN\Model\Config\Provider\CDN\Configuration;

class Disabled extends Field
{
    const BUTTON_ID = 'tinify_cdn_site';

    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_TinyCDN::config/form/disabled.phtml';

    /** @var Configuration $config */
    private $config;

    /**
     * Button constructor.
     *
     * @param Context $context
     * @param array   $data
     */
    // @codingStandardsIgnoreLine
    public function __construct(
        Context $context,
        Configuration $config,
        array $data = []
    ) {
        $this->config = $config;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope();
        $element->unsCanUseWebsiteValue();
        $element->unsCanUseDefaultValue();
        $element->unsCanRestoreToDefault();

        return parent::render($element);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    public function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFieldHtml()
    {
        $layout = $this->getLayout();

        /** @var \Magento\Framework\View\Element\Text $field */
        $field = $layout->createBlock(
            'Magento\Framework\View\Element\Text'
        );

        $field->setText($this->config->getCdnEndpoint());

        return $field->toHtml();
    }
}
