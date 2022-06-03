@api
Feature: enforce public link expiration policies

  As an administrator
  I want to enforce expiration date for public shares
  So public shares have default expiration if the expiration date is not changed

  Background:
    Given user "Alice" has been created with default attributes and small skeleton files

  Scenario Outline: user tries to create a public share without expiration date and password when "days maximum until link expires if password is not set" is enabled
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is not set public link password policy
    When user "Alice" creates a public link share of folder "PARENT" using the sharing API
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the value of the item "//message" in the response should match "/An expiration date is required./"
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 403        | 200         |
      | 2               | 403        | 403         |

  Scenario Outline: user creates a public share without expiration date when "days maximum until link expires if password is not set" is enabled
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is not set public link password policy
    When user "Alice" creates a public link share using the sharing API with settings
      | password | abcdefgh |
      | path     | PARENT   |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the public should be able to download file "parent.txt" from inside the last public link shared folder using the new public WebDAV API with password "abcdefgh" and the content should be "ownCloud test text file parent" plus end-of-line
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 100        | 200         |
      | 2               | 200        | 200         |

  Scenario Outline: user creates a public share without expiration date and password when "days maximum until link expires if password is set" is enabled
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is set public link password policy
    When user "Alice" creates a public link share using the sharing API with settings
      | path | PARENT |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the public should be able to download file "parent.txt" from inside the last public link shared folder using the new public WebDAV API with password "abcdefgh" and the content should be "ownCloud test text file parent" plus end-of-line
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 100        | 200         |
      | 2               | 200        | 200         |


  Scenario Outline: user tries to create a public share without expiration date when "days maximum until link expires if password is set" is enabled
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is set public link password policy
    When user "Alice" creates a public link share using the sharing API with settings
      | path     | PARENT   |
      | password | abcdefgh |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the value of the item "//message" in the response should match "/An expiration date is required./"
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 403        | 200         |
      | 2               | 403        | 403         |

  Scenario Outline: user tries to create a public share without password but with expiration date exceeding the default maximum days when "days maximum until link expires if password is not set" is enabled
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is not set public link password policy
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +10 days |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the value of the item "//message" in the response should match "/The expiration date cannot exceed 7 days./"
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 403        | 200         |
      | 2               | 403        | 403         |

  Scenario Outline: user creates a public share with password but with expiration date exceeding the default maximum days when "days maximum until link expires if password is not set" is enabled
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is not set public link password policy
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +10 days |
      | password   | abcdefgh |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the public should be able to download file "parent.txt" from inside the last public link shared folder using the new public WebDAV API with password "abcdefgh" and the content should be "ownCloud test text file parent" plus end-of-line
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 100        | 200         |
      | 2               | 200        | 200         |

  Scenario Outline: user creates a public share without password but expiration date exceeding the default maximum days when "days maximum until link expires if password is set" is enabled
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is set public link password policy
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +10 days |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the public should be able to download file "parent.txt" from inside the last public link shared folder using the new public WebDAV API with password "abcdefgh" and the content should be "ownCloud test text file parent" plus end-of-line
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 100        | 200         |
      | 2               | 200        | 200         |

  Scenario Outline: user tries to create a public share with password but expiration date exceeding the default maximum days when "days maximum until link expires if password is set" is enabled
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is set public link password policy
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +10 days |
      | password   | abcdefgh |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the value of the item "//message" in the response should match "/The expiration date cannot exceed 7 days./"
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 403        | 200         |
      | 2               | 403        | 403         |

  Scenario Outline: user creates a public share with expiration date when "days maximum until link expires if password is not set" is enabled and the default maximum days is changed
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is not set public link password policy
    And the administrator has set the value for the maximum days until link expires if password is not set to "10"
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +10 days |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the public should be able to download file "parent.txt" from inside the last public link shared folder using the new public WebDAV API with password "abcdefgh" and the content should be "ownCloud test text file parent" plus end-of-line
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 100        | 200         |
      | 2               | 200        | 200         |

  Scenario Outline: user creates a public share with password and expiration date when "days maximum until link expires if password is set" is enabled and the default maximum days is changed
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is set public link password policy
    And the administrator has set the value for the maximum days until link expires if password is set to "10"
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +10 days |
      | password   | abcdefgh |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the public should be able to download file "parent.txt" from inside the last public link shared folder using the new public WebDAV API with password "abcdefgh" and the content should be "ownCloud test text file parent" plus end-of-line
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 100        | 200         |
      | 2               | 200        | 200         |

  Scenario Outline: user tries to create a public share with expiration date exceeding the default value when "days maximum until link expires if password is not set" is enabled and the default maximum days is changed
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is not set public link password policy
    And the administrator has set the value for the maximum days until link expires if password is not set to "2"
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +10 days |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the value of the item "//message" in the response should match "/The expiration date cannot exceed 2 days./"
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 403        | 200         |
      | 2               | 403        | 403         |

  Scenario Outline: user tries to create a public share with password, and expiration date exceeding the default value when "days maximum until link expires if password is set" is enabled and the default maximum days is changed
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is set public link password policy
    And the administrator has set the value for the maximum days until link expires if password is set to "2"
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +10 days |
      | password   | abcdefgh |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the value of the item "//message" in the response should match "/The expiration date cannot exceed 2 days./"
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 403        | 200         |
      | 2               | 403        | 403         |

  Scenario Outline: user tries to create a public share with expiration date exceeding the default value when "days maximum until link expires if password is not set" is disabled but the default maximum days is changed
    Given using OCS API version "<ocs-api-version>"
    And the administrator has set the value for the maximum days until link expires if password is not set to "10"
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +20 days |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the public should be able to download file "parent.txt" from inside the last public link shared folder using the new public WebDAV API with password "abcdefgh" and the content should be "ownCloud test text file parent" plus end-of-line
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 100        | 200         |
      | 2               | 200        | 200         |

  Scenario Outline: user tries to create a public share with password, and expiration date exceeding the default value when "days maximum until link expires if password is not set" is disabled but the default maximum days is changed
    Given using OCS API version "<ocs-api-version>"
    And the administrator has set the value for the maximum days until link expires if password is set to "10"
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +20 days |
      | password   | abcdefgh |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the public should be able to download file "parent.txt" from inside the last public link shared folder using the new public WebDAV API with password "abcdefgh" and the content should be "ownCloud test text file parent" plus end-of-line
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 100        | 200         |
      | 2               | 200        | 200         |

  Scenario Outline: user decreases the default maximum days until link expires if password is set and then edits expiration date of the already created public link
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is set public link password policy
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT   |
      | expireDate | +6 days  |
      | password   | abcdefgh |
    And the administrator has set the value for the maximum days until link expires if password is set to "3"
    When user "Alice" updates the last public link share using the sharing API with
      | expireDate | +5 days |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the value of the item "//message" in the response should match "/The expiration date cannot exceed 3 days./"
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 400        | 200         |
      | 2               | 400        | 400         |

  Scenario Outline: user decreases the default maximum days until link expires if password is not set and then edits expiration date of the already created public link
    Given using OCS API version "<ocs-api-version>"
    And the administrator has enabled the days until link expires if password is not set public link password policy
    When user "Alice" creates a public link share using the sharing API with settings
      | path       | PARENT  |
      | expireDate | +6 days |
    And the administrator has set the value for the maximum days until link expires if password is not set to "3"
    When user "Alice" updates the last public link share using the sharing API with
      | expireDate | +5 days |
    Then the HTTP status code should be "<http-status>"
    And the OCS status code should be "<ocs-status>"
    And the value of the item "//message" in the response should match "/The expiration date cannot exceed 3 days./"
    Examples:
      | ocs-api-version | ocs-status | http-status |
      | 1               | 400        | 200         |
      | 2               | 400        | 400         |
