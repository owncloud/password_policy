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
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "Alice" should exist
    And the content of file "textfile0.txt" for user "Alice" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    Examples:
      | password             | ocs-api-version | ocs-status |
      | 10tenchars           | 1               | 100        |
      | 10tenchars           | 2               | 200        |
      | morethan10characters | 1               | 100        |
      | morethan10characters | 2               | 200        |

  Scenario Outline: admin creates a user with a password that is not long enough
    Given using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password is too short. At least 10 characters are required.
      """
    And user "Alice" should not exist
    Examples:
      | password  | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | A         | 1               | 101        | 200         | OK                 |
      | A         | 2               | 400        | 400         | Bad Request        |
      | 123456789 | 1               | 101        | 200         | OK                 |
      | 123456789 | 2               | 400        | 400         | Bad Request        |
