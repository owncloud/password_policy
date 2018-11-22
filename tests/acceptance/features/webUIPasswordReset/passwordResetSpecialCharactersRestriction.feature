@webUI @mailhog
Feature: enforce the restricted special characters in a password on the password reset UI page

  As an administrator
  I want user passwords to always contain some of a restricted list of special characters
  So that users cannot set passwords that have unusual hard-to-type characters

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    And these users have been created with default attributes:
      | username | password   |
      | user1    | a$b%c^1234 |
    And the user has browsed to the login page
    And the user logs in with username "user1" and invalid password "invalidpassword" using the webUI
    And the user has requested the password reset link using the webUI
    And the user has followed the password reset link from email address "user1@example.org"

  Scenario Outline: user resets their password to a string with enough restricted special characters
    When the user resets the password to "<password>" using the webUI
    And the user logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password              |
      | 3$Special%Characters^ |
      | 1*2&3^4%5$6           |

  Scenario Outline: user tries to reset their password to a string that has too few restricted special characters
    When the user resets the password to "<password>" using the webUI
    Then a message with this text should be displayed on the webUI:
      """
      The password contains too few special characters. At least 3 special characters ($%^&*) are required.
      """
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special&Characters |

  Scenario Outline: user tries to reset their password to a string that has invalid special characters
    When the user resets the password to "<password>" using the webUI
    Then a message with this text should be displayed on the webUI:
      """
      The password contains invalid special characters. Only $%^&* are allowed.
      """
    Examples:
      | password                                 |
      | Only#Invalid!Special@Characters          |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! |
