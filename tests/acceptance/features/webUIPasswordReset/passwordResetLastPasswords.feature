@webUI @insulated @disablePreviews @mailhog
Feature: enforce the number of last passwords that must not be used when resetting the password on the password reset UI page

  As an administrator
  I want to prevent users from re-using recent passwords
  So that recent passwords (that may have been compromised) cannot be used to access data

  Background:
    Given the administrator has enabled the last passwords user password policy
    And the administrator has set the number of last passwords that should not be used to "3"
    And these users have been created with default attributes and skeleton files:
      | username | password |
      | user1    | Number1  |
    And the administrator has reset the password of user "user1" to "Number2"
    And the administrator has reset the password of user "user1" to "Number3"
    And the administrator has reset the password of user "user1" to "Number4"
    And the user has browsed to the login page
    And the user logs in with username "user1" and invalid password "invalidpassword" using the webUI
    And the user has requested the password reset link using the webUI
    And the user has followed the password reset link from email address "user1@example.org"

  Scenario Outline: user resets their password to a string that is not one of their last 3 passwords
    When the user resets the password to "<password>" and confirms with the same password using the webUI
    And the user logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password     |
      | Number1      |
      | AnotherValue |

  Scenario Outline: user tries to reset their password to one of their last 3 passwords
    When the user resets the password to "<password>" and confirms with the same password using the webUI
    Then a message with this text should be displayed on the webUI:
      """
      The password must be different than your previous 3 passwords.
      """
    Examples:
      | password |
      | Number2  |
      | Number3  |
      | Number4  |
