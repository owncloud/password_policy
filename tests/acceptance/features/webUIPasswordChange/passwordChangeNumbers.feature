@webUI @insulated @disablePreviews
Feature: enforce the required number of numbers in a password on the password change UI page

  As an administrator
  I want user passwords to always contain a required number of numbers
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "3"
    And these users have been created with default attributes and large skeleton files:
      | username | password   |
      | Alice    | abcABC1234 |
    And the user has browsed to the login page
    And the user has logged in with username "Alice" and password "abcABC1234" using the webUI
    And the user has browsed to the personal general settings page

  Scenario Outline: user changes their password to a string with enough numbers
    When the user changes the password to "<password>" using the webUI
    And the user re-logs in with username "Alice" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password        |
      | 333Numbers      |
      | moreNumbers1234 |

  Scenario Outline: user tries to change their password to a string that has too few numbers
    When the user changes the password to "<password>" using the webUI
    Then a password error message should be displayed on the webUI with the text "The password contains too few numbers. At least 3 numbers are required."
    Examples:
      | password      |
      | NoNumbers     |
      | Only22Numbers |
