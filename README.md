Mink
====
[![Latest Stable Version](https://poser.pugx.org/behat/mink/v/stable.svg)](https://packagist.org/packages/behat/mink)
[![Latest Unstable Version](https://poser.pugx.org/behat/mink/v/unstable.svg)](https://packagist.org/packages/behat/mink)
[![Total Downloads](https://poser.pugx.org/behat/mink/downloads.svg)](https://packagist.org/packages/behat/mink)
[![CI](https://github.com/minkphp/Mink/actions/workflows/tests.yml/badge.svg)](https://github.com/minkphp/Mink/actions/workflows/tests.yml)
[![License](https://poser.pugx.org/behat/mink/license.svg)](https://packagist.org/packages/behat/mink)


Useful Links
------------

- The main website with documentation is at [https://mink.behat.org](https://mink.behat.org)
- Official Google Group is at [https://groups.google.com/group/behat](https://groups.google.com/group/behat)
- [Note on Patches/Pull Requests](CONTRIBUTING.md)

Usage Example
-------------

``` php
<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\GoutteDriver,
    Behat\Mink\Driver\Goutte\Client as GoutteClient;

$startUrl = 'http://example.com';

// init Mink and register sessions
$mink = new Mink(array(
    'goutte1' => new Session(new GoutteDriver(new GoutteClient())),
    'goutte2' => new Session(new GoutteDriver(new GoutteClient())),
    'custom'  => new Session(new MyCustomDriver($startUrl))
));

// set the default session name
$mink->setDefaultSessionName('goutte2');

// visit a page
$mink->getSession()->visit($startUrl);

// call to getSession() without argument will always return a default session if has one (goutte2 here)
$mink->getSession()->getPage()->findLink('Downloads')->click();
echo $mink->getSession()->getPage()->getContent();

// call to getSession() with argument will return session by its name
$mink->getSession('custom')->getPage()->findLink('Downloads')->click();
echo $mink->getSession('custom')->getPage()->getContent();

// this all is done to make possible mixing sessions
$mink->getSession('goutte1')->getPage()->findLink('Chat')->click();
$mink->getSession('goutte2')->getPage()->findLink('Chat')->click();
```

Install Dependencies
--------------------

``` bash
$> curl -sS https://getcomposer.org/installer | php
$> php composer.phar install
```

Contributors
------------

* Konstantin Kudryashov [everzet](https://github.com/everzet) [lead developer]
* Christophe Coevoet [stof](https://github.com/stof) [lead developer]
* Alexander Obuhovich [aik099](https://github.com/aik099) [lead developer]
* Other [awesome developers](https://github.com/minkphp/Mink/graphs/contributors)
