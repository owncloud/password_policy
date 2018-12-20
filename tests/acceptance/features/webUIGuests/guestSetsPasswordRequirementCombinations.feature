@webUI @insulated @disablePreviews @mailhog
Feature: enforce combinations of password policies when a guest user sets its own password

  Background:
    Given user "user0" has been created with default attributes
    And the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "15"
    And the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "4"
    And the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "2"
    And the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has created guest user "guest" with email "guest@example.com"
    And user "user0" has shared folder "/simple-folder" with user "guest@example.com"

  Scenario Outline: A guest user sets own password to a valid string
    When guest user "guest" registers and sets password to "<password>" using the webUI
    And user "guest@example.com" logs in using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    And folder "simple-folder" should be listed on the webUI
    Examples:
      | password                  |
      | 15***UPPloweZZZ           |
      | More%Than$15!Characters-0 |

  Scenario Outline: A guest user sets own password to an invalid string
    When guest user "guest" registers and sets password to "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "%productname%"
    And a warning should be displayed on the set-password-page saying "<message>"
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

