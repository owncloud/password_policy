@webUI @insulated @disablePreviews @email
Feature: enforce combinations of password policies on user creation

  As an administrator
  I want user passwords to always have some combination of minimum length, lowercase, uppercase, numbers and special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "15"
    And the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "4"
    And the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "2"
    And the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has logged in using the webUI
    And the administrator has browsed to the users page


  Scenario Outline: administrator creates a user with password set to a valid string
    When the administrator creates a user with the name "guiusr1" and the password "<password>" using the webUI
    And the administrator logs out of the webUI
    And the user logs in with username "guiusr1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    Examples:
      | password                  |
      | 15***UPPloweZZZ           |
      | More%Than$15!Characters-0 |


  Scenario Outline: administrator creates a user with password set to an invalid string
    When the administrator attempts to create a user with the name "guiusr1" and the password "<password>" using the webUI
    Then a notification should be displayed on the webUI with the text "Error creating user: <message>"
    And user "guiusr1" should not exist
    Examples:
      | password                       | message                                                                                       |
        # where just one of the requirements is not met
      | aA1!bB2#cC&d                   | The password is too short. At least 15 characters are required.                               |
      | aA1!bB2#cNOT&ENOUGH#LOWERCASE  | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |
      | aA1!bB2#cnot&enough#uppercase  | The password contains too few uppercase letters. At least 3 uppercase letters are required.   |
      | Not&Enough#Numbers=1           | The password contains too few numbers. At least 2 numbers are required.                       |
      | Not&Enough#Special8Characters2 | The password contains too few special characters. At least 3 special characters are required. |
        # where multiple requirements are not met, only the first error message is shown to the user
      | aA!1                           | The password is too short. At least 15 characters are required.                               |
      | aA!123456789012345             | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |


  Scenario Outline: user sets their password to a string with valid restricted special characters after being created with an Email address only
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
      | password                  |
      | 15***UPPloweZZZ           |
      | More%Than$15!Characters-0 |


  Scenario Outline: user tries to set their password to a string with invalid restricted special characters after being created with an Email address only
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    When the administrator creates a user with the name "Alice" and the email "guiusr1@owncloud" without a password using the webUI
    And the administrator logs out of the webUI
    And the user follows the password set link received by "guiusr1@owncloud" using the webUI
    And the user sets the password to "<password>" and confirms with the same password using the webUI
    Then a set password error message with this text should be displayed on the webUI:
      """
      <message>
      """
    Examples:
      | password        | message                                                                                     |
      | 15#!!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
      | 15&%!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
        # where multiple requirements are not met, only the first error message is shown to the user
      | 15&%!UPPlowZZZZ | The password contains too few lowercase letters. At least 4 lowercase letters are required. |
