@webUI @insulated @disablePreviews @mailhog
Feature: enforce the minimum length of a password on the password reset UI page

  As an administrator
  I want user passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And these users have been created with default attributes and skeleton files:
      | username | password   |
      | user1    | 1234567890 |
    And the user has browsed to the login page
    And the user logs in with username "user1" and invalid password "invalidpassword" using the webUI
    And the user has requested the password reset link using the webUI
    And the user has followed the password reset link from email address "user1@example.org"

  Scenario Outline: user resets their password to a long-enough string
    When the user resets the password to "<password>" and confirms with the same password using the webUI
    And the user logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |

  Scenario Outline: user tries to reset their password to a string that is too short
    When the user resets the password to "<password>" and confirms with the same password using the webUI
    Then a message with this text should be displayed on the webUI:
      """
      The password is too short. At least 10 characters are required.
      """
    Examples:
      | password  |
      | A         |
      | 123456789 |
