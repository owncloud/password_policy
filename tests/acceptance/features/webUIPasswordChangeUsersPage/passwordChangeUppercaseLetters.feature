@webUI @insulated @disablePreviews @skipOnFIREFOX
Feature: enforce the required number of uppercase letters in a password on the password change from users page

  As an administrator
  I want user passwords to always contain a required number of uppercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And these users have been created with default attributes and large skeleton files:
      | username | password   |
      | Alice    | abcABC1234 |
    And the user has browsed to the login page
    And user admin has logged in using the webUI
    And the user has browsed to the users page

  Scenario Outline: Admin changes user's password to a string with enough uppercase letters
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    And the user re-logs in with username "Alice" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password                  |
      | 3UpperCaseLetters         |
      | MoreThan3UpperCaseLetters |

  Scenario Outline: Admin tries to change user's password to a string that has too few uppercase letters
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    Then notifications should be displayed on the webUI with the text
      | The password contains too few uppercase letters. At least 3 uppercase letters are required. |
    Examples:
      | password       |
      | 0uppercase     |
      | Only2Uppercase |
