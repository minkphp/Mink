Feature: Mink steps
  In order to describe web application behavior
  As a web developer
  I need to be able to talk with Mink through Behat features

  @javascript
  Scenario: Basic form (through Sahi)
    Given I am on "basic_form.php"
    When I fill in "first_name" with "Konstantin"
    And I fill in "Kudryashov" for "lastn"
    And I press "Save"
    Then I should see "Anket for Konstantin"
    And I should see "Lastname: Kudryashov"

  Scenario: Basic form (through Goutte)
    Given I am on "basic_form.php"
    When I fill in "first_name" with "Konstantin"
    And I fill in "lastn" with "Kudryashov"
    And I press "Save"
    Then I should see "Anket for Konstantin"
    And I should see "Lastname: Kudryashov"

  Scenario: Looking for a text on a page matching a pattern
    Given I am on "http://mink.behat.org/"
    Then I should see text matching "/ensure that you have at least PHP 5\.3\.\d installed/"
      And I should not see text matching "/ensure that you have at least PHP 5\.2\.\d installed/"

  Scenario: Looking for a text on a page matching a text
    Given I am on "http://mink.behat.org/"
    Then I should see text matching "ensure that you have at least PHP 5.3.1 installed"
      And I should not see text matching "ensure that you have at least PHP 5.2.1 installed"