@api
Feature: enforce the required number of restricted special characters in a password when changing a user password

  As an administrator
  I want user passwords to always contain some of a restricted list of special characters
  So that users cannot set passwords that have unusual hard-to-type characters

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | a$b%c^1234 | User One    | u1@oc.com.np |

  Scenario Outline: admin changes a user password to one that has enough restricted special characters
    Given using OCS API version "<ocs-api-version>"
    When user "admin" sends HTTP method "PUT" to OCS API endpoint "/cloud/users/user1" with body
      | key   | password   |
      | value | <password> |
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    Examples:
      | password              | ocs-api-version | ocs-status |
      | 3$Special%Characters^ | 1               | 100        |
      | 3$Special%Characters^ | 2               | 200        |
      | 1*2&3^4%5$6           | 1               | 100        |
      | 1*2&3^4%5$6           | 2               | 200        |

  Scenario Outline: admin changes a user password to one that does not have enough restricted special characters
    Given using OCS API version "<ocs-api-version>"
    When user "admin" sends HTTP method "PUT" to OCS API endpoint "/cloud/users/user1" with body
      | key   | password   |
      | value | <password> |
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password contains too few special characters. At least 3 special characters ($%^&*) are required.
      """
    Examples:
      | password                 | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | NoSpecialCharacters123   | 1               | 403        | 200         | OK                 |
      | NoSpecialCharacters123   | 2               | 403        | 403         | Forbidden          |
      | Only2$Special&Characters | 1               | 403        | 200         | OK                 |
      | Only2$Special&Characters | 2               | 403        | 403         | Forbidden          |

  Scenario Outline: admin changes a user password to one that has invalid special characters
    Given using OCS API version "<ocs-api-version>"
    When user "admin" sends HTTP method "PUT" to OCS API endpoint "/cloud/users/user1" with body
      | key   | password   |
      | value | <password> |
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password contains invalid special characters. Only $%^&* are allowed.
      """
    Examples:
      | password                                 | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | Only#Invalid!Special@Characters          | 1               | 403        | 200         | OK                 |
      | Only#Invalid!Special@Characters          | 2               | 403        | 403         | Forbidden          |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! | 1               | 403        | 200         | OK                 |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! | 2               | 403        | 403         | Forbidden          |
