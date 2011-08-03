Mink
====

* The main website with documentation is at
[http://mink.behat.org](http://mink.behat.org)
* Official user group is at [Google Groups](http://groups.google.com/group/behat)

Usage Example
-------------

``` php
<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\SahiDriver;

$startUrl = 'http://example.com';

// init Mink and register sessions
$mink = new Mink(
    'goutte1'    => new Session(new GoutteDriver($startUrl)),
    'goutte2'    => new Session(new GoutteDriver($startUrl)),
    'javascript' => new Session(new SahiDriver($startUrl, 'firefox')),
    'custom'     => new Session(new MyCustomDriver($startUrl))
);

// set default session name
$mink->setDefaultSessionName('goutte2');

// call getSession without argument will always return default session if has one (goutte2 here)
$mink->getSession()->getPage()->findLink('Downloads')->click();
echo $mink->getSession()->getPage()->getContent();

// run in javascript (Sahi) session
$mink->getSession('javascript')->getPage()->findLink('Downloads')->click();
echo $mink->getSession('javascript')->getPage()->getContent();

// run in custom session
$mink->getSession('custom')->getPage()->findLink('Downloads')->click();
echo $mink->getSession('custom')->getPage()->getContent();

// mix sessions
$mink->getSession('goutte1')->getPage()->findLink('Chat')->click();
$mink->getSession('goutte2')->getPage()->findLink('Chat')->click();
```

Copyright
---------

Copyright (c) 2011 Konstantin Kudryashov (ever.zet). See LICENSE for details.

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]
* Pascal Cremer [b00giZm](http://github.com/b00giZm) [ZombieDriver creator]

Sponsors
--------

* knpLabs [knpLabs](http://www.knplabs.com/) [main sponsor]
