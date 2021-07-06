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

namespace TIG\TinifyCDN\Model;

/**
 * Class Challenge
 *
 * This class provides the necessary methods to create a challenge through which Tinify CDN and Magento
 * 2 can verify the validity of an authentication session.
 *
 * @package TIG\TinifyCDN\Model
 */
class Challenge extends AbstractModel
{
    /**
     * @return string
     */
    public function generateChallenge($verifier)
    {
        return $this->encode(pack('H*', hash('sha256', $verifier)));
    }

    /**
     * @param $randomValue
     *
     * @return string
     */
    public function generateVerifier($randomValue)
    {
        return $this->encode(pack('H*', $randomValue));
    }

    /**
     * @return string
     */
    public function generateRandomValue()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    /**
     * @param $value
     *
     * @return string
     */
    private function encode($value)
    {
        $base64    = base64_encode($value);
        $base64    = trim($base64, "=");
        $base64url = strtr($base64, '+/', '-_');

        return ($base64url);
    }
}
