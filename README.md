Behat\Mink
==========

* The main website with documentation is at [http://behat.org](http://behat.org)

Usage
-----

``` php
<?php

use Behat\Mink\Mink,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver;

$startUrl = 'http://example.com';

// init Mink and register drivers
$mink = new Mink();
$mink->registerDriver('goutte',     new GoutteDriver($startUrl), true);  // last argument === isDefault
$mink->registerDriver('javascript', new SahiDriver($startUrl, 'firefox'));
$mink->registerDriver('symfony2',   new GoutteDriver($startUrl, $container->get('client')));
$mink->registerDriver('custom',     new MyCustomDriver($startUrl));

// run in default driver ("goutte" is default driver - last argument to registerDriver())
$mink->switchToDefaultDriver();
$mink->getSession()->getPage()->findLink('Downloads')->click();
echo $mink->getSession()->getPage()->getContent();

// run in javascript (Sahi) driver
$mink->switchToDriver('javascript');
$mink->getSession()->getPage()->findLink('Downloads')->click();
echo $mink->getSession()->getPage()->getContent();

// run in custom driver
$mink->switchToDriver('custom');
$mink->getSession()->getPage()->findLink('Downloads')->click();
echo $mink->getSession()->getPage()->getContent();
```

Copyright
---------

Copyright (c) 2011 Konstantin Kudryashov (ever.zet). See LICENSE for details.

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]

Sponsors
--------

* knpLabs [knpLabs](http://www.knplabs.com/) [main sponsor]
