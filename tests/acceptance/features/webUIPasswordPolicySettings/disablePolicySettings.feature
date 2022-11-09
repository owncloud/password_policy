@webUI @insulated @disablePreviews
Feature: disable password policy settings

  As an administrator
  I want to be able to disable password requirements (minimum length, lowercase/uppercase/numeric/special characters)
  So that the password policy can be customized to the needs of the installation


  Scenario: password policy checkboxes are checked when password policies have been enabled
    Given the administrator has enabled the minimum characters password policy
    And the administrator has enabled the lowercase letters password policy
    And the administrator has enabled the uppercase letters password policy
    And the administrator has enabled the numbers password policy
    And the administrator has enabled the special characters password policy
    And the administrator has enabled the restrict to these special characters password policy
    And the administrator has enabled the last passwords user password policy
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has enabled the notification days before password expires user password policy
    And the administrator has enabled the force password change on first login user password policy
    And the administrator has enabled the days until link expires if password is set public link password policy
    And the administrator has enabled the days until link expires if password is not set public link password policy
    And the administrator has browsed to the admin security settings page
    Then the minimum characters password policy checkbox should be checked on the webUI
    And the lowercase letters password policy checkbox should be checked on the webUI
    And the uppercase letters password policy checkbox should be checked on the webUI
    And the numbers password policy checkbox should be checked on the webUI
    And the special characters password policy checkbox should be checked on the webUI
    And the restrict to these special characters password policy checkbox should be checked on the webUI
    And the last passwords user password policy checkbox should be checked on the webUI
    And the days until user password expires user password policy checkbox should be checked on the webUI
    And the notification days before password expires user password policy checkbox should be checked on the webUI
    And the force password change on first login user password policy checkbox should be checked on the webUI
    And the days until link expires if password is set public link password policy checkbox should be checked on the webUI
    And the days until link expires if password is not set public link password policy checkbox should be checked on the webUI


  Scenario: disable minimum length of password policy
    Given the administrator has enabled the minimum characters password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the minimum characters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the minimum characters password policy checkbox should be unchecked on the webUI
    And the minimum characters password policy should be disabled


  Scenario: disable lowercase letters password policy
    Given the administrator has enabled the lowercase letters password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the lowercase letters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the lowercase letters password policy checkbox should be unchecked on the webUI
    And the lowercase letters password policy should be disabled


  Scenario: disable uppercase letters password policy
    Given the administrator has enabled the uppercase letters password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the uppercase letters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the uppercase letters password policy checkbox should be unchecked on the webUI
    And the uppercase letters password policy should be disabled


  Scenario: disable numbers password policy
    Given the administrator has enabled the numbers password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the numbers password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the numbers password policy checkbox should be unchecked on the webUI
    And the numbers password policy should be disabled


  Scenario: disable special characters password policy
    Given the administrator has enabled the special characters password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the special characters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the special characters password policy checkbox should be unchecked on the webUI
    And the special characters password policy should be disabled


  Scenario: disable "restrict to these special characters" password policy
    Given the administrator has enabled the restrict to these special characters password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the restrict to these special characters password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the restrict to these special characters password policy checkbox should be unchecked on the webUI
    And the restrict to these special characters password policy should be disabled


  Scenario: disable "last passwords" user password policy
    Given the administrator has enabled the last passwords user password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the last passwords user password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the last passwords user password policy checkbox should be unchecked on the webUI
    And the last passwords user password policy should be disabled


  Scenario: disable "days until user password expires" user password policy
    Given the administrator has enabled the days until user password expires user password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the days until user password expires user password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the days until user password expires user password policy checkbox should be unchecked on the webUI
    And the days until user password expires user password policy should be disabled


  Scenario: disable "notification days before password expires" user password policy
    Given the administrator has enabled the notification days before password expires user password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the notification days before password expires user password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the notification days before password expires user password policy checkbox should be unchecked on the webUI
    And the notification days before password expires user password policy should be disabled


  Scenario: disable "force password change on first login" user password policy
    Given the administrator has enabled the force password change on first login user password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the force password change on first login user password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the force password change on first login user password policy checkbox should be unchecked on the webUI
    And the force password change on first login user password policy should be disabled


  Scenario: disable "days until link expires if password is set" public link password policy
    Given the administrator has enabled the days until link expires if password is set public link password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the days until link expires if password is set public link password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the days until link expires if password is set public link password policy checkbox should be unchecked on the webUI
    And the days until link expires if password is set public link password policy should be disabled


  Scenario: disable "days until link expires if password is not set" public link password policy
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    And the administrator has browsed to the admin security settings page
    When the administrator disables the days until link expires if password is not set public link password policy using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the days until link expires if password is not set public link password policy checkbox should be unchecked on the webUI
    And the days until link expires if password is not set public link password policy should be disabled
