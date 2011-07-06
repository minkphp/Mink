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
