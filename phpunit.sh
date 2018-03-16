#!/bin/bash

set -eo pipefail

for i in $(find tests -type f -name "*Test.php" | xargs -I {} basename {} .php)
do
    vendor/bin/phpunit --configuration phpunit.xml --filter $i
done