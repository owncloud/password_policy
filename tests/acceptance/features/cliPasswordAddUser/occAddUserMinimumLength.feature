@cli
Feature: enforce the minimum length of a password when creating a user

  As an administrator
  I want user passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"


  Scenario Outline: admin creates a user with a password that is long enough
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have been successful
    And the command output should contain the text 'The user "Alice" was created successfully'
    And user "Alice" should exist
    And user "Alice" should be able to upload file "filesForUpload/textfile.txt" to "/textfile.txt"
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |


  Scenario Outline: admin creates a user with a password that is not long enough
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have failed with exit code 1
    And the command error output should contain the text 'The password is too short. At least 10 characters are required.'
    And user "Alice" should not exist
    Examples:
      | password  |
      | A         |
      | 123456789 |
