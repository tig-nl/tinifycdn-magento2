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

namespace Tinify\TinifyCDN;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\LocalizedExceptionFactory;
use Magento\Framework\Phrase;
use Magento\Framework\PhraseFactory;

class Exception
{
    /** @var LocalizedExceptionFactory $exception */
    private $exception;

    /** @var PhraseFactory $phrase */
    private $phrase;

    /**
     * Exception constructor.
     *
     * @param LocalizedExceptionFactory $exceptionFactory
     * @param PhraseFactory             $phraseFactory
     */
    public function __construct(
        LocalizedExceptionFactory $exceptionFactory,
        PhraseFactory $phraseFactory
    ) {
        $this->exception = $exceptionFactory;
        $this->phrase = $phraseFactory;
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
     * @return LocalizedException
     */
    public function throwException(string $message)
    {
        $exception = $this->createPhrase($message);

        return $this->exception->create($exception);
    }
}
