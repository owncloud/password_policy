@webUI @mailhog
Feature: enforce the required number of numbers in a password on user creation

  As an administrator
  I want user passwords to always contain a required number of numbers
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "3"
    And the administrator has logged in using the webUI
    And the administrator has browsed to the users page

  Scenario Outline: administrator creates a user with password set to a string with enough numbers
    When the administrator creates a user with the name "guiusr1" and the password "<password>" using the webUI
    And the administrator logs out of the webUI
    And the user logs in with username "guiusr1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    Examples:
      | password        |
      | 333Numbers      |
      | moreNumbers1234 |

  Scenario Outline: administrator creates a user with password set to a string that has too few numbers
    When the administrator attempts to create a user with the name "guiusr1" and the password "<password>" using the webUI
    Then a notification should be displayed on the webUI with the text "Error creating user: The password contains too few numbers. At least 3 numbers are required."
    And user "guiusr1" should not exist
    Examples:
      | password      |
      | NoNumbers     |
      | Only22Numbers |

  Scenario Outline: user sets their password to a string with enough numbers after being created with an Email address only
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
      | password        |
      | 333Numbers      |
      | moreNumbers1234 |

  Scenario Outline: user tries to set their password to a string that has too few numbers after being created with an Email address only
    When the administrator creates a user with the name "user1" and the email "guiusr1@owncloud" without a password using the webUI
    And the administrator logs out of the webUI
    And the user follows the password set link received by "guiusr1@owncloud" using the webUI
    And the user sets the password to "<password>" and confirms with the same password using the webUI
    Then a set password error message with this text should be displayed on the webUI:
      """
      The password contains too few numbers. At least 3 numbers are required.
      """
    Examples:
      | password      |
      | NoNumbers     |
      | Only22Numbers |