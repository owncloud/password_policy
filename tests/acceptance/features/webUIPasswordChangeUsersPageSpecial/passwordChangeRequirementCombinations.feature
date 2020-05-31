@webUI @insulated @disablePreviews @skipOnFIREFOX
Feature: enforce combinations of password policies on the password change from users page

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
      | Alice    | aA1!bB2#cC&deee |
    And the user has browsed to the login page
    And user admin has logged in using the webUI
    And the user has browsed to the users page

  Scenario Outline: Admin changes user's password to a valid string
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    And the user re-logs in with username "Alice" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password                  |
      | 15***UPPloweZZZ           |
      | More%Than$15!Characters-0 |

  Scenario Outline: Admin tries to change user's password to an invalid string
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    Then notifications should be displayed on the webUI with the text
      | <message> |
    Examples:
      | password                       | message                                                                                       |
        # where just one of the requirements is not met
      | aA1!bB2#cC&d                   | The password is too short. At least 15 characters are required.                               |
      | aA1!bB2#cNOT&ENOUGH#LOWERCASE  | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |
      | aA1!bB2#cnot&enough#uppercase  | The password contains too few uppercase letters. At least 3 uppercase letters are required.   |
      | Not&Enough#Numbers=1           | The password contains too few numbers. At least 2 numbers are required.                       |
      | Not&Enough#Special8Characters2 | The password contains too few special characters. At least 3 special characters are required. |
        # where multiple requirements are not met, only the first error message is shown to the user
      | aA!1                           | The password is too short. At least 15 characters are required.                               |
      | aA!123456789012345             | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |

  Scenario Outline: Admin changes user's password using valid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    And the user re-logs in with username "Alice" and password "<password>" using the webUI
    Then the user should be redirected to a webUI page with the title "Files - ownCloud"
    Examples:
      | password                  |
      | 15%&*UPPloweZZZ           |
      | More^Than$15&Characters*0 |

  Scenario Outline: Admin tries to change user's password using invalid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    When the administrator changes the password of user "Alice" to "<password>" using the webUI
    Then notifications should be displayed on the webUI with the text
      | <message> |
    Examples:
      | password        | message                                                                                     |
      | 15#!!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
      | 15&%!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
        # where multiple requirements are not met, only the first error message is shown to the user
      | 15&%!UPPlowZZZZ | The password contains too few lowercase letters. At least 4 lowercase letters are required. |
