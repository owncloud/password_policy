@api
Feature: enforce the required number of lowercase letters on public link shares

  As an administrator
  I want public link share passwords to always contain a required number of lowercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "3"
    And these users have been created with default attributes and small skeleton files:
      | username | password   |
      | Alice    | abcABC1234 |
    And user "Alice" has uploaded file with content "Alice file" to "/randomfile.txt"
    And user "Alice" has created a public link share with settings
      | path     | randomfile.txt |
      | password | ABCabc1234     |


  Scenario Outline: user updates the public link share password to a string with enough lowercase letters
    When user "Alice" updates the last public link share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "<password>" and the content should be "Alice file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "<password>" and the content should be "Alice file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "ABCabc1234" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "ABCabc1234" should fail with HTTP status code "401"
    Examples:
      | password                  |
      | 3LCase                    |
      | moreThan3LowercaseLetters |


  Scenario Outline: user tries to update the public link share password to a string with not enough lowercase letters
    When user "Alice" tries to update the last public link share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "The password contains too few lowercase letters. At least 3 lowercase letters are required."
    And the OCS status code should be "400"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "ABCabc1234" and the content should be "Alice file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "ABCabc1234" and the content should be "Alice file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "<password>" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "<password>" should fail with HTTP status code "401"
    Examples:
      | password   |
      | 0LOWERCASE |
      | 2lOWERcASE |
