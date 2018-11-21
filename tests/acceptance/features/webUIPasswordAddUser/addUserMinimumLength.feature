@webUI @mailhog
Feature: enforce the minimum length of a password on user creation

  As an administrator
  I want user passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And the administrator has logged in using the webUI
    And the administrator has browsed to the users page

  Scenario Outline: administrator creates a user with password set to a long-enough string
    When the administrator creates a user with the name "guiusr1" and the password "<password>" using the webUI
    And the administrator logs out of the webUI
    And the user logs in with username "guiusr1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |

  Scenario Outline: administrator creates a user with password set to a string that is too short
    When the administrator attempts to create a user with the name "guiusr1" and the password "<password>" using the webUI
    Then a notification should be displayed on the webUI with the text "Error creating user: The password is too short. At least 10 characters are required."
    And user "guiusr1" should not exist
    Examples:
      | password  |
      | A         |
      | 123456789 |

  Scenario Outline: user sets their password to a long-enough string after being created with an Email address only
    When the administrator creates a user with the name "user1" and the email "guiusr1@owncloud" without a password using the webUI
    And the administrator logs out of the webUI
    And the user follows the password set link received by "guiusr1@owncloud" using the webUI
    And the user sets the password to "<password>" using the webUI
    Then the email address "guiusr1@owncloud" should have received an email with the body containing
      """
      Password changed successfully
      """
    When the user logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |

  Scenario Outline: user tries to set their password set to a string that is too short after being created with an Email address only
    When the administrator creates a user with the name "user1" and the email "guiusr1@owncloud" without a password using the webUI
    And the administrator logs out of the webUI
    And the user follows the password set link received by "guiusr1@owncloud" using the webUI
    And the user sets the password to "<password>" using the webUI
    Then a set password error message with this text should be displayed on the webUI:
      """
      The password is too short. At least 10 characters are required.
      """
    Examples:
      | password  |
      | A         |
      | 123456789 |