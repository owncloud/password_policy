@api
Feature: enforce the minimum length of a password when resetting the password using the occ command

  As an administrator
  I want user passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | 1234567890 | User One    | u1@oc.com.np |

  Scenario Outline: admin resets the password of a user to one that is long enough
    When the administrator resets the password of user "user1" to "<password>" using the occ command
    Then the command should have been successful
    And the command output should contain the text 'Successfully reset password for user1'
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |

  Scenario Outline: admin resets the password of a user to one that is not long enough
    When the administrator resets the password of user "user1" to "<password>" using the occ command
    Then the command should have failed with exit code 1
    And the command error output should contain the text 'The password is too short. At least 10 characters are required.'
    Examples:
      | password  |
      | A         |
      | 123456789 |
