@webUI @insulated @disablePreviews
Feature: enforce public link expiration policies on public links only

  As an administrator
  I want to enforce maximum expiration date for public shares
  I do not want the settings to affect user or group shares

  Background:
    Given these users have been created with default attributes and without skeleton files:
      | username |
      | Alice    |
      | Brian    |
    And user "Alice" has created folder "folder-to-share"
    And user "Alice" has logged in using the webUI

  @issue-287 @skipOnOcV10.3 @skipOnOcV10.4.0
  Scenario: user tries to create a user share when "days maximum until link expires if password is not set" is enabled
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    When the user shares folder "folder-to-share" with user "Brian Murphy" using the webUI
    Then as "Brian" folder "folder-to-share" should exist

  Scenario: user tries to create a user share when "days maximum until link expires if password is set" is enabled
    Given the administrator has enabled the days until link expires if password is set public link password policy
    When the user shares folder "folder-to-share" with user "Brian Murphy" using the webUI
    And as "Brian" folder "folder-to-share" should exist

  @issue-287 @skipOnOcV10.3 @skipOnOcV10.4.0
  Scenario: user tries to create a group share when "days maximum until link expires if password is not set" is enabled
    Given group "new-group" has been created
    And user "Brian" has been added to group "new-group"
    And the administrator has enabled the days until link expires if password is not set public link password policy
    When the user shares folder "folder-to-share" with group "new-group" using the webUI
    Then as "Brian" folder "folder-to-share" should exist

  Scenario: user tries to create a group share when "days maximum until link expires if password is set" is enabled
    Given group "new-group" has been created
    And user "Brian" has been added to group "new-group"
    And the administrator has enabled the days until link expires if password is set public link password policy
    When the user shares folder "folder-to-share" with group "new-group" using the webUI
    And as "Brian" folder "folder-to-share" should exist
