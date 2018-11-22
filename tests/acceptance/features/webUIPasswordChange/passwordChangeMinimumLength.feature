@webUI
Feature: enforce the minimum length of a password on the password change UI page

  As an administrator
  I want user passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And these users have been created with default attributes:
      | username | password   |
      | user1    | 1234567890 |
    And the user has browsed to the login page
    And the user has logged in with username "user1" and password "1234567890" using the webUI
    And the user has browsed to the personal general settings page

  Scenario Outline: user changes their password to a long-enough string
    When the user changes the password to "<password>" using the webUI
    And the user re-logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |

  Scenario Outline: user tries to change their password to a string that is too short
    When the user changes the password to "<password>" using the webUI
    Then a password error message should be displayed on the webUI with the text "The password is too short. At least 10 characters are required."
    Examples:
      | password  |
      | A         |
      | 123456789 |
