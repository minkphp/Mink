#!/bin/sh

wget http://selenium.googlecode.com/files/selenium-server-standalone-2.15.0.jar

jar xf selenium-server-standalone-2.15.0.jar core/scripts/atoms.js
ed -- "core/scripts/atoms.js" <<-PATCH
    9423s|a.|XPCNativeWrapper(a).|
    w
    q
PATCH
jar -uf selenium-server-standalone-2.15.0.jar core

rm -r core
