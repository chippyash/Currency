<?php

declare(strict_types=1);

/**
 * Currency Support
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Currency;

/**
 * A currency object
 *
 * Currency values are stored internally as integer values using int
 * This is to ensure that we don't lose precision when manipulating them
 * arithmetically
 */
class Currency implements CurrencyInterface
{
    /**
     * @var int
     */
    protected $value;

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $symbol;

    /**
     * Currency long name
     * If not set, will default to code
     *
     * @var string
     */
    protected $name;

    /**
     * Display format for currency ,allows user formatting modification
     * Defaults to '%s' if not passed in construction
     *
     * @see display()
     * @var string
     */
    protected $displayFormat;

    /**
     * Locale for displaying currency
     * @var string
     */
    protected $locale;

    /**
     * Digits of precision for this currency
     * Defaults to 2 if not passed in construction
     *
     * @var int
     */
    protected $precision;

    /**
     * Constructor
     * Will set locale to current default locale
     *
     * @param float $value Value of currency
     * @param string $code Code of currency
     * @param string $symbol Symbol for currency
     * @param int $precision number of digits of precision (or exponent) for this currency
     * @param string $name Long name of currency
     * @param string $displayFormat additional user defined display format for currency
     */
    public function __construct(float $value, string $code, string $symbol, ?int $precision = 2, ?string $name = null, ?string $displayFormat = '%s')
    {
        $this->setPrecision($precision)
            ->setAsFloat($value)
            ->setCode($code)
            ->setSymbol($symbol)
            ->setDisplayFormat($displayFormat)
            ->setLocale(locale_get_default());

        if (!is_null($name)) {
            $this->setName($name);
        }
    }

    /**
     * Return currency symbol
     * @return string
     */
    public function getSymbol(): string
    {
        return $this->symbol;
    }

    /**
     * Set currency symbol
     *
     * @param string $symbol
     * @return CurrencyInterface
     */
    public function setSymbol(string $symbol): CurrencyInterface
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * Return code of this currency
     *
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * Set currency code
     *
     * @param string $code
     * @return CurrencyInterface
     */
    public function setCode(string $code): CurrencyInterface
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Set long name for the currency
     *
     * @param string $name
     * @return CurrencyInterface
     */
    public function setName(string $name): CurrencyInterface
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Return currency long name.
     * Will return currency code if not set
     *
     * @return string
     */
    public function getName(): string
    {
        if (is_null($this->name)) {
            return $this->code;
        }

        return $this->name;
    }

    /**
     * Set user defined display format for use in display()
     * NB. sprintf formatter
     * Default is '%s'
     *
     * @param string $displayFormat
     * @return CurrencyInterface
     */
    public function setDisplayFormat(string $displayFormat): CurrencyInterface
    {
        $this->displayFormat = $displayFormat;
        return $this;
    }

    /**
     * Set locale for display purposes
     *
     * @param string $locale
     * @return CurrencyInterface
     */
    public function setLocale(string $locale): CurrencyInterface
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Set number of digits of precision for the currency
     * @param int $precision
     * @return CurrencyInterface
     */
    public function setPrecision(int $precision): CurrencyInterface
    {
        $this->precision = $precision;
        return $this;
    }

    /**
     * Return the precision
     *
     * @return integer
     */
    public function getPrecision(): int
    {
        return $this->precision;
    }

    /**
     * Set currency value, upscaling into an integer for internal storage
     *
     * @param float $value
     *
     * @return CurrencyInterface
     * @throws \InvalidArgumentException
     */
    public function setAsFloat($value): CurrencyInterface
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException('value is not numeric');
        }
        $val = floatval($value);
        $this->value =  intval(pow(10, $this->precision) * $val);

        return $this;
    }

    /**
     * return currency value as downscaled float value
     * @return float
     */
    public function getAsFloat(): float
    {
        return $this->value / pow(10, $this->precision);
    }

    /**
     * @param int $value
     * @return CurrencyInterface
     */
    public function setValue(int $value): CurrencyInterface
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Return currency amount formatted for display
     *
     * @return string
     */
    public function display(): string
    {
        $formatter = new \NumberFormatter($this->locale, \NumberFormatter::CURRENCY);
        $formatter->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, $this->symbol);
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $this->precision);

        $value = sprintf($this->displayFormat, $formatter->format($this->getAsFloat()));
        return $this->replaceNbspWithSpace($value);
    }

    public function __toString(): string
    {
        return (string) $this->getValue();
    }

    protected function replaceNbspWithSpace($content){
        $string = htmlentities($content, ENT_NOQUOTES, 'utf-8');
        $content = str_replace("&nbsp;", " ", $string);
        $content = html_entity_decode($content);
        return $content;
    }
}
