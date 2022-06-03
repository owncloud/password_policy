@cli
Feature: enforce the required number of numbers in a password when creating a user

  As an administrator
  I want user passwords to always contain a required number of numbers
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "3"

  Scenario Outline: admin creates a user with a password that has enough numbers
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have been successful
    And the command output should contain the text 'The user "Alice" was created successfully'
    And user "Alice" should exist
    And user "Alice" should be able to upload file "filesForUpload/textfile.txt" to "/textfile.txt"
    Examples:
      | password        |
      | 333Numbers      |
      | moreNumbers1234 |


  Scenario Outline: admin creates a user with a password that does not have enough numbers
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have failed with exit code 1
    And the command error output should contain the text 'The password contains too few numbers. At least 3 numbers are required.'
    And user "Alice" should not exist
    Examples:
      | password      |
      | NoNumbers     |
      | Only22Numbers |
