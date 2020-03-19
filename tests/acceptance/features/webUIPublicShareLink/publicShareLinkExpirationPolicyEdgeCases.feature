@webUI @insulated @disablePreviews
Feature: enforce public link expiration policies on public links only

  As an administrator
  I want to enforce maximum expiration date for public shares
  I do not want the settings to affect user or group shares

  Background:
    Given these users have been created with default attributes and without skeleton files:
      | username |
      | user1    |
      | user2    |
    And user "user1" has created folder "folder-to-share"
    And user "user1" has logged in using the webUI

  @issue-287 @skipOnOcV10.3 @skipOnOcV10.4.0
  Scenario: user tries to create a user share when "days maximum until link expires if password is not set" is enabled
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    When the user shares folder "folder-to-share" with user "User Two" using the webUI
    Then as "user2" folder "folder-to-share" should exist

  Scenario: user tries to create a user share when "days maximum until link expires if password is set" is enabled
    Given the administrator has enabled the days until link expires if password is set public link password policy
    When the user shares folder "folder-to-share" with user "User Two" using the webUI
    And as "user2" folder "folder-to-share" should exist

  @issue-287 @skipOnOcV10.3 @skipOnOcV10.4.0
  Scenario: user tries to create a group share when "days maximum until link expires if password is not set" is enabled
    Given group "new-group" has been created
    And user "user2" has been added to group "new-group"
    And the administrator has enabled the days until link expires if password is not set public link password policy
    When the user shares folder "folder-to-share" with group "new-group" using the webUI
    Then as "user2" folder "folder-to-share" should exist

  Scenario: user tries to create a group share when "days maximum until link expires if password is set" is enabled
    Given group "new-group" has been created
    And user "user2" has been added to group "new-group"
    And the administrator has enabled the days until link expires if password is set public link password policy
    When the user shares folder "folder-to-share" with group "new-group" using the webUI
    And as "user2" folder "folder-to-share" should exist
