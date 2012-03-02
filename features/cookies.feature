Feature: Mink steps
  In order to describe web application behavior
  As a web developer
  I need to be able to talk with Mink through Behat features

  Scenario: Setting and getting a cookie
	When I go to "http://www.google.com/"
	# Then print cookie jar
	Then the browser should have a "PREF" cookie

