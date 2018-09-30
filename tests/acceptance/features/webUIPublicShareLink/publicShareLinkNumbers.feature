@webUI
Feature: enforce the required number of numbers in a password on the public share link page

  As an administrator
  I want public share link passwords to always contain a required number of numbers
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "3"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | abcABC1234 | User One    | u1@oc.com.np |
    And the user has browsed to the login page
    And the user has logged in with username "user1" and password "abcABC1234" using the webUI

  Scenario Outline: user creates a public share link with enough numbers
    When the user creates a new public link for the folder "simple-folder" using the webUI with
      | password | <password> |
    And the public accesses the last created public link with password "<password>" using the webUI
    Examples:
      | password        |
      | 333Numbers      |
      | moreNumbers1234 |

  Scenario Outline: user tries to create a public share link that has too few numbers
    When the user tries to create a new public link for the folder "simple-folder" using the webUI with
      | password | <password> |
    Then the user should see a error message on public dialog saying "The password contains too few numbers. At least 3 numbers are required."
    And public link should not be generated
    Examples:
      | password      |
      | NoNumbers     |
      | Only22Numbers |
