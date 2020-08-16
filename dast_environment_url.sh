#!/usr/bin/env bash
FILENAME="environment_url.txt"
if [[ ! -z "$CI_COMMIT_TAG" ]]; then
  echo $URL_BETA > $FILENAME
else
  echo $URL_ALPHA > $FILENAME
fi
exit 0
