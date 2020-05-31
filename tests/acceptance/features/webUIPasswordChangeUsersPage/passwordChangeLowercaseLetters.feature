@webUI @insulated @disablePreviews @skipOnFIREFOX
Feature: enforce the required number of lowercase letters in a password on the password change from users page

  As an administrator
  I want user passwords to always contain a required number of lowercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "3"
    And these users have been created with default attributes and skeleton files:
      | username | password   |
      | Alice    | abcABC1234 |
    And the user has browsed to the login page
    And user admin has logged in using the webUI
    And the user has browsed to the users page

  Scenario Outline: Admin changes user's password to a string with enough lowercase letters
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    Then user "Alice" should exist
    And the content of file "textfile0.txt" for user "Alice" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    But user "Alice" using password "abcABC1234" should not be able to download file "textfile0.txt"
    Examples:
      | password                  |
      | 3LCase                    |
      | moreThan3LowercaseLetters |

  Scenario Outline: Admin tries to change user's password to a string that has too few lowercase letters
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    Then notifications should be displayed on the webUI with the text
      | The password contains too few lowercase letters. At least 3 lowercase letters are required. |
    Examples:
      | password   |
      | 0LOWERCASE |
      | 2lOWERcASE |
