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
    When user "Alice" logs in with expired password using the webUI
    Then the user should be redirected to a webUI page with the title "%productname%"
    When user "Alice" enters the current password, chooses a new password "newpassword" and confirms it using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
