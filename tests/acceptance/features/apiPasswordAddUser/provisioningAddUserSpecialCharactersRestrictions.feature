@api
Feature: enforce the restricted special characters in a password when creating a user

  As an administrator
  I want user passwords to always contain some of a restricted list of special characters
  So that users cannot set passwords that have unusual hard-to-type characters

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"

  Scenario Outline: admin creates a user with a password that has enough restricted special characters
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "brand-new-user" should exist
    Examples:
      | password              | ocs-api-version | ocs-status |
      | 3$Special%Characters^ | 1               | 100        |
      | 3$Special%Characters^ | 2               | 200        |
      | 1*2&3^4%5$6           | 1               | 100        |
      | 1*2&3^4%5$6           | 2               | 200        |

  Scenario Outline: admin creates a user with a password that does not have enough restricted special characters
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "<http-status>"
    And user "brand-new-user" should not exist
    Examples:
      | password                 | ocs-api-version | ocs-status | http-status |
      | NoSpecialCharacters123   | 1               | 101        | 200         |
      | NoSpecialCharacters123   | 2               | 400        | 400         |
      | Only2$Special&Characters | 1               | 101        | 200         |
      | Only2$Special&Characters | 2               | 400        | 400         |

  Scenario Outline: admin creates a user with a password that has invalid special characters
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "<http-status>"
    And user "brand-new-user" should not exist
    Examples:
      | password                                 | ocs-api-version | ocs-status | http-status |
      | Only#Invalid!Special@Characters          | 1               | 101        | 200         |
      | Only#Invalid!Special@Characters          | 2               | 400        | 400         |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! | 1               | 101        | 200         |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! | 2               | 400        | 400         |
