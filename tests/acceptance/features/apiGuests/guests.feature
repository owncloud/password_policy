@api
Feature: Guests

  Background:
    Given user "user0" has been created with default attributes
    And the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "3"

  @mailhog
  Scenario Outline: A guest user sets own password to a string that has enough lowercase letters
    Given using OCS API version "<ocs-api-version>"
    And the administrator has created guest user "guest" with email "guest@example.com"
    And user "user0" has shared file "/textfile1.txt" with user "guest@example.com"
    When guest user "guest" registers and sets password to "<password>"
    Then the HTTP status code should be "200"
    And user "guest" should be a guest user
    And user "guest@example.com" should see the following elements
      | /textfile1.txt |
    Examples:
      | password                  | ocs-api-version |
      | 3LCase                    | 1               |
      | 3LCase                    | 2               |
      | moreThan3LowercaseLetters | 1               |
      | moreThan3LowercaseLetters | 2               |

  @mailhog
  Scenario Outline: A guest user sets own password to a string that dos not have enough lowercase letters
    Given using OCS API version "<ocs-api-version>"
    And the administrator has created guest user "guest" with email "guest@example.com"
    And user "user0" has shared file "/textfile1.txt" with user "guest@example.com"
    When guest user "guest" registers and sets password to "<password>"
    Then the HTTP status code should be "200"
    And user "guest@example.com" should not see the following elements
      | /textfile1.txt |
    Examples:
      | password   | ocs-api-version |
      | 0LOWERCASE | 1               |
      | 0LOWERCASE | 2               |
      | 2lOWERcASE | 1               |
      | 2lOWERcASE | 2               |