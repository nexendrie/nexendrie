#!/bin/bash
curl -sS https://www.phing.info/get/phing-latest.phar > phing
chmod +x phing
./phing install-production
exit 0
