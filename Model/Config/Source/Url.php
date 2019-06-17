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

namespace TIG\TinyCDN\Model\Config\Source;

use Magento\Framework\UrlInterface;
use TIG\TinyCDN\Model\AbstractConfigSource;

class Url extends AbstractConfigSource
{
    const TINYCDN_CDN_AUTHORIZE_URI = 'tinify/cdn/authorize';
    
    const TINYCDN_CDN_CONNECT_URL   = 'tinify/cdn/connect';
    
    /** @var UrlInterface $urlBuilder */
    private $urlBuilder;
    
    /**
     * Url constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param UrlInterface                                                 $urlBuilder
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        UrlInterface $urlBuilder,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }
    
    /**
     * @param $uri
     *
     * @return string
     */
    private function buildUrl($uri)
    {
        return $this->urlBuilder->getUrl($uri);
    }
    
    /**
     * @return string
     */
    public function createRedirectUrl()
    {
        return $this->buildUrl(static::TINYCDN_CDN_AUTHORIZE_URI);
    }
    
    /**
     * @return string
     */
    public function createConnectUrl()
    {
        return $this->buildUrl(static::TINYCDN_CDN_CONNECT_URL);
    }
}
