@webUI @mailhog
Feature: password uppercase letters

  As an administrator
  I want user passwords to always contain a required number of uppercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And these users have been created but not initialized:
      | username | password   | displayname | email        |
      | user1    | abcABC1234 | User One    | u1@oc.com.np |
    And the user has browsed to the login page
    And the user logs in with username "user1" and invalid password "invalidpassword" using the webUI
    And the user has requested the password reset link using the webUI
    And the user has followed the password reset link from email address "u1@oc.com.np"

  Scenario Outline: user resets their password to a string with enough uppercase letters
    When the user resets the password to "<password>" using the webUI
    And the user logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password                  |
      | 3UpperCaseLetters         |
      | MoreThan3UpperCaseLetters |

  Scenario Outline: user tries to reset their password to a string that has too few uppercase letters
    When the user resets the password to "<password>" using the webUI
    Then a message with this text should be displayed on the webUI:
      """
      The password contains too few uppercase letters. At least 3 uppercase letters are required.
      """
    Examples:
      | password       |
      | 0uppercase     |
      | Only2Uppercase |
