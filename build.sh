#!/bin/bash
cd ~/Projects/chippyash/source/Currency
vendor/phpunit/phpunit/phpunit -c test/phpunit.xml --testdox-html contract.html test/
tdconv -t "Chippyash Currency" contract.html docs/Test-Contract.md
rm contract.html

