@api
Feature: enforce the required number of numbers in a password when changing a user password

  As an administrator
  I want user passwords to always contain a required number of numbers
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "3"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | abcABC1234 | User One    | u1@oc.com.np |

  Scenario Outline: admin changes a user password to one that has enough numbers
    Given using OCS API version "<ocs-api-version>"
    When user "admin" sends HTTP method "PUT" to OCS API endpoint "/cloud/users/user1" with body
      | key   | password   |
      | value | <password> |
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    Examples:
      | password        | ocs-api-version | ocs-status |
      | 333Numbers      | 1               | 100        |
      | 333Numbers      | 2               | 200        |
      | moreNumbers1234 | 1               | 100        |
      | moreNumbers1234 | 2               | 200        |

  Scenario Outline: admin changes a user password to one that does not have enough numbers
    Given using OCS API version "<ocs-api-version>"
    When user "admin" sends HTTP method "PUT" to OCS API endpoint "/cloud/users/user1" with body
      | key   | password   |
      | value | <password> |
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "<http-status>"
    Examples:
      | password      | ocs-api-version | ocs-status | http-status |
      | NoNumbers     | 1               | 403        | 200         |
      | NoNumbers     | 2               | 403        | 403         |
      | Only22Numbers | 1               | 403        | 200         |
      | Only22Numbers | 2               | 403        | 403         |
