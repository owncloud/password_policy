@api
Feature: enforce the required number of lowercase letters in a password when creating a user

  As an administrator
  I want user passwords to always contain a required number of lowercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "3"

  Scenario Outline: admin creates a user with a password that has enough lowercase letters
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "brand-new-user" should exist
    Examples:
      | password                  | ocs-api-version | ocs-status |
      | 3LCase                    | 1               | 100        |
      | 3LCase                    | 2               | 200        |
      | moreThan3LowercaseLetters | 1               | 100        |
      | moreThan3LowercaseLetters | 2               | 200        |

  Scenario Outline: admin creates a user with a password that does not have enough lowercase letters
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "<http-status>"
    And user "brand-new-user" should not exist
    Examples:
      | password   | ocs-api-version | ocs-status | http-status |
      | 0LOWERCASE | 1               | 101        | 200         |
      | 0LOWERCASE | 2               | 400        | 400         |
      | 2lOWERcASE | 1               | 101        | 200         |
      | 2lOWERcASE | 2               | 400        | 400         |
