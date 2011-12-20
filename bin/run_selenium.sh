#!/bin/sh

BASEDIR=$(dirname $0)
cd $BASEDIR/../vendor

java -jar selenium.jar > /dev/null &
sleep 5
