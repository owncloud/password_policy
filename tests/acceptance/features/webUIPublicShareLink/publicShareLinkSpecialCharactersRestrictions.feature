@webUI
Feature: enforce the restricted special characters in a password while creating public share link

  As an administrator
  I want user passwords to always contain some of a restricted list of special characters
  So that users cannot set passwords that have unusual hard-to-type characters

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | a$b%c^1234 | User One    | u1@oc.com.np |
    And the user has browsed to the login page
    And the user has logged in with username "user1" and password "a$b%c^1234" using the webUI

  Scenario Outline: user creates a public share link with enough restricted special characters
    When the user creates a new public link for the folder "simple-folder" using the webUI with
      | password | <password> |
    And the public accesses the last created public link with password "<password>" using the webUI
    Then the file "lorem.txt" should be listed on the webUI
    Examples:
      | password              |
      | 3$Special%Characters^ |
      | 1*2&3^4%5$6           |

  Scenario Outline: user tries to create a link with too few restricted special characters
    When the user tries to create a new public link for the folder "simple-folder" using the webUI with
      | password | <password> |
    Then the user should see a error message on public dialog saying "The password contains too few special characters. At least 3 special characters ($%^&*) are required."
    And public link should not be generated
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special&Characters |

  Scenario Outline: user tries to create a public share link with invalid special characters
    When the user tries to create a new public link for the folder "simple-folder" using the webUI with
      | password | <password> |
    Then the user should see a error message on public dialog saying "The password contains invalid special characters. Only $%^&* are allowed."
    And public link should not be generated
    Examples:
      | password                                 |
      | Only#Invalid!Special@Characters          |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! |
