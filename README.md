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

Existing Sahi session usage
---------------------------

Everytime Mink inits SahiDriver - it tries to connect to the browser with specific SID and if it can't - it starts new browser automatically. It means, that if you run ANY browser before starting mink and point it to page with correct SID - SahiDriver will use this browser as tests aim. But! By default, SahiDriver will automatically generate unique SID. You can change this behavior with third parameter to SahiDriver, which should be `Behat\SahiClient\Client` instance:

``` php
<?php

$client = new \Behat\SahiClient\Client(new \Behat\SahiClient\Connection('SAHI_SID'));
$mink->registerDriver('javascript', new SahiDriver($startUrl, 'firefox', $client));
```

`SAHI_SID` could be any unique string.
Now just configure proxy settings in needed browser and point it to:

    http://sahi.example.com/_s_/dyn/Driver_start?sahisid=SAHI_SID&startUrl=http://sahi.example.com/_s_/dyn/Driver_initialized

This way you could test your sites on iOS or Android or WinPhone devices.

Copyright
---------

Copyright (c) 2011 Konstantin Kudryashov (ever.zet). See LICENSE for details.

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]

Sponsors
--------

* knpLabs [knpLabs](http://www.knplabs.com/) [main sponsor]
