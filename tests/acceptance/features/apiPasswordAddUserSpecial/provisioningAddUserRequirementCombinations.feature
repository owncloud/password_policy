@api
Feature: enforce combinations of password policies when creating a user

  As an administrator
  I want user passwords to always have some combination of minimum length, lowercase, uppercase, numbers and special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "15"
    And the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "4"
    And the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "2"
    And the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"


  Scenario Outline: admin creates a user with a password that meets the password policy
    Given using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "Alice" should exist
    And user "Alice" should be able to upload file "filesForUpload/textfile.txt" to "/textfile.txt"
    Examples:
      | password                  | ocs-api-version | ocs-status |
      | 15***UPPloweZZZ           | 1               | 100        |
      | 15***UPPloweZZZ           | 2               | 200        |
      | More%Than$15!Characters-0 | 1               | 100        |
      | More%Than$15!Characters-0 | 2               | 200        |


  Scenario Outline: admin creates a user with a password that does not meet the password policy
    Given using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be "<ocs-status-message>"
    And user "Alice" should not exist
    Examples:
      | password                       | ocs-api-version | ocs-status | http-status | http-reason-phrase | ocs-status-message                                                                            |
        # just one of the requirements is not met
      | aA1!bB2#cC&d                   | 1               | 101        | 200         | OK                 | The password is too short. At least 15 characters are required.                               |
      | aA1!bB2#cC&d                   | 2               | 400        | 400         | Bad Request        | The password is too short. At least 15 characters are required.                               |
      | aA1!bB2#cNOT&ENOUGH#LOWERCASE  | 1               | 101        | 200         | OK                 | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |
      | aA1!bB2#cNOT&ENOUGH#LOWERCASE  | 2               | 400        | 400         | Bad Request        | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |
      | aA1!bB2#cnot&enough#uppercase  | 1               | 101        | 200         | OK                 | The password contains too few uppercase letters. At least 3 uppercase letters are required.   |
      | aA1!bB2#cnot&enough#uppercase  | 2               | 400        | 400         | Bad Request        | The password contains too few uppercase letters. At least 3 uppercase letters are required.   |
      | Not&Enough#Numbers=1           | 1               | 101        | 200         | OK                 | The password contains too few numbers. At least 2 numbers are required.                       |
      | Not&Enough#Numbers=1           | 2               | 400        | 400         | Bad Request        | The password contains too few numbers. At least 2 numbers are required.                       |
      | Not&Enough#Special8Characters2 | 1               | 101        | 200         | OK                 | The password contains too few special characters. At least 3 special characters are required. |
      | Not&Enough#Special8Characters2 | 2               | 400        | 400         | Bad Request        | The password contains too few special characters. At least 3 special characters are required. |
        # multiple requirements are not met
      | aA!1                           | 1               | 101        | 200         | OK                 | The password is too short. At least 15 characters are required.                               |
      | aA!1                           | 2               | 400        | 400         | Bad Request        | The password is too short. At least 15 characters are required.                               |
      | aA!123456789012345             | 1               | 101        | 200         | OK                 | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |
      | aA!123456789012345             | 2               | 400        | 400         | Bad Request        | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |


  Scenario Outline: admin creates a user with a password that has valid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    And using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the OCS status code should be "<ocs-status>"
    And the HTTP status code should be "200"
    And user "Alice" should exist
    And user "Alice" should be able to upload file "filesForUpload/textfile.txt" to "/textfile.txt"
    Examples:
      | password                  | ocs-api-version | ocs-status |
      | 15%&*UPPloweZZZ           | 1               | 100        |
      | 15%&*UPPloweZZZ           | 2               | 200        |
      | More^Than$15&Characters*0 | 1               | 100        |
      | More^Than$15&Characters*0 | 2               | 200        |


  Scenario Outline: admin creates a user with a password that has invalid restricted special characters
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    And using OCS API version "<ocs-api-version>"
    And user "Alice" has been deleted
    When the administrator sends a user creation request for user "Alice" password "<password>" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be "<ocs-status-message>"
    And user "Alice" should not exist
    Examples:
      | password        | ocs-api-version | ocs-status | http-status | http-reason-phrase | ocs-status-message                                                                          |
      | 15#!!UPPloweZZZ | 1               | 101        | 200         | OK                 | The password contains invalid special characters. Only $%^&* are allowed.                   |
      | 15#!!UPPloweZZZ | 2               | 400        | 400         | Bad Request        | The password contains invalid special characters. Only $%^&* are allowed.                   |
      | 15&%!UPPloweZZZ | 1               | 101        | 200         | OK                 | The password contains invalid special characters. Only $%^&* are allowed.                   |
      | 15&%!UPPloweZZZ | 2               | 400        | 400         | Bad Request        | The password contains invalid special characters. Only $%^&* are allowed.                   |
        # multiple requirements are not met
      | 15&%!UPPlowZZZZ | 1               | 101        | 200         | OK                 | The password contains too few lowercase letters. At least 4 lowercase letters are required. |
      | 15&%!UPPlowZZZZ | 2               | 400        | 400         | Bad Request        | The password contains too few lowercase letters. At least 4 lowercase letters are required. |
