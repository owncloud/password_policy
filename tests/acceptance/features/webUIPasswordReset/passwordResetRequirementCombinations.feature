@webUI @mailhog
Feature: enforce combinations of password policies on the password reset UI page

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
    And these users have been created with default attributes:
      | username | password        |
      | user1    | aA1!bB2#cC&deee |
    And the user has browsed to the login page
    And the user logs in with username "user1" and invalid password "invalidpassword" using the webUI

  Scenario Outline: user resets their password to a valid string
    When the user requests the password reset link using the webUI
    And the user follows the password reset link from email address "user1@example.org"
    And the user resets the password to "<password>" using the webUI
    And the user logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password                  |
      | 15***UPPloweZZZ           |
      | More%Than$15!Characters-0 |

  Scenario Outline: user tries to reset their password to an invalid string
    When the user requests the password reset link using the webUI
    And the user follows the password reset link from email address "user1@example.org"
    And the user resets the password to "<password>" using the webUI
    Then a message with this text should be displayed on the webUI:
      """
      <message>
      """
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

  Scenario Outline: user resets their password using valid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    When the user requests the password reset link using the webUI
    And the user follows the password reset link from email address "user1@example.org"
    And the user resets the password to "<password>" using the webUI
    And the user logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password                  |
      | 15%&*UPPloweZZZ           |
      | More^Than$15&Characters*0 |

  Scenario Outline: user tries to reset their password using invalid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    When the user requests the password reset link using the webUI
    And the user follows the password reset link from email address "user1@example.org"
    And the user resets the password to "<password>" using the webUI
    Then a message with this text should be displayed on the webUI:
      """
      <message>
      """
    Examples:
      | password        | message                                                                                     |
      | 15#!!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
      | 15&%!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
        # where multiple requirements are not met, only the first error message is shown to the user
      | 15&%!UPPlowZZZZ | The password contains too few lowercase letters. At least 4 lowercase letters are required. |
