<?php

declare(strict_types=1);

/**
 * Currency Support

 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Currency;

/**
 * A currency object interface
 */
interface CurrencyInterface
{
    /**
     * Return currency symbol
     * @return string
     */
    public function getSymbol(): string;

    /**
     * Set currency symbol
     *
     * @param string $symbol
     * @return CurrencyInterface
     */
    public function setSymbol(string $symbol): CurrencyInterface;

    /**
     * Return name of this currency
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Set display format for use in display()
     * NB. sprintf formatter
     *
     * @param string $displayFormat
     * @return CurrencyInterface
     */
    public function setDisplayFormat(string $displayFormat): CurrencyInterface;

    /**
     * Set locale for display purposes
     * By default the locale is same as locale_get_default()
     *
     * @param string $locale
     * @return CurrencyInterface
     */
    public function setLocale(string $locale): CurrencyInterface;

    /**
     * Set number of digits of precision for the currency
     * @param int $precision
     * @return CurrencyInterface
     */
    public function setPrecision(int $precision): CurrencyInterface;

    /**
     * @return int
     */
    public function getValue(): int;

    /**
     * @param int $value
     * @return CurrencyInterface
     */
    public function setValue(int $value): CurrencyInterface;

    /**
     * Set currency value, upscaling into an integer for internal storage
     *
     * @param float $value
     *
     * @return CurrencyInterface
     * @throws \InvalidArgumentException
     */
    public function setAsFloat($value): CurrencyInterface;

    /**
     * return currency value as downscaled float value
     * @return float
     */
    public function getAsFloat(): float;

    /**
     * Return currency amount formatted for display
     * according to i18n localisation rules
     *
     * @return string
     */
    public function display(): string;
}
