@webUI
Feature: expire user's password using the occ command

  As an administrator
  I want to expire user's password
  So that users change their password regularly

  Background:
    Given user "Alice" has been created with default attributes and skeleton files

  Scenario: admin expires password of a user
    Given the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    When user "Alice" logs in with the expired password using the webUI
    Then the user should be redirected to a webUI page with the title "%productname%"
    When user "Alice" enters the current password, chooses a new password "newpassword" and confirms it using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"

  Scenario: password update with wrong old password
    Given the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When the user requests for password update with the following credentials using the webUI
      | old_password         | wrong1213   |
      | new_password         | newpassword |
      | confirm_new_password | newpassword |
    Then an error message with the following text should be displayed on the webUI:
      """
      Old password is wrong.
      """

  Scenario: new password should be confirmed for successful update
    Given the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When the user requests for password update with the following credentials using the webUI
      | old_password         | wrong1213   |
      | new_password         | newpass     |
      | confirm_new_password | newpassword |
    Then 2 new password fields should be highlighted with red color
    And the password update submit button should be disabled
