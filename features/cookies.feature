Feature: Mink steps
  In order to describe web application behavior
  As a web developer
  I need to be able to talk with Mink through Behat features

  Scenario: Setting and getting a cookie
	When I go to "http://www.google.com/"
	Then the browser should receive a "PREF" cookie
	# And print cookie jar
	And the browser should have a "PREF" cookie
	# And response headers should be printed.

