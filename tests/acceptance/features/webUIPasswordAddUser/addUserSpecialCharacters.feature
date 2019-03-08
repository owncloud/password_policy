@webUI @insulated @disablePreviews @mailhog
Feature: enforce the required number of special characters in a password on user creation

  As an administrator
  I want user passwords to always contain a required number of special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has logged in using the webUI
    And the administrator has browsed to the users page

  Scenario Outline: administrator creates a user with password set to a string with enough special characters
    When the administrator creates a user with the name "guiusr1" and the password "<password>" using the webUI
    And the administrator logs out of the webUI
    And the user logs in with username "guiusr1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    Examples:
      | password              |
      | 3#Special$Characters! |
      | 1!2@3#4$5%6^7&8*      |

  Scenario Outline: administrator creates a user with password set to a string that has too few special characters
    When the administrator attempts to create a user with the name "guiusr1" and the password "<password>" using the webUI
    Then a notification should be displayed on the webUI with the text "Error creating user: The password contains too few special characters. At least 3 special characters are required."
    And user "guiusr1" should not exist
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special!Characters |

  Scenario Outline: user sets their password to a string with valid restricted special characters after being created with an Email address only
    When the administrator creates a user with the name "user1" and the email "guiusr1@owncloud" without a password using the webUI
    And the administrator logs out of the webUI
    And the user follows the password set link received by "guiusr1@owncloud" using the webUI
    And the user sets the password to "<password>" and confirms with the same password using the webUI
    Then the email address "guiusr1@owncloud" should have received an email with the body containing
      """
      Password changed successfully
      """
    When the user logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    Examples:
      | password              |
      | 3#Special$Characters! |
      | 1!2@3#4$5%6^7&8*      |

  Scenario Outline: user tries to set their password to a string that has too few special characters after being created with an Email address only
    When the administrator creates a user with the name "user1" and the email "guiusr1@owncloud" without a password using the webUI
    And the administrator logs out of the webUI
    And the user follows the password set link received by "guiusr1@owncloud" using the webUI
    And the user sets the password to "<password>" and confirms with the same password using the webUI
    Then a set password error message with this text should be displayed on the webUI:
      """
      The password contains too few special characters. At least 3 special characters are required.
      """
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special!Characters |