Mink
====

- [stable (master)](https://github.com/Behat/Mink) ([![Master Build
Status](https://secure.travis-ci.org/Behat/Mink.png?branch=master)](http://travis-ci.org/Behat/Mink)) - staging branch. Last stable version.
- [development (develop)](https://github.com/Behat/Mink/tree/develop) ([![Develop Build Status](https://secure.travis-ci.org/Behat/Mink.png?branch=develop)](http://travis-ci.org/Behat/Mink)) - development branch. Development happens here and you should send your PRs here too.

Useful Links
------------

- The main website with documentation is at [http://mink.behat.org](http://mink.behat.org)
- Official Google Group is at [http://groups.google.com/group/behat](http://groups.google.com/group/behat)
- IRC channel on [#freenode](http://freenode.net/) is `#behat`
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
    'goutte1' => new Session(new GoutteDriver(GoutteClient($startUrl))),
    'goutte2' => new Session(new GoutteDriver(GoutteClient($startUrl))),
    'custom'  => new Session(new MyCustomDriver($startUrl))
));

// set the default session name
$mink->setDefaultSessionName('goutte2');

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
$> curl http://getcomposer.org/installer | php
$> php composer.phar install
```

Contributors
------------

* Konstantin Kudryashov [everzet](http://github.com/everzet) [lead developer]
* Other [awesome developers](https://github.com/Behat/Mink/graphs/contributors)
