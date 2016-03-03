<?php
/**
 * Currency Support
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Currency;

use Chippyash\Type\Interfaces\NumericTypeInterface;
use Chippyash\Type\Number\IntType;
use Chippyash\Type\String\StringType;

/**
 * A currency object
 *
 * Currency values are stored internally as integer values using IntType
 * This is to ensure that we don't lose precision when manipulating them
 * arithmetically
 */
class Currency extends IntType implements CurrencyInterface
{
    /**
     * @var StringType
     */
    protected $code;

    /**
     * @var StringType
     */
    protected $symbol;

    /**
     * Currency long name
     * If not set, will default to code
     *
     * @var StringType
     */
    protected $name;

    /**
     * Display format for currency ,allows user formatting modification
     * Defaults to '%s' if not passed in construction
     *
     * @see display()
     * @var StringType
     */
    protected $displayFormat;

    /**
     * Locale for displaying currency
     * @var StringType
     */
    protected $locale;

    /**
     * Digits of precision for this currency
     * Defaults to 2 if not passed in construction
     *
     * @var IntType
     */
    protected $precision;

    /**
     * Constructor
     * Will set locale to current default locale
     *
     * @param numeric $value Value of currency
     * @param string $code Code of currency
     * @param string $symbol Symbol for currency
     * @param int $precision number of digits of precision (or exponent) for this currency
     * @param string $name Long name of currency
     * @param string $displayFormat additional user defined display format for currency
     */
    public function __construct($value, $code, $symbol, $precision = 2, $name = null, $displayFormat = '%s')
    {
        $this->setPrecision(new IntType($precision))
            ->setAsFloat($value)
            ->setCode(new StringType($code))
            ->setSymbol(new StringType($symbol))
            ->setDisplayFormat(new StringType($displayFormat))
            ->setLocale(new StringType(locale_get_default()));

        if (!is_null($name)) {
            $this->setName(new StringType($name));
        }
    }

    /**
     * Return currency symbol
     * @return StringType
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Set currency symbol
     *
     * @param StringType $symbol
     * @return Fluent Interface
     */
    public function setSymbol(StringType $symbol)
    {
        $this->symbol = $symbol;
        return $this;
    }

    /**
     * Return code of this currency
     *
     * @return StringType
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set currency code
     *
     * @param StringType $code
     * @return Fluent Interface
     */
    public function setCode(StringType $code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * Set long name for the currency
     *
     * @param StringType $name
     * @return Fluent Interface
     */
    public function setName(StringType $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Return currency long name.
     * Will return currency code if not set
     *
     * @return StringType
     */
    public function getName()
    {
        if(is_null($this->name)) {
            return $this->code;
        }

        return $this->name;
    }

    /**
     * Set user defined display format for use in display()
     * NB. sprintf formatter
     * Default is '%s'
     *
     * @param StringType $displayFormat
     * @return Fluent Interface
     */
    public function setDisplayFormat(StringType $displayFormat)
    {
        $this->displayFormat = $displayFormat;
        return $this;
    }

    /**
     * Set locale for display purposes
     *
     * @param StringType $locale
     * @return Fluent Interface
     */
    public function setLocale(StringType $locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Set number of digits of precision for the currency
     * @param IntType $precision
     * @return Fluent Interface
     */
    public function setPrecision(IntType $precision)
    {
        $this->precision = $precision;
        return $this;
    }

    /**
     * Set currency value, upscaling into an integer for internal storage
     *
     * @param numeric|NumericType $value
     *
     * @return Fluent Interface
     * @throws \InvalidArgumentException
     */
    public function setAsFloat($value)
    {
        if (!(is_numeric($value) || $value instanceof NumericTypeInterface)) {
            throw new \InvalidArgumentException('value is not numeric');
        }
        $val = is_object($value) ? $value->asFloatType()->get() : floatval($value);
        $intVal = pow(10, $this->precision->get()) * $val;
        parent::set($intVal);

        return $this;
    }

    /**
     * return currency value as downscaled float value
     * @return float
     */
    public function getAsFloat()
    {
        return $this->value / pow(10, $this->precision->get());
    }


    /**
     * Return currency amount formatted for display
     *
     * @return StringType
     */
    public function display()
    {
        $formatter = new \NumberFormatter($this->locale->get(), \NumberFormatter::CURRENCY);
        $formatter->setSymbol(\NumberFormatter::CURRENCY_SYMBOL, $this->symbol->get());
        $formatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $this->precision->get());

        return new StringType(sprintf($this->displayFormat, $formatter->format($this->getAsFloat())));
    }
}