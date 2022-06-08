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

namespace Tinify\TinifyCDN\Model\Api;

use Tinify\TinifyCDN\Model\AbstractApi;

class Purge extends AbstractApi
{
    const TINIFY_API_CDN_PURGE = 'cdn/sites/{id}/purge';

    /**
     * @param int $id
     *
     * @return mixed
     */
    public function purge($id)
    {
        return $this->doPostRequest($id);
    }

    /**
     * @param array|string|string[] $id
     *
     * @return array
     */
    private function doPostRequest($id)
    {
        $uri = str_replace('{id}', $id, self::TINIFY_API_CDN_PURGE);

        return $this->call($uri, 'post', true);
    }
}
