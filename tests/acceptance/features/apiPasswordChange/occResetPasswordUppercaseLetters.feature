@api
Feature: enforce the required number of uppercase letters in a password when resetting the password using the occ command

  As an administrator
  I want user passwords to always contain a required number of uppercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | abcABC1234 | User One    | u1@oc.com.np |

  Scenario Outline: admin resets the password of a user with a password that has enough uppercase letters
    When the administrator resets the password of user "user1" to "<password>" using the occ command
    Then the command should have been successful
    And the command output should contain the text 'Successfully reset password for user1'
    And the content of file "textfile0.txt" for user "user1" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "abcABC1234" should not be able to download file "textfile0.txt"
    Examples:
      | password                  |
      | 3UpperCaseLetters         |
      | MoreThan3UpperCaseLetters |

  Scenario Outline: admin resets the password of a user with a password that does not have enough uppercase letters
    When the administrator resets the password of user "user1" to "<password>" using the occ command
    Then the command should have failed with exit code 1
    # Long text output comes on multiple lines. Here we just check for enough that will fit on one of the lines.
    And the command error output should contain the text 'The password contains too few uppercase letters. At least 3 uppercase'
    And the content of file "textfile0.txt" for user "user1" using password "abcABC1234" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "<password>" should not be able to download file "textfile0.txt"
    Examples:
      | password       |
      | 0uppercase     |
      | Only2Uppercase |
