# Chippyash/Currency

## Quality Assurance

[![Build Status](https://travis-ci.org/chippyash/Currency.svg?branch=master)](https://travis-ci.org/chippyash/Currency)
[![Coverage Status](https://coveralls.io/repos/chippyash/Currency/badge.svg?branch=master)](https://coveralls.io/r/chippyash/Currency?branch=master)

Certified for PHP 5.3 (Production), Requires PHP 5.5  for Development

See the [Test Contract](https://github.com/Chippyash/currency/blob/master/docs/Test-Contract.md)

## What?

Provides strong type implementation of an [ISO-4217](http://en.wikipedia.org/wiki/ISO_4217) Current Currency.  Includes 

* currency Factory
* development tool to create (hard) currency classes.
* awareness of locale

Uses publicly available currency definitions.

The library is released under the [GNU GPL V3 or later license](http://www.gnu.org/copyleft/gpl.html)

## Why?

Dealing with currencies is a pain in the arse.  To get it right you have to wade through loads of stuff about locales,
symbols and language names, when in fact all you want is a simple way of defining a currency.  This library is aimed
at removing a whole bunch of complexity.

You can simply use the supplied Factory method to create a currency object, or if you know you are only dealing
with a few chosen currencies, use a utility program to generate 'hard' currencies into you own project namespace.

## When

The library was developed to support a '[Simple Accounting](https://github.com/Chippyash/Simple-Accounts)' (double entry book-keeping) library.
It is based on the [Chippyash\StrongType](https://github.com/Chippyash/Strong-Type) set of classes and offers:

* a native PHP IntType based Currency
* a Factory to create currencies
* a utility to create 'hard' (i.e. concrete) currency classes for your application as the Factory method utilizes a
20k line xml currency definition file and could be too slow for your requirements.
* a utility to generate the currency definition file from publicly available data sources.
 
If you want more, either suggest it, or better still, fork it and provide a pull request. If you feel like helping, the
data/symbols.html file needs a/ refactoring into an xml file and b/ having missing symbol definitions added.  Take a
 look at docs/missing-symbols.md as a starting point.  Most of the missing information is available on Wikipedia, but
 it is a manual task to transcribe.

Check out [ZF4 Packages](http://zf4.biz/packages?utm_source=github&utm_medium=web&utm_campaign=blinks&utm_content=currency) for more packages

### Roadmap

V1 - support for native PHP integer based currencies

V2 - support for StrongType GMPIntType based currencies

## How

### Coding Basics

Create a currency (in your current default locale,) via the Type Factory:

<pre>
    use Chippyash\Currency\Factory;
    
    $gbp = Factory::create('GBP');
    //or with an initial value
    $gbp = Factory::create('GBP', 12.26);
</pre>

Create a currency for a different locale:

<pre>
    use Chippyash\Currency\Factory;

    Factory::setLocale('fr_FR');
    $euro = Factory::create('EUR');
    //or with an initial value
    $euro = Factory::create('EUR', 12.26);
</pre>

Create currency directly:

<pre>
    use Chippyash\Currency\Currency;
    
    $value = 12.26;
    $code = 'FOO';
    $symbol = 'f';
    $foo = new Currency($value, $code, $symbol);
    
    //set the precision - and this is where using the Factory starts to 
    // win out unless of course you are creating fantasy currencies
    $precision = 3; //default precision == 2
    $foo = new Currency($value, $code, $symbol, $precision);
    //set long name
    $name = 'Extra Terrestial FooBar'; //default name is the code
    $foo = new Currency($value, $code, $symbol, $precision, $name);
    //supply a display format wrapper
    $displayFormat = 'Yak Yak %s'; //default wrapper is '%s'
    $foo = new Currency($value, $code, $symbol, $precision, $name, $displayFormat);
</pre>

In all ways, the Currency that you have created acts as an IntType. An additional method is supplied:

* display()

display() returns the currency value formatted for the locale that has been set for the currency, e.g. creating:

<pre>
    Factory::setLocale('en_GB');
    $gbp = Factory::create('GBP', 1200.26);
    echo $gbp->display();
</pre>

will display:

£1,200.26

whereas

<pre>
    Factory::setLocale('fr_FR');
    $gbp = Factory::create('GBP', 1200.26);
    echo $gbp->display();
</pre>

will display:

1 200,26 £

In both cases, $gbp->get() (or simply $gbp()) will return 120026, i.e. an int.

If you need the value as a float downscaled according to its precision use getAsFloat()

As alluded to above, the Currency class is based on the strongtype IntType.  This is because integer maths is far more
accurate than floating point maths and if you were to throw some Currencies at the Chippyash/math-type-calculator then
the results would be more accurate and consistent.  The class knows how to convert to/from int/float using the precision
parameter and can therefore maintain long term accuracy.

If you don't want to use the math-type-calculator, you can retrieve the currency's internal value via the get()
method. You can set its value either via the set() method using an integer value or via the setAsFloat() method using
a float value that will be upscaled to the internal integer value using the Currency's precision value. e.g. assuming
a precision == 2, then:

<pre>
    $curr->set(2000);
    //is same as 
    $curr->setAsFloat(20.00);
</pre>

Please note that the strongtype magic \__toString() method, will return the value as stringified integer.  Use the
 display() method for locale aware display of the currency.
 
#### Generating your own application hard currencies

Let's say that your application is only interested in using AUD, GBP and USD.  From a processing point of view, constantly
having to query the currencies.xml file, which although optimized, is still large, and is potentially an expensive process.

A utility is provided to enable you generate 'hard' currency classes to disk.  You will need to have installed the
library via Composer with dev requirements (default for Composer).  The utility can be found at bin/generate-currency-class.php.

The parameters (in order of use,) are: 

- code: ISO4217 currency code 
- namespace: namespace of your application. Escape '\\' by doubling up, '\\\\'
- destDir: target directory to write code to.
- locale: \[optional\] if not specified then your current locale (locale_get_default) will be used

Assuming our current locale is the one we require, we can then run the program 3 times to generate the classes:

<pre>
bin/generate-currency-class.php aud MyApp\\Currency /home/foo/Projects/MyApp/Currency
bin/generate-currency-class.php gbp MyApp\\Currency /home/foo/Projects/MyApp/Currency
bin/generate-currency-class.php usd MyApp\\Currency /home/foo/Projects/MyApp/Currency
</pre>

Let's say we also want to support display of these currencies in France. We can generate thus: 

<pre>
bin/generate-currency-class.php aud MyApp\\Currency\\Fr /home/foo/Projects/MyApp/Currency/Fr fr_FR
bin/generate-currency-class.php gbp MyApp\\Currency\\Fr /home/foo/Projects/MyApp/Currency/Fr fr_FR
bin/generate-currency-class.php usd MyApp\\Currency\\Fr /home/foo/Projects/MyApp/Currency/Fr fr_FR
</pre>

In either case, you'll find three classes have been generated in your target directory:

- AUD.php
- GBP.php
- USD.php

### Class diagram

![currency class diagram](https://github.com/Chippyash/currency/blob/master/docs/class-diagram.jpg)

### Changing the library

1.  fork it
2.  write the test
3.  amend it
4.  do a pull request

Found a bug you can't figure out?

1.  fork it
2.  write the test
3.  do a pull request

NB. Make sure you rebase to HEAD before your pull request

Or - raise an issue ticket.

### Amending the currencies data sets

As previously stated, we are missing some symbol data in the data set (see docs/missing-symbols.md).  If you update any
of the files in the data/ directory, you will need to run the bin/create-currency-data.php script to regenerate the
src/Chippyash/Currency/currencies.xml file.  Run this before any pull request to make changes to the data set.

Please note that CLDR do not provide some currency names for a small set of locales.  The Currency\\Factory will default
to using the base language translation if it can find one, or the English name as a backstop.

Where a symbol cannot be found for a Currency, then it will default to using the ISO4217 code as a symbol.

#### Data set sources

The data sets are sourced from various places:

- [ISO4217.xml](http://www.currency-iso.org/dam/downloads/table_a1.xml) NB - rename the downloaded file
- [symbols.html](http://www.xe.com/symbols.php) NB - scraped from the page. If you know a better source, please let me know.
- cldr directory. is the main directory from the [latest](http://unicode.org/Public/cldr/latest/core.zip) CLDR data set

## Where?

The library is hosted at [Github](https://github.com/Chippyash/Currency). It is
available at [Packagist.org](https://packagist.org/packages/Chippyash/currency)

### Installation

Install [Composer](https://getcomposer.org/)

#### For production

<pre>
    "chippyash/currency": "~2"
</pre>

#### For development

Clone this repo, and then run Composer in local repo root to pull in dependencies

<pre>
    git clone git@github.com:chippyash/Currency.git Currency
    cd Currency
    composer install
</pre>

To run the tests:

<pre>
    cd Currency
    vendor/bin/phpunit -c test/phpunit.xml test/
</pre>

## History

V1.0.0 Initial release

V2.0.0 BC Break: namespace changed from chippyash\Currency to Chippyash\Currency

V2.0.1 Update dev dependencies

V2.0.2 Switch from coveralls to codeclimate

V2.0.3 Add link to packages
