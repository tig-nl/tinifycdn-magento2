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

namespace Tinify\TinifyCDN\Model;

use Magento\Framework\App\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Framework\App\RequestInterface;

abstract class AbstractConfigProvider extends AbstractModel
{
    /** @var ScopeConfig $scopeConfig */
    private $scopeConfig;

    /** @var RequestInterface $request */
    private $request;

    /**
     * AbstractConfigProvider constructor.
     *
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param ScopeConfig                                                  $scopeConfig
     * @param RequestInterface                                             $request
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        ScopeConfig $scopeConfig,
        RequestInterface $request,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->request     = $request;
        parent::__construct($context, $registry, $resource, $resourceCollection);
    }

    /**
     * @param string    $path
     * @param null      $storeId
     *
     * @return mixed
     */
    public function getConfigValue(string $path, $storeId = null)
    {
        return $this->scopeConfig->getValue($path, $this->resolveScope(), $storeId);
    }

    /**
     * Since configuration values use the plural form of a scope (e.g. 'websites' instead of
     * 'website') we use the request's params to resolve the singular form to a plural form.
     *
     * @return string
     */
    private function resolveScope()
    {
        $scopes = ['store', 'website'];
        $params = $this->request->getParams();

        $scope = array_filter($params, function ($key) use ($scopes) {
            return in_array($key, $scopes);
        }, ARRAY_FILTER_USE_KEY);

        if (empty($scope)) {
            return ScopeInterface::SCOPE_DEFAULT;
        }

        $scope = key($scope) . 's'; // Make string plural.

        return $scope;
    }
}
