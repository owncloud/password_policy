@webUI @insulated @disablePreviews @email
Feature: enforce the restricted special characters in a password on user creation

  As an administrator
  I want user passwords to always contain some of a restricted list of special characters
  So that users cannot set passwords that have unusual hard-to-type characters

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    And the administrator has logged in using the webUI
    And the administrator has browsed to the users page


  Scenario Outline: administrator creates a user with password set to a string with enough restricted special characters
    When the administrator creates a user with the name "guiusr1" and the password "<password>" using the webUI
    And the administrator logs out of the webUI
    And the user logs in with username "guiusr1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    Examples:
      | password              |
      | 3$Special%Characters^ |
      | 1*2&3^4%5$6           |


  Scenario Outline: administrator creates a user with password set to a string that has too few restricted special characters
    When the administrator attempts to create a user with the name "guiusr1" and the password "<password>" using the webUI
    Then a notification should be displayed on the webUI with the text "Error creating user: The password contains too few special characters. At least 3 special characters ($%^&*) are required."
    And user "guiusr1" should not exist
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special&Characters |


  Scenario Outline: administrator creates a user with password set to a string that has invalid special characters
    When the administrator attempts to create a user with the name "guiusr1" and the password "<password>" using the webUI
    Then a notification should be displayed on the webUI with the text "Error creating user: The password contains invalid special characters. Only $%^&* are allowed."
    And user "guiusr1" should not exist
    Examples:
      | password                                 |
      | Only#Invalid!Special@Characters          |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! |


  Scenario Outline: user sets their password to a string with enough restricted special characters after being created with an Email address only
    When the administrator creates a user with the name "Alice" and the email "guiusr1@owncloud" without a password using the webUI
    And the administrator logs out of the webUI
    And the user follows the password set link received by "guiusr1@owncloud" using the webUI
    And the user sets the password to "<password>" and confirms with the same password using the webUI
    Then the user should be redirected to the login page
    And the email address "guiusr1@owncloud" should have received an email with the body containing
      """
      Password changed successfully
      """
    When the user logs in with username "Alice" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    Examples:
      | password              |
      | 3$Special%Characters^ |
      | 1*2&3^4%5$6           |


  Scenario Outline: user tries to set their password to a string that has too few restricted special characters after being created with an Email address only
    When the administrator creates a user with the name "Alice" and the email "guiusr1@owncloud" without a password using the webUI
    And the administrator logs out of the webUI
    And the user follows the password set link received by "guiusr1@owncloud" using the webUI
    And the user sets the password to "<password>" and confirms with the same password using the webUI
    Then a set password error message with this text should be displayed on the webUI:
      """
      The password contains too few special characters. At least 3 special characters ($%^&*) are required.
      """
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special&Characters |


  Scenario Outline: user tries to set their password to a string that has invalid special characters after being created with an Email address only
    When the administrator creates a user with the name "Alice" and the email "guiusr1@owncloud" without a password using the webUI
    And the administrator logs out of the webUI
    And the user follows the password set link received by "guiusr1@owncloud" using the webUI
    And the user sets the password to "<password>" and confirms with the same password using the webUI
    Then a set password error message with this text should be displayed on the webUI:
      """
      The password contains invalid special characters. Only $%^&* are allowed.
      """
    Examples:
      | password                                 |
      | Only#Invalid!Special@Characters          |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! |
