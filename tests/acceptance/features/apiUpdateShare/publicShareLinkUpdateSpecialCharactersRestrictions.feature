@api
Feature: enforce the restricted special characters in a password on public share links

  As an administrator
  I want public share link passwords to always contain some of a restricted list of special characters
  So that users cannot set passwords that have unusual hard-to-type characters

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | a$b%c^1234 | User One    | u1@oc.com.np |
    And user "user1" has created a public link share with settings
      | path     | welcome.txt   |
      | password | a324$b%c^1234 |

  Scenario Outline: user updates the public share link password to a string with enough restricted special characters
    When user "user1" updates the last share using the sharing API with
      | password | <password> |
    Then the OCS status code should be "100"
    And the HTTP status code should be "200"
    And the last public shared file should be able to be downloaded with password "<password>"
    And the last public shared file should not be able to be downloaded with password "a324$b%c^1234"
    Examples:
      | password              |
      | 3$Special%Characters^ |
      | 1*2&3^4%5$6           |

  Scenario Outline: user tries to update the public share link password to a string that has too few restricted special characters
    When user "user1" tries to update the last share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "The password contains too few special characters. At least 3 special characters ($%^&*) are required."
    And the OCS status code should be "400"
    And the last public shared file should be able to be downloaded with password "a324$b%c^1234"
    And the last public shared file should not be able to be downloaded with password "<password>"
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special&Characters |

  Scenario Outline: user tries to update the public share link password to a string that has invalid special characters
    When user "user1" tries to update the last share using the sharing API with
      | password | <password> |
    Then the OCS status message should be "The password contains invalid special characters. Only $%^&* are allowed."
    And the OCS status code should be "400"
    And the last public shared file should be able to be downloaded with password "a324$b%c^1234"
    And the last public shared file should not be able to be downloaded with password "<password>"
    Examples:
      | password                                 |
      | Only#Invalid!Special@Characters          |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! |
