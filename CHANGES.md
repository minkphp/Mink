1.0.3 / 2011-08-02
==================

  * File uploads for empty fields fixed (GoutteDriver)
  * Lazy sessions restart
  * `show_tmp_dir` option in MinkContext
  * Updated to stable Symfony2 components
  * SahiClient connection limit bumped to 60 seconds
  * Dutch language support

1.0.2 / 2011-07-22
==================

  * ElementHtmlException fixed (thanks @Stof)

1.0.1 / 2011-07-21
==================

  * Fixed buggy assertions in MinkContext

1.0.0 / 2011-07-20
==================

  * Added missing tests for almost everything
  * Hude speedup for SahiDriver
  * Support for Behat 2.0 contexts
  * Bundled PHPUnit TestCase
  * Deep element traversing
  * Correct behavior of getText() method
  * New getHtml() method
  * Basic HTTP auth support
  * Soft and hard session resetting
  * Cookies management
  * Browser history interactions (reload(), back(), forward())
  * Weaverryan'd exception messages
  * Huge amount of bugfixes and small additions

0.3.2 / 2011-06-20
==================

  * Fixed file uploads in Goutte driver
  * Fixed setting of long texts into fields
  * Added getPlainText() (returns text without tags and whitespaces) method to the element's API
  * Start_url is now optional parameter
  * Default session (if needed) name now need to be always specified by hands with setDefaultSessionName()
  * default_driver => default_session
  * Updated Symfony Components

0.3.1 / 2011-05-17
==================

  * Small SahiClient update (it generates SID now if no provided)
  * setActiveSessionName => setDefaultSessionName method rename

0.3.0 / 2011-05-17
==================

  * Rewritten from scratch Mink drivers handler. Now it's sessions handler. And Mink now
    sessions-centric tool. See examples in readme. Much cleaner API now.

0.2.4 / 2011-05-12
==================

  * Fixed wrong url locator function
  * Fixed wrong regex in `should see` step
  * Fixed delimiters use in `should see` step
  * Added url-match step for checking urls against regex

0.2.3 / 2011-05-01
==================

  * Updated SahiClient with new version, which is faster and cleaner with it's exceptions

0.2.2 / 2011-05-01
==================

  * Ability to use already started browser as SahiDriver aim
  * Added japanese translation for bundled steps (thanks @hidenorigoto)
  * 10 seconds limit for browser connection in SahiDriver

0.2.1 / 2011-04-21
==================

  * Fixed some bundled step definitions

0.2.0 / 2011-04-21
==================

  * Additional step definitions
  * Support for extended drivers configuration through behat.yml environment parameters
  * Lots of new named selectors
  * Bug fixes
  * Small improvements

0.1.2 / 2011-04-08
==================

  * Fixed Sahi url escaping

0.1.1 / 2011-04-06
==================

  * Fixed should/should_not steps
  * Added spanish translation
  * Fixed forms to use <base> element
  * Fixed small UnsupportedByDriverException issue

0.1.0 / 2011-04-04
==================

  * Initial release
