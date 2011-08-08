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

Translated languages
--------------------

For now exists 6 translated language: `es`,`fr`,`ja`,`nl`,`pt`,`ru`.

**Note:** The `es` and `fr` are outdated.

#### How to add a new translated language?

If you want to translate another language, you can use as reference the `ru` language file under
[translations folder](https://github.com/Behat/Mink/tree/develop/src/Behat/Mink/Behat/Context/translations).
 
Then add it in [MinkContext](https://github.com/Behat/Mink/blob/develop/src/Behat/Mink/Behat/Context/MinkContext.php) after line #657.

**Important:** The filename must match with the same translated language file in [Behat](https://github.com/Behat/Behat/tree/master/i18n) and [Gherkin](https://github.com/Behat/Gherkin/tree/master/i18n) in order to work correctly. If the language does not exist in [Gherkin](https://github.com/Behat/Gherkin/tree/master/i18n) and [Behat](https://github.com/Behat/Behat/tree/master/i18n) you must  add it there too.

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
