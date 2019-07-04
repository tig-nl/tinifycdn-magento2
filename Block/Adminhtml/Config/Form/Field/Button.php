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
use TIG\TinyCDN\Model\Config\Source\Url;

class Button extends Field
{
    const BUTTON_ID = 'tinify_cdn_connect';

    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_TinyCDN::config/form/button.phtml';

    /**
     * Button constructor.
     *
     * @param Context $context
     * @param array   $data
     */
    // @codingStandardsIgnoreLine
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
    public function getButtonHtml()
    {
        $layout = $this->getLayout();

        /** @var \Magento\Backend\Block\Widget\Button $button */
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
     * Builds URL to Connect controller and adds current scope and id as params.
     *
     * @return string
     */
    public function getConnectUrl()
    {
        $params      = $this->getRequest()->getParams();
        $storeParams = $this->createRequiredParams($params);

        return $this->getUrl(Url::TINYCDN_CDN_CONNECT_URL, $storeParams);
    }

    /**
     * @param array $params
     * @param array $requiredKeys
     *
     * @return array
     */
    private function createRequiredParams(array $params, $requiredKeys = ['website', 'store'])
    {
        $requiredParams = ['scope' => 'default', 'id' => 0];

        $available = array_filter($params, function ($key) use ($requiredKeys) {
            return in_array($key, $requiredKeys);
        }, ARRAY_FILTER_USE_KEY);

        if ($available) {
            $requiredParams['scope'] = key($available);
            $requiredParams['id']    = $available[key($available)];
        }

        return $requiredParams;
    }
}
