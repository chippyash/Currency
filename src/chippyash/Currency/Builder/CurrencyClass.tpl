<?php
/**
 * chippyash/Currency: hard currency
 * Created by generate-currency-class.php

 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
namespace <namespace>;

use chippyash\Currency\Currency;
use chippyash\Type\String\StringType;

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
        $this->setLocale(new StringType('<locale>'));
    }
}