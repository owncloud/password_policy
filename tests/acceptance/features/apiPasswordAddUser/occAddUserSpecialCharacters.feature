@api
Feature: enforce the required number of special characters in a password when creating a user

  As an administrator
  I want user passwords to always contain a required number of special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"

  Scenario Outline: admin creates a user with a password that has enough special characters
    When the administrator creates this user using the occ command:
      | username | password   | displayname | email        |
      | user1    | <password> | User-One    | u1@oc.com.np |
    Then the command should have been successful
    And the command output should contain the text 'The user "user1" was created successfully'
    And user "user1" should exist
    Examples:
      | password              |
      | 3#Special$Characters! |
      | 1!2@3#4$5%6^7&8*      |

  Scenario Outline: admin creates a user with a password that does not have enough special characters
    When the administrator creates this user using the occ command:
      | username | password   | displayname | email        |
      | user1    | <password> | User-One    | u1@oc.com.np |
    Then the command should have failed with exit code 1
    # Long text output comes on multiple lines. Here we just check for enough that will fit on one of the lines.
    And the command error output should contain the text 'The password contains too few special characters. At least 3 special char'
    And user "user1" should not exist
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special!Characters |
