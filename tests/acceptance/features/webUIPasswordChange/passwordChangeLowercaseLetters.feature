@webUI
Feature: enforce the required number of lowercase letters in a password on the password change UI page

  As an administrator
  I want user passwords to always contain a required number of lowercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "3"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | abcABC1234 | User One    | u1@oc.com.np |
    And the user has browsed to the login page
    And the user has logged in with username "user1" and password "abcABC1234" using the webUI
    And the user has browsed to the personal general settings page

  Scenario Outline: user changes their password to a string with enough lowercase letters
    When the user changes the password to "<password>" using the webUI
    And the user re-logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password                  |
      | 3LCase                    |
      | moreThan3LowercaseLetters |

  Scenario Outline: user tries to change their password to a string that has too few lowercase letters
    When the user changes the password to "<password>" using the webUI
    Then a password error message should be displayed on the webUI with the text "The password contains too few lowercase letters. At least 3 lowercase letters are required."
    Examples:
      | password   |
      | 0LOWERCASE |
      | 2lOWERcASE |
