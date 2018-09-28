@webUI
Feature: enforce the required number of special characters in a public share link

  As an administrator
  I want public share link password to always contain a required number of special characters
  So that users cannot set passwords that are too easy to guess

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And these users have been created:
      | username | password   | displayname | email        |
      | user1    | a!b@c#1234 | User One    | u1@oc.com.np |
    And the user has browsed to the login page
    And the user has logged in with username "user1" and password "a!b@c#1234" using the webUI

  Scenario Outline: user creates a public share link with enough special characters
    When the user creates a new public link for the folder "simple-folder" using the webUI with
      | password | <password> |
    And the public accesses the last created public link with password "<password>" using the webUI
    Then the file "lorem.txt" should be listed on the webUI
    Examples:
      | password              |
      | 3#Special$Characters! |
      | 1!2@3#4$5%6^7&8*      |

  Scenario Outline: user tries to create a public share link with too few special characters
    When the user tries to create a new public link for the folder "simple-folder" using the webUI with
      | password | <password> |
    Then the user should see a error message on public dialog saying "The password contains too few special characters. At least 3 special characters are required."
    And public link should not be generated
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special!Characters |
