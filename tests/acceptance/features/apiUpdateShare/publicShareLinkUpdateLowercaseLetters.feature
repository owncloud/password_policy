@api
Feature: enforce the required number of lowercase letters on public share links

  As an administrator
  I want public share link passwords to always contain a required number of lowercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "3"
    And these users have been created with default attributes and skeleton files:
      | username | password   |
      | user1    | abcABC1234 |
    And user "user1" has uploaded file with content "user1 file" to "/randomfile.txt"
    And user "user1" has created a public link share with settings
      | path     | randomfile.txt |
      | password | ABCabc1234     |

  Scenario Outline: user updates the public share link password to a string with enough lowercase letters
    When user "user1" updates the last share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "<password>" and the content should be "user1 file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "<password>" and the content should be "user1 file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "ABCabc1234" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "ABCabc1234" should fail with HTTP status code "401"
    Examples:
      | password                  |
      | 3LCase                    |
      | moreThan3LowercaseLetters |

  Scenario Outline: user tries to update the public share link password to a string with not enough lowercase letters
    When user "user1" tries to update the last share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "The password contains too few lowercase letters. At least 3 lowercase letters are required."
    And the OCS status code should be "400"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "ABCabc1234" and the content should be "user1 file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "ABCabc1234" and the content should be "user1 file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "<password>" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "<password>" should fail with HTTP status code "401"
    Examples:
      | password   |
      | 0LOWERCASE |
      | 2lOWERcASE |
