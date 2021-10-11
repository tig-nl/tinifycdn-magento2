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

namespace Tinify\TinifyCDN\Block\Adminhtml\Config\Form\Field\Button;

use Magento\Backend\Block\Template\Context;
use Tinify\TinifyCDN\Block\Adminhtml\Config\Form\Field\AbstractButton;
use Tinify\TinifyCDN\Model\Config\Source\Cdn\Url;

class Connect extends AbstractButton
{
    const BUTTON_ID = 'tinify_cdn_connect';

    // @codingStandardsIgnoreLine
    protected $_template = 'Tinify_TinifyCDN::config/form/button/connect.phtml';

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

        return $this->getUrl(Url::TINIFYCDN_CDN_CONNECT_URL, $storeParams);
    }
}
