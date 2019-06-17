@cli
Feature: enforce the number of last passwords that must not be used when resetting the password using the occ command

  As an administrator
  I want to prevent users from re-using recent passwords
  So that recent passwords (that may have been compromised) cannot be used to access data

  Background:
    Given the administrator has enabled the last passwords user password policy
    And the administrator has set the number of last passwords that should not be used to "3"
    And these users have been created with default attributes and skeleton files:
      | username | password |
      | user1    | Number1  |

  Scenario: admin resets the password of a user to the existing password
    When the administrator resets the password of user "user1" to "Number1" using the occ command
    Then the command should have failed with exit code 1
    And the command output should contain the text 'The password must be different than your previous 3 passwords.'
    And the content of file "textfile0.txt" for user "user1" using password "Number1" should be "ownCloud test text file 0" plus end-of-line

  Scenario: admin resets the password of a user to the 1st of 2 passwords
    Given the administrator has reset the password of user "user1" to "Number2"
    When the administrator resets the password of user "user1" to "Number1" using the occ command
    Then the command should have failed with exit code 1
    And the command output should contain the text 'The password must be different than your previous 3 passwords.'
    And the content of file "textfile0.txt" for user "user1" using password "Number2" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "Number1" should not be able to download file "textfile0.txt"

  Scenario: admin resets the password of a user to the 2nd of 2 passwords
    Given the administrator has reset the password of user "user1" to "Number2"
    When the administrator resets the password of user "user1" to "Number2" using the occ command
    Then the command should have failed with exit code 1
    And the command output should contain the text 'The password must be different than your previous 3 passwords.'
    And the content of file "textfile0.txt" for user "user1" using password "Number2" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "Number1" should not be able to download file "textfile0.txt"

  Scenario: admin resets the password of a user to the 2nd of 3 passwords
    Given the administrator has reset the password of user "user1" to "Number2"
    And the administrator has reset the password of user "user1" to "Number3"
    When the administrator resets the password of user "user1" to "Number2" using the occ command
    Then the command should have failed with exit code 1
    And the command output should contain the text 'The password must be different than your previous 3 passwords.'
    And the content of file "textfile0.txt" for user "user1" using password "Number3" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "Number2" should not be able to download file "textfile0.txt"

  Scenario: admin resets the password of a user to the 2nd of 4 passwords
    Given the administrator has reset the password of user "user1" to "Number2"
    And the administrator has reset the password of user "user1" to "Number3"
    And the administrator has reset the password of user "user1" to "Number4"
    When the administrator resets the password of user "user1" to "Number2" using the occ command
    Then the command should have failed with exit code 1
    And the command output should contain the text 'The password must be different than your previous 3 passwords.'
    And the content of file "textfile0.txt" for user "user1" using password "Number4" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "Number2" should not be able to download file "textfile0.txt"

  Scenario: admin resets the password of a user to the 1st of 4 passwords - the password can be reused
    Given the administrator has reset the password of user "user1" to "Number2"
    And the administrator has reset the password of user "user1" to "Number3"
    And the administrator has reset the password of user "user1" to "Number4"
    When the administrator resets the password of user "user1" to "Number1" using the occ command
    Then the command should have been successful
    And the command output should contain the text 'Successfully reset password for user1'
    And the content of file "textfile0.txt" for user "user1" using password "Number1" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "Number4" should not be able to download file "textfile0.txt"

  Scenario: admin resets the password of a user to a value different to any previous passwords - the password can be reused
    Given the administrator has reset the password of user "user1" to "Number2"
    And the administrator has reset the password of user "user1" to "Number3"
    And the administrator has reset the password of user "user1" to "Number4"
    When the administrator resets the password of user "user1" to "AnotherValue" using the occ command
    Then the command should have been successful
    And the command output should contain the text 'Successfully reset password for user1'
    And the content of file "textfile0.txt" for user "user1" using password "AnotherValue" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "Number4" should not be able to download file "textfile0.txt"
