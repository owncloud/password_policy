@api
Feature: enforce the required number of uppercase letters in a password when creating a user

  As an administrator
  I want user passwords to always contain a required number of uppercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"

  Scenario Outline: admin creates a user with a password that has enough uppercase letters
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "brand-new-user" should exist
    Examples:
      | password                  | ocs-api-version | ocs-status |
      | 3UpperCaseLetters         | 1               | 100        |
      | 3UpperCaseLetters         | 2               | 200        |
      | MoreThan3UpperCaseLetters | 1               | 100        |
      | MoreThan3UpperCaseLetters | 2               | 200        |

  Scenario Outline: admin creates a user with a password that does not have enough uppercase letters
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "<http-status>"
    And user "brand-new-user" should not exist
    Examples:
      | password       | ocs-api-version | ocs-status | http-status |
      | 0uppercase     | 1               | 101        | 200         |
      | 0uppercase     | 2               | 400        | 400         |
      | Only2Uppercase | 1               | 101        | 200         |
      | Only2Uppercase | 2               | 400        | 400         |
