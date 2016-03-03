<?php
/**
 * Currency Support
 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Currency;

use Chippyash\Type\String\StringType;
use Chippyash\Type\Number\IntType;

/**
 * A currency object interface
 */
interface CurrencyInterface
{
    /**
     * Return currency symbol
     * @return StringType
     */
    public function getSymbol();

    /**
     * Set currency symbol
     *
     * @param StringType $symbol
     * @return Fluent Interface
     */
    public function setSymbol(StringType $symbol);

    /**
     * Return name of this currency
     *
     * @return StringType
     */
    public function getName();

    /**
     * Set display format for use in display()
     * NB. sprintf formatter
     *
     * @param StringType $displayFormat
     * @return Fluent Interface
     */
    public function setDisplayFormat(StringType $displayFormat);

    /**
     * Set locale for display purposes
     * By default the locale is same as locale_get_default()
     *
     * @param StringType $locale
     * @return Fluent Interface
     */
    public function setLocale(StringType $locale);

    /**
     * Set number of digits of precision for the currency
     * @param IntType $precision
     * @return Fluent Interface
     */
    public function setPrecision(IntType $precision);

    /**
     * Return currency amount formatted for display
     * according to i18n localisation rules
     *
     * @return StringType
     */
    public function display();
}