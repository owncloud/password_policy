@cli
Feature: enforce combinations of password policies when resetting a user password using the occ command

  As an administrator
  I want user passwords to always have some combination of minimum length, lowercase, uppercase, numbers and special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "15"
    And the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "4"
    And the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "2"
    And the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And these users have been created with default attributes and skeleton files:
      | username | password        |
      | user1    | aA1!bB2#cC&deee |

  Scenario Outline: admin resets the password of a user with a password that meets the password policy
    When the administrator resets the password of user "user1" to "<password>" using the occ command
    Then the command should have been successful
    And the command output should contain the text 'Successfully reset password for user1'
    And the content of file "textfile0.txt" for user "user1" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "aA1!bB2#cC&deee" should not be able to download file "textfile0.txt"
    Examples:
      | password                  |
      | 15***UPPloweZZZ           |
      | More%Than$15!Characters-0 |

  @skipOnOcV10.2
  Scenario Outline: admin resets the password of a user with a password that does not meet the password policy
    When the administrator resets the password of user "user1" to "<password>" using the occ command
    Then the command should have failed with exit code 1
    And the command error output should contain the text '<message>'
    And the content of file "textfile0.txt" for user "user1" using password "aA1!bB2#cC&deee" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "<password>" should not be able to download file "textfile0.txt"
    Examples:
      | password                       | message                                                                   |
        # where just one of the requirements is not met
      | aA1!bB2#cC&d                   | The password is too short. At least 15 characters are required.           |
      | aA1!bB2#cNOT&ENOUGH#LOWERCASE  | The password contains too few lowercase letters. At least 4 lowercase     |
      | aA1!bB2#cnot&enough#uppercase  | The password contains too few uppercase letters. At least 3 uppercase     |
      | Not&Enough#Numbers=1           | The password contains too few numbers. At least 2 numbers are required.   |
      | Not&Enough#Special8Characters2 | The password contains too few special characters. At least 3 special char |
        # where multiple requirements are not met, only the first error message is shown to the user
      | aA!1                           | The password is too short. At least 15 characters are required.           |
      | aA!123456789012345             | The password contains too few lowercase letters. At least 4 lowercase     |

  Scenario Outline: admin resets the password of a user with a password that has valid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    When the administrator resets the password of user "user1" to "<password>" using the occ command
    Then the command should have been successful
    And the command output should contain the text 'Successfully reset password for user1'
    And the content of file "textfile0.txt" for user "user1" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "aA1!bB2#cC&deee" should not be able to download file "textfile0.txt"
    Examples:
      | password                  |
      | 15%&*UPPloweZZZ           |
      | More^Than$15&Characters*0 |

  @skipOnOcV10.2
  Scenario Outline: admin resets the password of a user with a password that has invalid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    When the administrator resets the password of user "user1" to "<password>" using the occ command
    Then the command should have failed with exit code 1
    And the command error output should contain the text '<message>'
    And the content of file "textfile0.txt" for user "user1" using password "aA1!bB2#cC&deee" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "<password>" should not be able to download file "textfile0.txt"
    Examples:
      | password        | message                                                                   |
      | 15#!!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed. |
      | 15&%!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed. |
        # where multiple requirements are not met, only the first error message is shown to the user
      | 15&%!UPPlowZZZZ | The password contains too few lowercase letters. At least 4 lowercase     |
