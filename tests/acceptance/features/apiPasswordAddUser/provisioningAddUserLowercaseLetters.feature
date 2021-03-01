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
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "Alice" should exist
    And user "Alice" should be able to upload file "filesForUpload/textfile.txt" to "/textfile.txt"
    Examples:
      | password                  | ocs-api-version | ocs-status |
      | 3LCase                    | 1               | 100        |
      | 3LCase                    | 2               | 200        |
      | moreThan3LowercaseLetters | 1               | 100        |
      | moreThan3LowercaseLetters | 2               | 200        |

  Scenario Outline: admin creates a user with a password that does not have enough lowercase letters
    Given using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password contains too few lowercase letters. At least 3 lowercase letters are required.
      """
    And user "Alice" should not exist
    Examples:
      | password   | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | 0LOWERCASE | 1               | 101        | 200         | OK                 |
      | 0LOWERCASE | 2               | 400        | 400         | Bad Request        |
      | 2lOWERcASE | 1               | 101        | 200         | OK                 |
      | 2lOWERcASE | 2               | 400        | 400         | Bad Request        |
