@webUI @insulated @disablePreviews @mailhog
Feature: enforce the required number of numbers in a password on the password reset UI page

  As an administrator
  I want user passwords to always contain a required number of numbers
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "3"
    And these users have been created with default attributes and skeleton files:
      | username | password   |
      | Alice    | abcABC1234 |
    And the user has browsed to the login page
    And the user logs in with username "Alice" and invalid password "invalidpassword" using the webUI
    And the user has requested the password reset link using the webUI
    And the user has followed the password reset link from email address "alice@example.org"

  Scenario Outline: user resets their password to a string with enough numbers
    When the user resets the password to "<password>" and confirms with the same password using the webUI
    And the user logs in with username "Alice" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password        |
      | 333Numbers      |
      | moreNumbers1234 |

  Scenario Outline: user tries to reset their password to a string that has too few numbers
    When the user resets the password to "<password>" and confirms with the same password using the webUI
    Then a message with this text should be displayed on the webUI:
      """
      The password contains too few numbers. At least 3 numbers are required.
      """
    Examples:
      | password      |
      | NoNumbers     |
      | Only22Numbers |
