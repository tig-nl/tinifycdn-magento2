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

use \Magento\Backend\Block\Template\Context;
use \Magento\Config\Block\System\Config\Form\Field;
use \Magento\Framework\Data\Form\Element\AbstractElement;

class Button extends Field
{
    const BUTTON_ID = 'tig_tinycdn_connect';
    
    /** @var string $_template */
    protected $_template = 'TIG_TinyCDN::config/button/button.phtml';
    
    /**
     * Button constructor.
     *
     * @param Context $context
     * @param array   $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
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
        
        return parent::render($element);
    }
    
    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function _getElementHtml(AbstractElement $element)
    {
        return $this->_toHtml();
    }
    
    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getButtonHtml()
    {
        $layout = $this->getLayout();
        
        $button = $layout->createBlock(
            'Magento\Backend\Block\Widget\Button'
        );
        
        $button->setData(
            [
                'id'    => static::BUTTON_ID,
                'label' => __('Connect to your Tinify account')
            ]
        );
        
        return $button->toHtml();
    }
    
    /**
     * @return string
     */
    public function getConnectUrl()
    {
        return $this->getUrl('*/tinycdn/connect');
    }
    
    /**
     * @return string
     */
    public function getButtonId()
    {
        return static::BUTTON_ID;
    }
}
