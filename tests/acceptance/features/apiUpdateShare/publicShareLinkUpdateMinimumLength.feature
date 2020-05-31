@api
Feature: enforce the minimum length of a password on public share links

  As an administrator
  I want public share link passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And these users have been created with default attributes and skeleton files:
      | username | password   |
      | Alice    | 1234567890 |
    And user "Alice" has uploaded file with content "Alice file" to "/randomfile.txt"
    And user "Alice" has created a public link share with settings
      | path     | randomfile.txt |
      | password | ABCabc1234     |

  @skipOnOcV10.2
  Scenario Outline: user updates the public share link password a long-enough string
    When user "Alice" updates the last share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "<password>" and the content should be "Alice file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "<password>" and the content should be "Alice file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "ABCabc1234" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "ABCabc1234" should fail with HTTP status code "401"
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |

  @skipOnOcV10.3
  # This scenario repeats the one above, but without checking the new public WebDAV API.
  # It works against core 10.2.1. Delete the scenario when testing against 10.2.1 is no longer required.
  Scenario Outline: user updates the public share link password a long-enough string
    When user "Alice" updates the last share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "<password>" and the content should be "Alice file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "ABCabc1234" should fail with HTTP status code "401"
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |

  @skipOnOcV10.2
  Scenario Outline: user tries to update the public share link password to a string that is too short
    When user "Alice" tries to update the last share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "The password is too short. At least 10 characters are required."
    And the OCS status code should be "400"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "ABCabc1234" and the content should be "Alice file"
    And the public should be able to download the last publicly shared file using the new public WebDAV API with password "ABCabc1234" and the content should be "Alice file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "<password>" should fail with HTTP status code "401"
    And the public download of the last publicly shared file using the new public WebDAV API with password "<password>" should fail with HTTP status code "401"
    Examples:
      | password  |
      | A         |
      | 123456789 |

  @skipOnOcV10.3
  # This scenario repeats the one above, but without checking the new public WebDAV API.
  # It works against core 10.2.1. Delete the scenario when testing against 10.2.1 is no longer required.
  Scenario Outline: user tries to update the public share link password to a string that is too short
    When user "Alice" tries to update the last share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "The password is too short. At least 10 characters are required."
    And the OCS status code should be "400"
    And the public should be able to download the last publicly shared file using the old public WebDAV API with password "ABCabc1234" and the content should be "Alice file"
    And the public download of the last publicly shared file using the old public WebDAV API with password "<password>" should fail with HTTP status code "401"
    Examples:
      | password  |
      | A         |
      | 123456789 |
