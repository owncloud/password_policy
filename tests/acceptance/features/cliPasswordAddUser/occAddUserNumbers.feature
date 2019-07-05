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
      | user1    | <password> |
    Then the command should have been successful
    And the command output should contain the text 'The user "user1" was created successfully'
    And user "user1" should exist
    And the content of file "textfile0.txt" for user "user1" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    Examples:
      | password        |
      | 333Numbers      |
      | moreNumbers1234 |

  @skipOnOcV10.2
  Scenario Outline: admin creates a user with a password that does not have enough numbers
    When the administrator creates this user using the occ command:
      | username | password   |
      | user1    | <password> |
    Then the command should have failed with exit code 1
    And the command output should contain the text 'The password contains too few numbers. At least 3 numbers are required.'
    And user "user1" should not exist
    Examples:
      | password      |
      | NoNumbers     |
      | Only22Numbers |
