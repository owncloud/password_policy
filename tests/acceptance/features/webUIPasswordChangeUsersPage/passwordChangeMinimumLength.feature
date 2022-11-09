@webUI @insulated @disablePreviews @skipOnFIREFOX
Feature: enforce the minimum length of a password on the password change from users page

  As an administrator
  I want user passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And these users have been created with default attributes and large skeleton files:
      | username | password   |
      | Alice    | 1234567890 |
    And the user has browsed to the login page
    And user admin has logged in using the webUI
    And the user has browsed to the users page


  Scenario Outline: Admin changes user's their password to a long-enough string
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    And the user re-logs in with username "Alice" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |


  Scenario Outline: Admin tries to change user's password to a string that is too short
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    Then notifications should be displayed on the webUI with the text
      | The password is too short. At least 10 characters are required. |
    Examples:
      | password  |
      | A         |
      | 123456789 |
