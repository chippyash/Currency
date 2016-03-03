#!/usr/bin/env php
<?php
/**
 * generate-currency-class.php
 * Generate a concrete currency class for you application
 *
 * usage:
 * generate-currency-class.php crcyCode namespace destinationDirectory
 * e.g.
 * generate-currency-class.php GBP myapp\foo /home/me/projects/myapp/foo
 *
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2015, UK
 * @license GPL V3+ See LICENSE.md
 */
namespace chippyash\Currency;

include_once realpath(__DIR__ . '/../vendor/autoload.php');

use Chippyash\Currency\Builder\CurrencyDirector;
use Chippyash\Type\String\StringType;

if ($argc < 4) {
    echo "Please supply arguments: code, namespace, destDir[, locale]\n";
    exit(-1);
}
array_shift($argv); //remove program name
$argc --;

if ($argc == 3) {
    list($code, $namespace, $destDir) = $argv;
    $useLocale = false;
} else {
    list($code, $namespace, $destDir, $locale) = $argv;
    $useLocale = true;
}

if (!file_exists($destDir)) {
    echo "Invalid destination directory: {$destDir}\n";
    exit(-2);
}

$code= strtoupper($code);

if ($useLocale) {
    $saveLocale = locale_get_default();
    locale_set_default($locale);
}

try {
    $director = new CurrencyDirector(new StringType($code), new StringType($namespace), new StringType($destDir));
    $director->build();
    echo "Finished. {$code}.php written to {$destDir}\n";
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage() . PHP_EOL;
    exit(-3);
}

if ($useLocale) {
    locale_set_default($saveLocale);
}

exit(0);