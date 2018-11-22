@api
Feature: enforce the minimum length of a password when changing a user password

  As an administrator
  I want user passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And these users have been created with default attributes:
      | username | password   |
      | user1    | 1234567890 |

  Scenario Outline: admin changes a user password to one that is long enough
    Given using OCS API version "<ocs-api-version>"
    When user "admin" sends HTTP method "PUT" to OCS API endpoint "/cloud/users/user1" with body
      | key   | password   |
      | value | <password> |
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And the content of file "textfile0.txt" for user "user1" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "1234567890" should not be able to download file "textfile0.txt"
    Examples:
      | password             | ocs-api-version | ocs-status |
      | 10tenchars           | 1               | 100        |
      | 10tenchars           | 2               | 200        |
      | morethan10characters | 1               | 100        |
      | morethan10characters | 2               | 200        |

  Scenario Outline: admin changes a user password to one that is not long enough
    Given using OCS API version "<ocs-api-version>"
    When user "admin" sends HTTP method "PUT" to OCS API endpoint "/cloud/users/user1" with body
      | key   | password   |
      | value | <password> |
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password is too short. At least 10 characters are required.
      """
    And the content of file "textfile0.txt" for user "user1" using password "1234567890" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "<password>" should not be able to download file "textfile0.txt"
    Examples:
      | password  | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | A         | 1               | 403        | 200         | OK                 |
      | A         | 2               | 403        | 403         | Forbidden          |
      | 123456789 | 1               | 403        | 200         | OK                 |
      | 123456789 | 2               | 403        | 403         | Forbidden          |
