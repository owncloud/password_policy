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
      | user1    | <password> |
    Then the command should have been successful
    And the command output should contain the text 'The user "user1" was created successfully'
    And user "user1" should exist
    And the content of file "textfile0.txt" for user "user1" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |

  @skipOnOcV10.2
  # The command output for errors is coming on stdout from core 10.3 onwards
  Scenario Outline: admin creates a user with a password that is not long enough
    When the administrator creates this user using the occ command:
      | username | password   |
      | user1    | <password> |
    Then the command should have failed with exit code 1
    And the command output should contain the text 'The password is too short. At least 10 characters are required.'
    And user "user1" should not exist
    Examples:
      | password  |
      | A         |
      | 123456789 |

  @skipOnOcV10.3
  # The command output for errors comes on stderr in core 10.2
  Scenario Outline: admin creates a user with a password that is not long enough
    When the administrator creates this user using the occ command:
      | username | password   |
      | user1    | <password> |
    Then the command should have failed with exit code 1
    And the command error output should contain the text 'The password is too short. At least 10 characters are required.'
    And user "user1" should not exist
    Examples:
      | password  |
      | A         |
      | 123456789 |
