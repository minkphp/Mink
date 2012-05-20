Mink
====

[![Build Status](https://secure.travis-ci.org/Behat/Mink.png)](http://travis-ci.org/Behat/Mink)

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
    Behat\Mink\Driver\Goutte\Client as GoutteClient,
    Behat\Mink\Driver\SahiDriver;

$startUrl = 'http://example.com';

// init Mink and register sessions
$mink = new Mink(array(
    'goutte1'    => new Session(new GoutteDriver(GoutteClient($startUrl))),
    'goutte2'    => new Session(new GoutteDriver(GoutteClient($startUrl))),
    'javascript' => new Session(new SahiDriver('firefox')),
    'custom'     => new Session(new MyCustomDriver($startUrl))
));

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

Install Dependencies
--------------------

``` bash
curl http://getcomposer.org/installer | php
php composer.phar install
```

How to run Mink test suite
--------------------------

1. Install dependencies (including Sahi and Selenium)

    ``` bash
    bin/install_deps
    bin/install_sahi
    bin/install_selenium
    ```

2. To run all tests - call `bin/run_all_tests`

If you want to run specific driver tests, use appropriate PHPUnit group
and make sure that `Sahi`/`Selenium` is runned if you want to test
one of them:

``` bash
bin/start_sahi
phpunit --group sahidriver
bin/kill_sahi
```

``` bash
bin/start_selenium
phpunit --group seleniumdriver
bin/kill_selenium
```

`Zombie` and `Goutte` driver tests doesn't require some specific proxy
to be runned and could be tested as is:

``` bash
phpunit --group zombiedriver
```

``` bash
phpunit --group gouttedriver
```

Behat integration and translated languages
------------------------------------------

Behat integration altogether with translations have moved into separate
project called `MinkExtension`. It's an extension to Behat 2.4. This will
lead to much faster release cycles as `MinkExtension` doesn't have actual
releases - any accepted PR about language translation or new step definitions
will immediately go into live.

Copyright
---------

Copyright (c) 2011 Konstantin Kudryashov (ever.zet). See LICENSE for details.

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]
* Pascal Cremer [b00giZm](http://github.com/b00giZm) [ZombieDriver creator]
* Alexandre Salomé [alexandresalome](http://github.com/alexandresalome) [SeleniumDriver creator]
* Pete Otaqui [pete-otaqui](http://github.com/pete-otaqui) [Selenium2Driver creator]

Sponsors
--------

* knpLabs [knpLabs](http://www.knplabs.com/) [main sponsor]
