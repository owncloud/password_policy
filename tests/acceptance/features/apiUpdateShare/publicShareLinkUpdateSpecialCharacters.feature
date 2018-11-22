@api
Feature: enforce the required number of special characters in a password on public share links

  As an administrator
  I want public share link passwords to always contain a required number of special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And these users have been created with default attributes:
      | username | password   |
      | user1    | a!b@c#1234 |
    And user "user1" has created a public link share with settings
      | path     | welcome.txt |
      | password | g@b#c!1234  |

  Scenario Outline: user updates the public share link password to a string with enough special characters
    When user "user1" updates the last share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the last public shared file should be able to be downloaded with password "<password>"
    And the last public shared file should not be able to be downloaded with password "g@b#c!1234"
    Examples:
      | password              |
      | 3#Special$Characters! |
      | 1!2@3#4$5%6^7&8*      |

  Scenario Outline: user tries to update the public share link password to a string that has too few special characters
    When user "user1" tries to update the last share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "The password contains too few special characters. At least 3 special characters are required."
    And the OCS status code should be "400"
    And the last public shared file should be able to be downloaded with password "g@b#c!1234"
    And the last public shared file should not be able to be downloaded with password "<password>"
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special!Characters |
