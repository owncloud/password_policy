@webUI @insulated @disablePreviews @mailhog
Feature: enforce the minimum length of a password when a guest user sets its own password

  Background:
    Given user "Alice" has been created with default attributes and large skeleton files
    And the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And the administrator has created guest user "guest" with email "guest@example.com"
    And user "Alice" has shared folder "/simple-folder" with user "guest@example.com"


  Scenario Outline: A guest user sets own password to a long-enough string
    When guest user "guest" registers and sets password to "<password>" using the webUI
    And user "guest@example.com" logs in using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    And folder "simple-folder" should be listed on the webUI
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |


  Scenario Outline: A guest user sets own password to a string that is too short
    When guest user "guest" registers and sets password to "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "%productname%"
    And a warning should be displayed on the set-password-page saying "The password is too short. At least 10 characters are required."
    Examples:
      | password  |
      | A         |
      | 123456789 |
