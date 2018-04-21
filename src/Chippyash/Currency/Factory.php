<?php
/**
 * Currency
 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

namespace Chippyash\Currency;


use Chippyash\Type\String\StringType;

abstract class Factory
{
    /**
     * @var SimpleXMLElement
     */
    static protected $definitions;

    /**
     * Locale to use for currency creation
     * Default is locale_get_default() if not set
     * @see setLocale()
     *
     * @var StringType
     */
    static protected $locale;

    /**
     * Create a currency
     *
     * @param string $code  Currency 3 letter ISO4217 code
     * @param int    $value initial value for currency
     *
     * @return Currency
     *
     * @throws \ErrorException
     */
    public static function create($code, $value = 0)
    {
        $cd = strtoupper($code);
        list($symbol, $precision, $name) = self::getDefinition($cd);
        $crcy = new Currency($value, $cd, $symbol, $precision, $name);
        $crcy->setLocale(self::getLocale());

        return $crcy;
    }

    /**
     * Set locale to be used for currency creation
     *
     * @param string $locale
     */
    public static function setLocale($locale)
    {
        self::$locale = new StringType($locale);
    }

    /**
     * Get locale to be used for currency creation
     * Will default to locale_get_default() if not set
     *
     * @return StringType
     */
    public static function getLocale()
    {
        if (empty(self::$locale)) {
            self::$locale = new StringType(\locale_get_default());
        }

        return self::$locale;
    }

    /**
     * Get a currency definition
     *
     * @param string $code ISO4217 currency code
     *
     * @return array ['symbol','precision', 'name']
     * @throws \ErrorException
     */
    protected static function getDefinition($code)
    {
        $currencies = self::getDefinitions();
        $nodes = $currencies->xpath("//currency[@code='{$code}']");
        if (empty($nodes)) {
            throw new \InvalidArgumentException("Unknown currency: {$code}");
        }

        $cNode = $nodes[0];

        $def = array(
            self::createSymbol($cNode->symbol, $code),
            intval($cNode->exponent),
            self::createName($cNode),
        );

        return $def;
    }

    /**
     * Load currency definitions
     *
     * @return \SimpleXMLElement
     */
    protected static function getDefinitions()
    {
        if (empty(self::$definitions)) {
            self::$definitions = \simplexml_load_file(__DIR__ . '/currencies.xml');
        }

        return self::$definitions;
    }

    /**
     * Create currency symbol from the symbol node
     *
     * @param \SimpleXMLElement $sNode
     * @param string $code currency code
     *
     * @return string
     */
    protected static function createSymbol(\SimpleXMLElement $sNode, $code)
    {
        switch((string) $sNode['type']) {
            case 'UCS':
                $symbol = (string) $sNode['UTF-8'];
                break;
            case null: //no symbol - use code
            default:
                $symbol = $code;
                break;
        }

        return $symbol;
    }

    /**
     * Find closest matching name for a currency based on currently set locale.
     * Default to 'en' entry if none more suitable found
     *
     * @param \SimpleXMLElement $currency
     *
     * @return string
     */
    protected static function createName(\SimpleXMLElement $currency)
    {
        $locale = self::getLocale();
        //first - see if we have an exact locale match
        $nodes = $currency->xpath("name[@lang='{$locale}']");
        if (count($nodes) > 0) {
            return (string) $nodes[0];
        }

        //next, see if we have a name for the language part of the locale
        $lang = \locale_get_primary_language($locale());
        $nodes = $currency->xpath("name[@lang='{$lang}']");
        if (count($nodes) > 0) {
            return (string) $nodes[0];
        }

        //else default to using 'en'
        $nodes = $currency->xpath("name[@lang='en']");
        return (string) $nodes[0];
    }
}