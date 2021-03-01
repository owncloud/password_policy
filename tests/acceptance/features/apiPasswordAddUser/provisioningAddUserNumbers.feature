@api
Feature: enforce the required number of numbers in a password when creating a user

  As an administrator
  I want user passwords to always contain a required number of numbers
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "3"

  Scenario Outline: admin creates a user with a password that has enough numbers
    Given using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "Alice" should exist
    And user "Alice" should be able to upload file "filesForUpload/textfile.txt" to "/textfile.txt"
    Examples:
      | password        | ocs-api-version | ocs-status |
      | 333Numbers      | 1               | 100        |
      | 333Numbers      | 2               | 200        |
      | moreNumbers1234 | 1               | 100        |
      | moreNumbers1234 | 2               | 200        |

  Scenario Outline: admin creates a user with a password that does not have enough numbers
    Given using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password contains too few numbers. At least 3 numbers are required.
      """
    And user "Alice" should not exist
    Examples:
      | password      | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | NoNumbers     | 1               | 101        | 200         | OK                 |
      | NoNumbers     | 2               | 400        | 400         | Bad Request        |
      | Only22Numbers | 1               | 101        | 200         | OK                 |
      | Only22Numbers | 2               | 400        | 400         | Bad Request        |
