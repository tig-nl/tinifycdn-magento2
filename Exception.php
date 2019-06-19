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

namespace TIG\TinyCDN;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\PhraseFactory;

class Exception extends LocalizedException
{
    /**
     * Exception constructor.
     *
     * @param PhraseFactory $phraseFactory
     */
    public function __construct(
        Phrase $phrase,
        PhraseFactory $phraseFactory
    ) {
        $this->phrase = $phraseFactory;
        parent::__construct(
            $phrase
        );
    }
    
    /**
     * @param $message
     *
     * @return Phrase
     */
    private function createPhrase($message)
    {
        return $this->phrase->create($message);
    }
    
    /**
     * @param string $message
     *
     * @throws LocalizedException
     */
    public function throwException(string $message)
    {
        $exception = $this->createPhrase($message);
        
        throw new LocalizedException($exception);
    }
}
