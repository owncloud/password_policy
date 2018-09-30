@webUI
Feature: enforce the required number of uppercase letters in a password on the public share link page

  As an administrator
  I want public share link passwords to always contain a required number of uppercase letters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given these users have been created:
      | username | password | displayname | email        |
      | user1    | abc123   | User One    | u1@oc.com.np |
    And the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And the user has browsed to the login page
    And the user has logged in with username "user1" and password "abc123" using the webUI

  Scenario Outline: user creates a public share link with enough uppercase letters
    When the user creates a new public link for the folder "simple-folder" using the webUI with
      | password | <password> |
    And the public accesses the last created public link with password "<password>" using the webUI
    Then the file "lorem.txt" should be listed on the webUI
    Examples:
      | password                  |
      | 3UpperCaseLetters         |
      | MoreThan3UpperCaseLetters |

  Scenario Outline: user tries to create a public share link with too few uppercase letters
    When the user tries to create a new public link for the folder "simple-folder" using the webUI with
      | password | <password> |
    Then the user should see a error message on public dialog saying "The password contains too few uppercase letters. At least 3 uppercase letters are required."
    And public link should not be generated
    Examples:
      | password       |
      | 0uppercase     |
      | Only2Uppercase |
