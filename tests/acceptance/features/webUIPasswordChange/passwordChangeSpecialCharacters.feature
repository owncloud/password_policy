@webUI @mailhog
Feature: enforce the required number of special characters in a password on the password change UI page

  As an administrator
  I want user passwords to always contain a required number of special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | a!b@c#1234 | User One    | u1@oc.com.np |
    And the user has browsed to the login page
    And the user has logged in with username "user1" and password "a!b@c#1234" using the webUI
    And the user has browsed to the personal general settings page

  Scenario Outline: user changes their password to a string with enough special characters
    When the user changes the password to "<password>" using the webUI
    And the user re-logs in with username "user1" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password              |
      | 3#Special$Characters! |
      | 1!2@3#4$5%6^7&8*      |

  Scenario Outline: user tries to change their password to a string that has too few special characters
    When the user changes the password to "<password>" using the webUI
    Then a password error message should be displayed on the webUI with the text "The password contains too few special characters. At least 3 special characters are required."
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special!Characters |
