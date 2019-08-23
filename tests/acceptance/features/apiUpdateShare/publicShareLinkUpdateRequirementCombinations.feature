@api
Feature: enforce combinations of password policies on public share links

  As an administrator
  I want public share link passwords to always have some combination of minimum length, lowercase, uppercase, numbers and special characters
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
    And user "user1" has uploaded file with content "user1 file" to "/randomfile.txt"
    And user "user1" has created a public link share with settings
      | path     | randomfile.txt  |
      | password | zA1@bB2#cC&deee |

  Scenario Outline: user updates the public share link password to a valid string
    When user "user1" updates the last share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "<password>" and the content should be "user1 file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "<password>" and the content should be "user1 file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "zA1@bB2#cC&deee" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "zA1@bB2#cC&deee" should fail with HTTP status code "401"
    Examples:
      | password                  |
      | 15***UPPloweZZZ           |
      | More%Than$15!Characters-0 |

  Scenario Outline: user tries to update the public share link password to an invalid string
    When user "user1" tries to update the last share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "<message>"
    And the OCS status code should be "400"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "zA1@bB2#cC&deee" and the content should be "user1 file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "zA1@bB2#cC&deee" and the content should be "user1 file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "<password>" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "<password>" should fail with HTTP status code "401"
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

  Scenario Outline: user updates the public share link password to valid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    When user "user1" updates the last share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "<password>" and the content should be "user1 file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "<password>" and the content should be "user1 file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "zA1@bB2#cC&deee" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "zA1@bB2#cC&deee" should fail with HTTP status code "401"
    Examples:
      | password                  |
      | 15%&*UPPloweZZZ           |
      | More^Than$15&Characters*0 |

  Scenario Outline: user tries to update the public share link password to invalid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    When user "user1" tries to update the last share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "<message>"
    And the OCS status code should be "400"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "zA1@bB2#cC&deee" and the content should be "user1 file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "zA1@bB2#cC&deee" and the content should be "user1 file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "<password>" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "<password>" should fail with HTTP status code "401"
    Examples:
      | password        | message                                                                                     |
      | 15#!!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
      | 15&%!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
      # where multiple requirements are not met, only the first error message is shown to the user
      | 15&%!UPPlowZZZZ | The password contains too few lowercase letters. At least 4 lowercase letters are required. |
