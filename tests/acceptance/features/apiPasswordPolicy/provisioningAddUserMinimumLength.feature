@api
Feature: enforce the minimum length of a password when creating a user

  As an administrator
  I want user passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"

  Scenario Outline: admin creates a user with a password that is long enough
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "brand-new-user" should exist
    Examples:
      | password             | ocs-api-version | ocs-status |
      | 10tenchars           | 1               | 100        |
      | 10tenchars           | 2               | 200        |
      | morethan10characters | 1               | 100        |
      | morethan10characters | 2               | 200        |

  Scenario Outline: admin creates a user with a password that is not long enough
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "<http-status>"
    And user "brand-new-user" should not exist
    Examples:
      | password  | ocs-api-version | ocs-status | http-status |
      | A         | 1               | 101        | 200         |
      | A         | 2               | 400        | 400         |
      | 123456789 | 1               | 101        | 200         |
      | 123456789 | 2               | 400        | 400         |
