@webUI @insulated @disablePreviews @mailhog
Feature: enforce the required number of uppercase letters in a password when a guest user sets its own password

  Background:
    Given user "user0" has been created with default attributes
    And the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And the administrator has created guest user "guest" with email "guest@example.com"
    And user "user0" has shared folder "/simple-folder" with user "guest@example.com"

  Scenario Outline: A guest user sets own password to a string with enough uppercase letters
    When guest user "guest" registers and sets password to "<password>" using the webUI
    And user "guest@example.com" logs in using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    And folder "simple-folder" should be listed on the webUI
    Examples:
      | password                  |
      | 3UpperCaseLetters         |
      | MoreThan3UpperCaseLetters |

  Scenario Outline: A guest user sets own password to a string that has too few uppercase letters
    When guest user "guest" registers and sets password to "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "%productname%"
    And a warning should be displayed on the set-password-page saying "The password contains too few uppercase letters. At least 3 uppercase letters are required."
    Examples:
      | password       |
      | 0uppercase     |
      | Only2Uppercase |