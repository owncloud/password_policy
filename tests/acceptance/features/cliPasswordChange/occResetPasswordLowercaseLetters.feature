@cli
Feature: enforce the required number of lowercase letters in a password when resetting the password using the occ command

  As an administrator
  I want user passwords to always contain a required number of lowercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "3"
    And these users have been created with default attributes and small skeleton files:
      | username | password   |
      | Alice    | abcABC1234 |

  Scenario Outline: admin resets the password of a user with a password that has enough lowercase letters
    When the administrator resets the password of user "Alice" to "<password>" using the occ command
    Then the command should have been successful
    And the command output should contain the text 'Successfully reset password for Alice'
    And the content of file "textfile0.txt" for user "Alice" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    But user "Alice" using password "abcABC1234" should not be able to download file "textfile0.txt"
    Examples:
      | password                  |
      | 3LCase                    |
      | moreThan3LowercaseLetters |

  Scenario Outline: admin resets the password of a user with a password that does not have enough lowercase letters
    When the administrator resets the password of user "Alice" to "<password>" using the occ command
    Then the command should have failed with exit code 1
    # Long text output comes on multiple lines. Here we just check for enough that will fit on one of the lines.
    And the command error output should contain the text 'The password contains too few lowercase letters. At least 3 lowercase'
    And the content of file "textfile0.txt" for user "Alice" using password "abcABC1234" should be "ownCloud test text file 0" plus end-of-line
    But user "Alice" using password "<password>" should not be able to download file "textfile0.txt"
    Examples:
      | password   |
      | 0LOWERCASE |
      | 2lOWERcASE |
