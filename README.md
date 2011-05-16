Behat\Mink
==========

* The main website with documentation is at [http://behat.org](http://behat.org)

Usage
-----

``` php
<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver;

$startUrl = 'http://example.com';

// init Mink and register drivers
$mink = new Mink(
    'goutte1'    => new Session(new GoutteDriver($startUrl)),
    'goutte2'    => new Session(new GoutteDriver($startUrl)),
    'javascript' => new Session(new SahiDriver($startUrl, 'firefox')),
    'custom'     => new Session(new MyCustomDriver($startUrl))
);

// set active session name
$mink->setActiveSessionName('goutte2');

// call getSession without argument will always return active session if has one (goutte2 here)
$mink->getSession()->getPage()->findLink('Downloads')->click();
echo $mink->getSession()->getPage()->getContent();

// run in javascript (Sahi) driver
$mink->getSession('javascript')->getPage()->findLink('Downloads')->click();
echo $mink->getSession('javascript')->getPage()->getContent();

// run in custom driver
$mink->getSession('custom')->getPage()->findLink('Downloads')->click();
echo $mink->getSession('custom')->getPage()->getContent();

// mix sessions
$mink->getSession('goutte1')->getPage()->findLink('Chat')->click();
$mink->getSession('goutte2')->getPage()->findLink('Chat')->click();
```

Existing Sahi session usage
---------------------------

Everytime Mink inits SahiDriver - it tries to connect to the browser with specific SID and if it can't - it starts new browser automatically. It means, that if you run ANY browser before starting mink and point it to page with correct SID - SahiDriver will use this browser as tests aim. But! By default, SahiDriver will automatically generate unique SID. You can change this behavior with third parameter to SahiDriver, which should be `Behat\SahiClient\Client` instance:

``` php
<?php

$client = new \Behat\SahiClient\Client(new \Behat\SahiClient\Connection('SAHI_SID'));
$mink->registerSession('javascript', new Session(new SahiDriver($startUrl, 'firefox', $client)));
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
