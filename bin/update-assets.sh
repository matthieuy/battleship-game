#!/bin/bash

echo "=> Update JS translation"
./bin/console bazinga:js-translation:dump public

echo -e "\r\n=> Update JS routing"
./bin/console fos:js-routing:dump --format=json --target=public/js/fos_js_routes.json

echo -e "\r\n=> Clear cache"
./bin/console cache:clear

exit 0
