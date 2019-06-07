@api
Feature: enforce the required number of uppercase letters in a password when changing a user password

  As an administrator
  I want user passwords to always contain a required number of uppercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And these users have been created with default attributes and skeleton files:
      | username | password   |
      | user1    | abcABC1234 |

  Scenario Outline: admin changes a user password to one that has enough uppercase letters
    Given using OCS API version "<ocs-api-version>"
    When user "admin" sends HTTP method "PUT" to OCS API endpoint "/cloud/users/user1" with body
      | key   | password   |
      | value | <password> |
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And the content of file "textfile0.txt" for user "user1" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "abcABC1234" should not be able to download file "textfile0.txt"
    Examples:
      | password                  | ocs-api-version | ocs-status |
      | 3UpperCaseLetters         | 1               | 100        |
      | 3UpperCaseLetters         | 2               | 200        |
      | MoreThan3UpperCaseLetters | 1               | 100        |
      | MoreThan3UpperCaseLetters | 2               | 200        |

  Scenario Outline: admin changes a user password to one that does not have enough uppercase letters
    Given using OCS API version "<ocs-api-version>"
    When user "admin" sends HTTP method "PUT" to OCS API endpoint "/cloud/users/user1" with body
      | key   | password   |
      | value | <password> |
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password contains too few uppercase letters. At least 3 uppercase letters are required.
      """
    And the content of file "textfile0.txt" for user "user1" using password "abcABC1234" should be "ownCloud test text file 0" plus end-of-line
    But user "user1" using password "<password>" should not be able to download file "textfile0.txt"
    Examples:
      | password       | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | 0uppercase     | 1               | 403        | 200         | OK                 |
      | 0uppercase     | 2               | 403        | 403         | Forbidden          |
      | Only2Uppercase | 1               | 403        | 200         | OK                 |
      | Only2Uppercase | 2               | 403        | 403         | Forbidden          |
