@webUI @mailhog
Feature: enforce the required number of lowercase letters in a password on the password reset UI page

  As an administrator
  I want user passwords to always contain a required number of lowercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "3"
    And these users have been created:
      | username | password   |
      | user1    | abcABC1234 |
    And the user has browsed to the login page
    And the user logs in with username "user1" and invalid password "invalidpassword" using the webUI
    And the user has requested the password reset link using the webUI
    And the user has followed the password reset link from email address "user1@example.org"

  Scenario Outline: user resets their password to a string with enough lowercase letters
    When the user resets the password to "<password>" using the webUI
    And the user logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password                  |
      | 3LCase                    |
      | moreThan3LowercaseLetters |

  Scenario Outline: user tries to reset their password to a string that has too few lowercase letters
    When the user resets the password to "<password>" using the webUI
    Then a message with this text should be displayed on the webUI:
      """
      The password contains too few lowercase letters. At least 3 lowercase letters are required.
      """
    Examples:
      | password   |
      | 0LOWERCASE |
      | 2lOWERcASE |
