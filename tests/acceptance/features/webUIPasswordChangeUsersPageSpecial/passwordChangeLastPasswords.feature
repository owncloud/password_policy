@webUI @insulated @disablePreviews @skipOnFIREFOX
Feature: enforce the number of last passwords that must not be used when resetting the password on the password change from users page

  As an administrator
  I want to prevent users from re-using recent passwords
  So that recent passwords (that may have been compromised) cannot be used to access data

  Background:
    Given the administrator has enabled the last passwords user password policy
    And the administrator has set the number of last passwords that should not be used to "3"
    And these users have been created with default attributes and skeleton files:
      | username | password |
      | Alice    | Number1  |
    And the administrator has reset the password of user "Alice" to "Number2"
    And the administrator has reset the password of user "Alice" to "Number3"
    And the administrator has reset the password of user "Alice" to "Number4"
    And the user has browsed to the login page
    And user admin has logged in using the webUI
    And the user has browsed to the users page

  Scenario Outline: Admin changes user's password to a string that is not one of their last 3 passwords
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    And the user re-logs in with username "Alice" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password     |
      | Number1      |
      | AnotherValue |

  Scenario Outline: Admin tries to change user's password to one of their last 3 passwords
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    Then notifications should be displayed on the webUI with the text
      | <error-message> |
    Examples:
      | password | error-message                                                  |
      | Number2  | The password must be different than your previous 3 passwords. |
      | Number3  | The password must be different than your previous 3 passwords. |
      | Number4  | The password must be different than your previous 3 passwords. |
