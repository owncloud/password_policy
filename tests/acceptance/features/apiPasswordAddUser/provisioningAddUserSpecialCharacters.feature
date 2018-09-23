@api
Feature: enforce the required number of special characters in a password when creating a user

  As an administrator
  I want user passwords to always contain a required number of special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"

  Scenario Outline: admin creates a user with a password that has enough special characters
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "brand-new-user" should exist
    And the content of file "textfile0.txt" for user "brand-new-user" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    Examples:
      | password              | ocs-api-version | ocs-status |
      | 3#Special$Characters! | 1               | 100        |
      | 3#Special$Characters! | 2               | 200        |
      | 1!2@3#4$5%6^7&8*      | 1               | 100        |
      | 1!2@3#4$5%6^7&8*      | 2               | 200        |

  Scenario Outline: admin creates a user with a password that does not have enough special characters
    Given using OCS API version "<ocs-api-version>"
    And user "brand-new-user" has been deleted
    When the administrator sends a user creation request for user "brand-new-user" password "<password>" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be:
      """
      The password contains too few special characters. At least 3 special characters are required.
      """
    And user "brand-new-user" should not exist
    Examples:
      | password                 | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | NoSpecialCharacters123   | 1               | 101        | 200         | OK                 |
      | NoSpecialCharacters123   | 2               | 400        | 400         | Bad Request        |
      | Only2$Special!Characters | 1               | 101        | 200         | OK                 |
      | Only2$Special!Characters | 2               | 400        | 400         | Bad Request        |
