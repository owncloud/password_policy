@cli
Feature: enforce the required number of uppercase letters in a password when creating a user

  As an administrator
  I want user passwords to always contain a required number of uppercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"


  Scenario Outline: admin creates a user with a password that has enough uppercase letters
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have been successful
    And the command output should contain the text 'The user "Alice" was created successfully'
    And user "Alice" should exist
    And user "Alice" should be able to upload file "filesForUpload/textfile.txt" to "/textfile.txt"
    Examples:
      | password                  |
      | 3UpperCaseLetters         |
      | MoreThan3UpperCaseLetters |


  Scenario Outline: admin creates a user with a password that does not have enough uppercase letters
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have failed with exit code 1
    # Long text output comes on multiple lines. Here we just check for enough that will fit on one of the lines.
    And the command error output should contain the text 'The password contains too few uppercase letters. At least 3 uppercase'
    And user "Alice" should not exist
    Examples:
      | password       |
      | 0uppercase     |
      | Only2Uppercase |
