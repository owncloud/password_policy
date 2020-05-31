@api
Feature: enforce the number of last passwords that must not be used when resetting the password using the provisioning API

  As an administrator
  I want to prevent users from re-using recent passwords
  So that recent passwords (that may have been compromised) cannot be used to access data

  Background:
    Given the administrator has enabled the last passwords user password policy
    And the administrator has set the number of last passwords that should not be used to "3"
    And these users have been created with default attributes and skeleton files:
      | username | password |
      | Alice    | Number1  |

  Scenario Outline: admin resets the password of a user to the existing password
    Given using OCS API version "<ocs-api-version>"
    When the administrator resets the password of user "Alice" to "Number1" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be "The password must be different than your previous 3 passwords."
    And the content of file "textfile0.txt" for user "Alice" using password "Number1" should be "ownCloud test text file 0" plus end-of-line
    Examples:
      | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | 1               | 403        | 200         | OK                 |
      | 2               | 403        | 403         | Forbidden          |

  Scenario Outline: admin resets the password of a user to the 1st of 2 passwords
    Given using OCS API version "<ocs-api-version>"
    And the administrator has reset the password of user "Alice" to "Number2"
    When the administrator resets the password of user "Alice" to "Number1" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be "The password must be different than your previous 3 passwords."
    And the content of file "textfile0.txt" for user "Alice" using password "Number2" should be "ownCloud test text file 0" plus end-of-line
    But user "Alice" using password "Number1" should not be able to download file "textfile0.txt"
    Examples:
      | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | 1               | 403        | 200         | OK                 |
      | 2               | 403        | 403         | Forbidden          |

  Scenario Outline: admin resets the password of a user to the 2nd of 2 passwords
    Given using OCS API version "<ocs-api-version>"
    And the administrator has reset the password of user "Alice" to "Number2"
    When the administrator resets the password of user "Alice" to "Number2" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be "The password must be different than your previous 3 passwords."
    And the content of file "textfile0.txt" for user "Alice" using password "Number2" should be "ownCloud test text file 0" plus end-of-line
    But user "Alice" using password "Number1" should not be able to download file "textfile0.txt"
    Examples:
      | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | 1               | 403        | 200         | OK                 |
      | 2               | 403        | 403         | Forbidden          |

  Scenario Outline: admin resets the password of a user to the 2nd of 3 passwords
    Given using OCS API version "<ocs-api-version>"
    And the administrator has reset the password of user "Alice" to "Number2"
    And the administrator has reset the password of user "Alice" to "Number3"
    When the administrator resets the password of user "Alice" to "Number2" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be "The password must be different than your previous 3 passwords."
    And the content of file "textfile0.txt" for user "Alice" using password "Number3" should be "ownCloud test text file 0" plus end-of-line
    But user "Alice" using password "Number2" should not be able to download file "textfile0.txt"
    Examples:
      | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | 1               | 403        | 200         | OK                 |
      | 2               | 403        | 403         | Forbidden          |

  Scenario Outline: admin resets the password of a user to the 2nd of 4 passwords
    Given using OCS API version "<ocs-api-version>"
    And the administrator has reset the password of user "Alice" to "Number2"
    And the administrator has reset the password of user "Alice" to "Number3"
    And the administrator has reset the password of user "Alice" to "Number4"
    When the administrator resets the password of user "Alice" to "Number2" using the provisioning API
    Then the HTTP status code should be "<http-status>"
    And the HTTP reason phrase should be "<http-reason-phrase>"
    And the OCS status code should be "<ocs-status>"
    And the OCS status message should be "The password must be different than your previous 3 passwords."
    And the content of file "textfile0.txt" for user "Alice" using password "Number4" should be "ownCloud test text file 0" plus end-of-line
    But user "Alice" using password "Number2" should not be able to download file "textfile0.txt"
    Examples:
      | ocs-api-version | ocs-status | http-status | http-reason-phrase |
      | 1               | 403        | 200         | OK                 |
      | 2               | 403        | 403         | Forbidden          |

  Scenario Outline: admin resets the password of a user to the 1st of 4 passwords - the password can be reused
    Given using OCS API version "<ocs-api-version>"
    And the administrator has reset the password of user "Alice" to "Number2"
    And the administrator has reset the password of user "Alice" to "Number3"
    And the administrator has reset the password of user "Alice" to "Number4"
    When the administrator resets the password of user "Alice" to "Number1" using the provisioning API
    Then the HTTP status code should be "200"
    And the OCS status code should be "<ocs-status>"
    And the content of file "textfile0.txt" for user "Alice" using password "Number1" should be "ownCloud test text file 0" plus end-of-line
    But user "Alice" using password "Number4" should not be able to download file "textfile0.txt"
    Examples:
      | ocs-api-version | ocs-status |
      | 1               | 100        |
      | 2               | 200        |

  Scenario Outline: admin resets the password of a user to a value different to any previous passwords - the password can be reused
    Given using OCS API version "<ocs-api-version>"
    And the administrator has reset the password of user "Alice" to "Number2"
    And the administrator has reset the password of user "Alice" to "Number3"
    And the administrator has reset the password of user "Alice" to "Number4"
    When the administrator resets the password of user "Alice" to "AnotherValue" using the provisioning API
    Then the HTTP status code should be "200"
    And the OCS status code should be "<ocs-status>"
    And the content of file "textfile0.txt" for user "Alice" using password "AnotherValue" should be "ownCloud test text file 0" plus end-of-line
    But user "Alice" using password "Number4" should not be able to download file "textfile0.txt"
    Examples:
      | ocs-api-version | ocs-status |
      | 1               | 100        |
      | 2               | 200        |
