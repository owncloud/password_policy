@webUI @insulated @disablePreviews
Feature: enable password policy settings

  As an administrator
  I want to be able to enable password requirements (minimum length, lowercase/uppercase/numeric/special characters)
  So that user and public link passwords have a minimum level of complexity

  Background:
    Given the administrator has browsed to the admin security settings page


  Scenario: password policy checkboxes are unchecked when no settings have been made
    Then the minimum characters password policy checkbox should be unchecked on the webUI
    And the minimum characters password policy should be disabled
    And the lowercase letters password policy checkbox should be unchecked on the webUI
    And the lowercase letters password policy should be disabled
    And the uppercase letters password policy checkbox should be unchecked on the webUI
    And the uppercase letters password policy should be disabled
    And the numbers password policy checkbox should be unchecked on the webUI
    And the numbers password policy should be disabled
    And the special characters password policy checkbox should be unchecked on the webUI
    And the special characters password policy should be disabled
    And the restrict to these special characters password policy checkbox should be unchecked on the webUI
    And the restrict to these special characters password policy should be disabled
    And the last passwords user password policy checkbox should be unchecked on the webUI
    And the last passwords user password policy should be disabled
    And the days until user password expires user password policy checkbox should be unchecked on the webUI
    And the days until user password expires user password policy should be disabled
    And the notification days before password expires user password policy checkbox should be unchecked on the webUI
    And the notification days before password expires user password policy should be disabled
    And the force password change on first login user password policy checkbox should be unchecked on the webUI
    And the force password change on first login user password policy should be disabled
    And the days until link expires if password is set public link password policy checkbox should be unchecked on the webUI
    And the days until link expires if password is set public link password policy should be disabled
    And the days until link expires if password is not set public link password policy checkbox should be unchecked on the webUI
    And the days until link expires if password is not set public link password policy should be disabled


  Scenario: enable minimum length of password policy
    When the administrator enables the minimum characters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the minimum characters password policy checkbox should be checked on the webUI
    And the minimum characters password policy should be enabled


  Scenario: enable lowercase letters password policy
    When the administrator enables the lowercase letters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the lowercase letters password policy checkbox should be checked on the webUI
    And the lowercase letters password policy should be enabled


  Scenario: enable uppercase letters password policy
    When the administrator enables the uppercase letters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the uppercase letters password policy checkbox should be checked on the webUI
    And the uppercase letters password policy should be enabled


  Scenario: enable numbers password policy
    When the administrator enables the numbers password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the numbers password policy checkbox should be checked on the webUI
    And the numbers password policy should be enabled


  Scenario: enable special characters password policy
    When the administrator enables the special characters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the special characters password policy checkbox should be checked on the webUI
    And the special characters password policy should be enabled


  Scenario: enable "restrict to these special characters" password policy
    When the administrator enables the restrict to these special characters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the restrict to these special characters password policy checkbox should be checked on the webUI
    And the restrict to these special characters password policy should be enabled


  Scenario: enable "last passwords" user password policy
    When the administrator enables the last passwords user password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the last passwords user password policy checkbox should be checked on the webUI
    And the last passwords user password policy should be enabled


  Scenario: enable "days until user password expires" user password policy
    When the administrator enables the days until user password expires user password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the days until user password expires user password policy checkbox should be checked on the webUI
    And the days until user password expires user password policy should be enabled


  Scenario: enable "notification days before password expires" user password policy
    When the administrator enables the notification days before password expires user password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the notification days before password expires user password policy checkbox should be checked on the webUI
    And the notification days before password expires user password policy should be enabled


  Scenario: enable "force password change on first login" user password policy
    When the administrator enables the force password change on first login user password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the force password change on first login user password policy checkbox should be checked on the webUI
    And the force password change on first login user password policy should be enabled


  Scenario: enable "days until link expires if password is set" public link password policy
    When the administrator enables the days until link expires if password is set public link password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the days until link expires if password is set public link password policy checkbox should be checked on the webUI
    And the days until link expires if password is set public link password policy should be enabled


  Scenario: enable "days until link expires if password is not set" public link password policy
    When the administrator enables the days until link expires if password is not set public link password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the days until link expires if password is not set public link password policy checkbox should be checked on the webUI
    And the days until link expires if password is not set public link password policy should be enabled
