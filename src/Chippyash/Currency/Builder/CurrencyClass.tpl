<?php
/**
 * Chippyash/Currency: hard currency
 * Created by generate-currency-class.php

 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
namespace <namespace>;

use Chippyash\Currency\Currency;

/**
 * Hard currency: <name>
 */
class <code> extends Currency
{
    /**
     * Construct the currency
     */
    public function __construct()
    {
        parent::__construct(<value>, '<code>', '<symbol>', <precision>, '<name>', '<displayFormat>');
        $this->setLocale('<locale>');
    }
}