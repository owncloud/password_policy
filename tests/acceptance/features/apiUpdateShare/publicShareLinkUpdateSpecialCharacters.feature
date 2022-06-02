@api
Feature: enforce the required number of special characters in a password on public link shares

  As an administrator
  I want public link share passwords to always contain a required number of special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And these users have been created with default attributes and small skeleton files:
      | username | password   |
      | Alice    | a!b@c#1234 |
    And user "Alice" has uploaded file with content "Alice file" to "/randomfile.txt"
    And user "Alice" has created a public link share with settings
      | path     | randomfile.txt |
      | password | g@b#c!1234     |


  Scenario Outline: user updates the public link share password to a string with enough special characters
    When user "Alice" updates the last public link share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "<password>" and the content should be "Alice file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "<password>" and the content should be "Alice file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "g@b#c!1234" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "g@b#c!1234" should fail with HTTP status code "401"
    Examples:
      | password              |
      | 3#Special$Characters! |
      | 1!2@3#4$5%6^7&8*      |


  Scenario Outline: user tries to update the public link share password to a string that has too few special characters
    When user "Alice" tries to update the last public link share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "The password contains too few special characters. At least 3 special characters are required."
    And the OCS status code should be "400"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "g@b#c!1234" and the content should be "Alice file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "g@b#c!1234" and the content should be "Alice file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "<password>" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "<password>" should fail with HTTP status code "401"
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special!Characters |
