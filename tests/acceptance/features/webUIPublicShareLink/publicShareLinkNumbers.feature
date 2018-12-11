@webUI
Feature: enforce the required number of numbers in a password on the public share link page

  As an administrator
  I want public share link passwords to always contain a required number of numbers
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "3"
    And these users have been created with default attributes:
      | username | password   |
      | user1    | abcABC1234 |
    And the user has browsed to the login page
    And the user has logged in with username "user1" and password "abcABC1234" using the webUI

  Scenario Outline: user creates a public share link with enough numbers
    When the user creates a new public link for folder "simple-folder" using the webUI with
      | password | <password> |
    And the public accesses the last created public link with password "<password>" using the webUI
    Then file "lorem.txt" should be listed on the webUI
    Examples:
      | password        |
      | 333Numbers      |
      | moreNumbers1234 |

  Scenario Outline: user tries to create a public share link that has too few numbers
    When the user tries to create a new public link for folder "simple-folder" using the webUI with
      | password | <password> |
    Then the user should see an error message on the public link share dialog saying "The password contains too few numbers. At least 3 numbers are required."
    And the public link should not have been generated
    Examples:
      | password      |
      | NoNumbers     |
      | Only22Numbers |
