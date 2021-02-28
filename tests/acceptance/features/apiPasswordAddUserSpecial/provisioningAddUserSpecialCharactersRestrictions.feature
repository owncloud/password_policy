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
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "Alice" should exist
    And user "Alice" should be able to upload file "filesForUpload/textfile.txt" to "/textfile.txt"
    Examples:
      | password              | ocs-api-version | ocs-status |
      | 3$Special%Characters^ | 1               | 100        |
      | 3$Special%Characters^ | 2               | 200        |
      | 1*2&3^4%5$6           | 1               | 100        |
      | 1*2&3^4%5$6           | 2               | 200        |

  Scenario Outline: admin creates a user with a password that does not have enough restricted special characters
    Given using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password contains too few special characters. At least 3 special characters ($%^&*) are required.
      """
    And user "Alice" should not exist
    Examples:
      | password                 | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | NoSpecialCharacters123   | 1               | 101        | 200         | OK                 |
      | NoSpecialCharacters123   | 2               | 400        | 400         | Bad Request        |
      | Only2$Special&Characters | 1               | 101        | 200         | OK                 |
      | Only2$Special&Characters | 2               | 400        | 400         | Bad Request        |

  Scenario Outline: admin creates a user with a password that has invalid special characters
    Given using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password contains invalid special characters. Only $%^&* are allowed.
      """
    And user "Alice" should not exist
    Examples:
      | password                                 | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | Only#Invalid!Special@Characters          | 1               | 101        | 200         | OK                 |
      | Only#Invalid!Special@Characters          | 2               | 400        | 400         | Bad Request        |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! | 1               | 101        | 200         | OK                 |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! | 2               | 400        | 400         | Bad Request        |
