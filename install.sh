#!/bin/bash
curl -sS https://www.phing.info/get/phing-latest.phar > phing
mv phing-latest.phar phing
chmod +x phing
./phing install
exit 0
