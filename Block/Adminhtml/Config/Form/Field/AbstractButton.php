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

namespace Tinify\TinifyCDN\Block\Adminhtml\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

abstract class AbstractButton extends Field
{
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
     * @return mixed
     */
    abstract public function getButtonHtml();

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
     * @param array $params
     * @param array $requiredKeys
     *
     * @return array
     */
    public function createRequiredParams(array $params, $requiredKeys = ['website', 'store'])
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
