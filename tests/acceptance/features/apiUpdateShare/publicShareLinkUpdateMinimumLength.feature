@api
Feature: enforce the minimum length of a password on public share links

  As an administrator
  I want public share link passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | 1234567890 | User One    | u1@oc.com.np |
    And user "user1" has created a public link share with settings
      | path     | welcome.txt |
      | password | ABCabc1234  |

  Scenario Outline: user updates the public share link password a long-enough string
    When user "user1" updates the last share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the last public shared file should be able to be downloaded with password "<password>"
    And the last public shared file should not be able to be downloaded with password "ABCabc1234"
    Examples:
      | password             |
      | 10tenchars           |
      | morethan10characters |

  Scenario Outline: user tries to update the public share link password to a string that is too short
    When user "user1" tries to update the last share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "The password is too short. At least 10 characters are required."
    And the OCS status code should be "400"
    And the last public shared file should be able to be downloaded with password "ABCabc1234"
    And the last public shared file should not be able to be downloaded with password "<password>"
    Examples:
      | password  |
      | A         |
      | 123456789 |
