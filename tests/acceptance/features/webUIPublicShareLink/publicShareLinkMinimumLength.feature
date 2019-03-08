@webUI @insulated @disablePreviews
Feature: enforce the minimum length of a password on the public share link page

  As an administrator
  I want public share link passwords to always be a certain minimum length
  So that users cannot set passwords that are too short (easy to crack)

  Background:
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And these users have been created with default attributes:
      | username | password   |
      | user1    | 1234567890 |
    And the user has browsed to the login page
    And the user has logged in with username "user1" and password "1234567890" using the webUI

  Scenario Outline: user creates a public share link with enough letters
    When the user creates a new public link for folder "simple-folder" using the webUI with
      | password | <password> |
    And the public accesses the last created public link with password "<password>" using the webUI
    Then file "lorem.txt" should be listed on the webUI
    Examples:
      | password                  |
      | 10tenchars                |
      | moreThan3LowercaseLetters |

  Scenario Outline: user tries to create a public share link with too few letters
    When the user tries to create a new public link for folder "simple-folder" using the webUI with
      | password | <password> |
    Then the user should see an error message on the public link share dialog saying "The password is too short. At least 10 characters are required."
    And the public link should not have been generated
    Examples:
      | password  |
      | A         |
      | 123456789 |
