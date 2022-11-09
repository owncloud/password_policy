@webUI @insulated @disablePreviews
Feature: set password policy settings

  As an administrator
  I want to be able to set password requirements (minimum length, lowercase/uppercase/numeric/special characters)
  So that user and public link passwords have a minimum level of complexity

  Background:
    Given the administrator has browsed to the admin security settings page


  Scenario: set minimum length of password
    When the administrator enables the minimum characters password policy using the webUI
    And the administrator sets the minimum characters required to "5" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the minimum characters password policy checkbox should be checked on the webUI
    And the required minimum characters should be set to "5" on the webUI
    And the required minimum characters should be set to "5"
    And the minimum characters password policy should be enabled


  Scenario: set lowercase letters required in password
    When the administrator enables the lowercase letters password policy using the webUI
    And the administrator sets the lowercase letters required to "2" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the lowercase letters password policy checkbox should be checked on the webUI
    And the required number of lowercase letters should be set to "2" on the webUI
    And the required number of lowercase letters should be set to "2"
    And the lowercase letters password policy should be enabled


  Scenario: set uppercase letters required in password
    When the administrator enables the uppercase letters password policy using the webUI
    And the administrator sets the uppercase letters required to "2" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the uppercase letters password policy checkbox should be checked on the webUI
    And the required number of uppercase letters should be set to "2" on the webUI
    And the required number of uppercase letters should be set to "2"
    And the uppercase letters password policy should be enabled


  Scenario: set numbers required in password
    When the administrator enables the numbers password policy using the webUI
    And the administrator sets the numbers required to "2" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the numbers password policy checkbox should be checked on the webUI
    And the required number of numbers should be set to "2" on the webUI
    And the required number of numbers should be set to "2"
    And the numbers password policy should be enabled


  Scenario: set special characters required in password
    When the administrator enables the special characters password policy using the webUI
    And the administrator sets the special characters required to "2" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the special characters password policy checkbox should be checked on the webUI
    And the required number of special characters should be set to "2" on the webUI
    And the required number of special characters should be set to "2"
    And the special characters password policy should be enabled


  Scenario: set "restrict to these special characters" list of characters
    When the administrator enables the restrict to these special characters password policy using the webUI
    And the administrator sets the restricted list of special characters to "!@#$%^&*" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the restrict to these special characters password policy checkbox should be checked on the webUI
    And restrict to these special characters should be set to "!@#$%^&*" on the webUI
    And restrict to these special characters should be set to "!@#$%^&*"
    And the restrict to these special characters password policy should be enabled


  Scenario: set "restrict to these special characters" empty receives user notification and the change is not saved
    When the administrator enables the restrict to these special characters password policy using the webUI
    And the administrator sets the restricted list of special characters to "" using the webUI
    And the administrator saves the password policy settings using the webUI
    Then notifications should be displayed on the webUI with the text
      | Error: The special characters cannot be empty. |
    And the administrator reloads the admin security settings page
    And restrict to these special characters should be set to "#!" on the webUI


  Scenario: set number of last passwords that should not be used
    When the administrator enables the last passwords user password policy using the webUI
    And the administrator sets the number of last passwords that should not be used to "12" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the last passwords user password policy checkbox should be checked on the webUI
    And last passwords that should not be used should be set to "12" on the webUI
    And last passwords that should not be used should be set to "12"
    And the last passwords user password policy should be enabled


  Scenario: set "days until user password expires"
    When the administrator enables the days until user password expires user password policy using the webUI
    And the administrator sets the number of days until user password expires to "42" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the days until user password expires user password policy checkbox should be checked on the webUI
    And the number of days until user password expires should be set to "42" on the webUI
    And the number of days until user password expires should be set to "42"
    And the days until user password expires user password policy should be enabled


  Scenario: set "notification days before password expires"
    When the administrator enables the notification days before password expires user password policy using the webUI
    And the administrator sets the notification days before password expires to "14" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the notification days before password expires user password policy checkbox should be checked on the webUI
    And the notification days before password expires should be set to "14" on the webUI
    # This settting is stored in seconds. 14 days = 14*24*60*60 = 1209600 seconds
    And the notification seconds before password expires should be set to "1209600"
    And the notification days before password expires user password policy should be enabled


  Scenario: set "days until link expires if password is set" for public links
    When the administrator enables the days until link expires if password is set public link password policy using the webUI
    And the administrator sets the number of days until link expires if password is set to "35" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the days until link expires if password is set public link password policy checkbox should be checked on the webUI
    And the number of days until link expires if password is set should be set to "35" on the webUI
    And the number of days until link expires if password is set should be set to "35"
    And the days until link expires if password is set public link password policy should be enabled


  Scenario: set "days until link expires if password is not set" for public links
    When the administrator enables the days until link expires if password is not set public link password policy using the webUI
    And the administrator sets the number of days until link expires if password is not set to "19" using the webUI
    And the administrator saves the password policy settings using the webUI
    And the administrator reloads the admin security settings page
    Then the days until link expires if password is not set public link password policy checkbox should be checked on the webUI
    And the number of days until link expires if password is not set should be set to "19" on the webUI
    And the number of days until link expires if password is not set should be set to "19"
    And the days until link expires if password is not set public link password policy should be enabled
