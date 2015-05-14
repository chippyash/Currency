#! /usr/bin/env php
<?php
/**
 * create-currency-data.php
 *
 * Currency Support
 * Create currencies.xml file required by system using data collected from various sources.  Data is held in the
 * ../data directory.
 *
 * Data sources:
 *  - iso4217.xml: http://www.currency-iso.org/dam/downloads/table_a1.xml
 *  - symbols.html: cut from page at http://www.xe.com/symbols.php
 *  - cldr/ folder: the main folder from http://unicode.org/Public/cldr/latest/core.zip
 *
 * The iso4217 and CLDR data are regularly updated.  The symbol information is a bit of a hack and if you know of
 * a more authoritative source as even CLDR doesn't appear to have them all tied to the currency codes, then I'd be glad
 * to hear it.
 *
 * Here are some other resources that may be useful:
 * http://unicode.org/cldr/utility/list-unicodeset.jsp?a=%5B%3Asc%3A%5D
 * http://en.wikipedia.org/wiki/Currency_symbol
 * http://cldr.unicode.org/
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */

/**
 * Convert unicode value to utf8 string
 *
 * @param $num unicode value
 * @return string
 */
function unicode_to_utf8($num)
{
    if($num<=0x7F)       return chr($num);
    if($num<=0x7FF)      return chr(($num>>6)+192).chr(($num&63)+128);
    if($num<=0xFFFF)     return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
    if($num<=0x1FFFFF)   return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128).chr(($num&63)+128);
    return '';
}

/**
 * Convert unicode decimla list to utf-8 encoded character
 *
 * @param string $strDecVal [comma separated list of] decimal character value(s)
 * @return string
 */
function convToUTF8($strDecVal)
{
    $codes = explode(',', str_replace(' ','',$strDecVal));
    $symbol = '';
    foreach($codes as $code) {
        $symbol .= unicode_to_utf8(intval($code));
    }

    return $symbol;
}

/**
 * Main program
 */
function main()
{
    $dataDir = realpath(__DIR__ . '/../data');

    $fSymbols = new DOMDocument();
    $fSymbols->loadHTMLFile($dataDir . '/symbols.html');

    $fIso = new DOMDocument();
    $fIso->load($dataDir . '/iso4217.xml');

    $qSymbols = new DOMXPath($fSymbols);
    $qIso = new DOMXPath($fIso);

    $fOut = new DOMDocument('1.0', 'UTF-8');
    $nRoot = $fOut->createElement('currencies');
    $fOut->appendChild($nRoot);

    $currencies = $qIso->query('//Ccy');
    $treated = [];
    foreach ($currencies as $currency) {
        $cCode = $currency->textContent;
        if (in_array($cCode, $treated)) {
            continue;
        }
        echo "Creating currency: {$cCode}\n";

        //currency element
        $nCurrency = $fOut->createElement('currency');
        $nCurrency->setAttribute('code', $cCode);
        $nParent = $currency->parentNode;
        $nCurrency->setAttribute('number', $nParent->getElementsByTagName('CcyNbr')->item(0)->nodeValue);

        //currency/name
        $nName = $fOut->createElement('name', $nParent->getElementsByTagName('CcyNm')->item(0)->textContent);
        $nName->setAttribute('lang', 'en');
        $nCurrency->appendChild($nName);

        //currency/exponent
        $eVal = $nParent->getElementsByTagName('CcyMnrUnts')->item(0)->textContent;
        $nExp = $fOut->createElement('exponent', ($eVal == 'N.A.' ? '0' : $eVal));
        $nCurrency->appendChild($nExp);

        //currency/symbol
        $symbolList = $qSymbols->query("//td[text()='{$cCode}']");
        if ($symbolList->length > 0) {
            $nSymbol = $fOut->createElement('symbol');
            $decVal = $symbolList->item(0)->parentNode->getElementsByTagName('td')->item(5)->textContent;
            $ucsVal = $symbolList->item(0)->parentNode->getElementsByTagName('td')->item(6)->textContent;
            $nSymbol->setAttribute('type', 'UCS');
            $nSymbol->setAttribute('decimal', str_replace(' ', '', $decVal));
            $nSymbol->setAttribute('hex', str_replace(' ', '', $ucsVal));
            $nSymbol->setAttribute('UTF-8', convToUTF8($decVal));
            $nCurrency->appendChild($nSymbol);
        } else {
            echo "No symbol found for: {$cCode}\n";
        }

        $nRoot->appendChild($nCurrency);

        $treated[] = $cCode;
    }

    //find all other language variants for names using cldr database
    echo "\nFinding language variants for display name\n";
    $qOut = new DOMXPath($fOut);
    //create indexed array of files that have currency settings
    $temp = array_diff(scandir($dataDir . '/cldr'), array('..', '.', 'en.xml'));
    $cldrFiles = array();
    foreach ($temp as $entry) {
        $idx = basename($entry, '.xml');
        $dom = new DOMDocument();
        $dom->load($dataDir . "/cldr/{$entry}");
        $q = new DOMXPath($dom);
        $n = $q->query('//currencies/currency/displayName');
        if ($n->length > 0) {
            $cldrFiles[$idx] = $q;
        }
    }
    //scan each currency and find its other language variants
    foreach ($cldrFiles as $idx => $qLang) {
        $nLangs = $qLang->query('//currencies/currency');

        foreach ($nLangs as $lang) {
            $crcyCode = $lang->getAttribute('type');
            try {
                $name = $lang->getElementsByTagName('displayName')->item(0)->textContent;
            } catch (\Exception $e) {
                echo "No display name for: {$crcyCode} in language file: {$idx}.xml\n";
                continue;
            }
            $srcNodes = $qOut->query("//currency[@code='{$crcyCode}']");
            if ($srcNodes->length == 0) {
                //we don't have this currency so remove from cldr list
                unset($cldrFiles[$idx]);
                continue;
            }

            $nName = $fOut->createElement('name', $name);
            $nName->setAttribute('lang', $idx);
            $srcNodes->item(0)->appendChild($nName);
        }
    }

    $fOut->save(realpath(__DIR__ . '/../src/chippyash/Currency') . '/currencies.xml');
}

//set errors to create exceptions
set_error_handler(
    function($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        // error was suppressed with the @-operator
        if (0 === error_reporting()) {
            return false;
        }

        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
);

main();