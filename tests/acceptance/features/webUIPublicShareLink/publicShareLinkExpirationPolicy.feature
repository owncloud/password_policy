@webUI @insulated @disablePreviews
Feature: enforce public link expiration policies

  As an administrator
  I want to enforce expiration date for public shares
  So public shares have default expiration if the expiration date is not changed

  Background:
    Given user "Alice" has been created with default attributes and skeleton files
    And user "Alice" has logged in using the webUI

  Scenario: user tries to create a public share without expiration date and password when "days maximum until link expires if password is not set" is enabled
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    When the user tries to create a new public link for folder "simple-folder" using the webUI
    Then the user should see an error message on the public link share dialog saying "An expiration date is required."
    And the public link should not have been generated

  Scenario: user creates a public share without expiration date when "days maximum until link expires if password is not set" is enabled
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    When the user creates a new public link for folder "simple-folder" using the webUI with
      | password | abcdefgh |
    And the public accesses the last created public link with password "abcdefgh" using the webUI
    Then file "lorem.txt" should be listed on the webUI

  Scenario: user creates a public share without expiration date and password when "days maximum until link expires if password is set" is enabled
    Given the administrator has enabled the days until link expires if password is set public link password policy
    When the user creates a new public link for folder "simple-folder" using the webUI
    And the public accesses the last created public link using the webUI
    Then file "lorem.txt" should be listed on the webUI

  Scenario: user tries to create a public share without expiration date when "days maximum until link expires if password is set" is enabled
    Given the administrator has enabled the days until link expires if password is set public link password policy
    When the user tries to create a new public link for folder "simple-folder" using the webUI with
      | password | abcdefgh |
    Then the user should see an error message on the public link share dialog saying "An expiration date is required."
    And the public link should not have been generated

  Scenario: user tries to create a public share without password but with expiration date exceeding the default maximum days when "days maximum until link expires if password is not set" is enabled
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    When the user tries to create a new public link for folder "simple-folder" using the webUI with
      | expiration | +10 days |
    Then the user should see an error message on the public link share dialog saying "The expiration date cannot exceed 7 days."
    And the public link should not have been generated

  Scenario: user creates a public share with password but with expiration date exceeding the default maximum days when "days maximum until link expires if password is not set" is enabled
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    When the user creates a new public link for folder "simple-folder" using the webUI with
      | expiration | +10 days |
      | password   | abcdefgh |
    And the public accesses the last created public link with password "abcdefgh" using the webUI
    Then file "lorem.txt" should be listed on the webUI

  Scenario: user creates a public share without password but expiration date exceeding the default maximum days when "days maximum until link expires if password is set" is enabled
    Given the administrator has enabled the days until link expires if password is set public link password policy
    When the user creates a new public link for folder "simple-folder" using the webUI
      | expiration | +10 days |
    And the public accesses the last created public link using the webUI
    Then file "lorem.txt" should be listed on the webUI

  Scenario: user tries to create a public share with password but expiration date exceeding the default maximum days when "days maximum until link expires if password is set" is enabled
    Given the administrator has enabled the days until link expires if password is set public link password policy
    When the user tries to create a new public link for folder "simple-folder" using the webUI with
      | expiration | +10 days |
      | password   | abcdefgh |
    Then the user should see an error message on the public link share dialog saying "The expiration date cannot exceed 7 days."
    And the public link should not have been generated

  Scenario: user creates a public share with expiration date when "days maximum until link expires if password is not set" is enabled and the default maximum days is changed
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    And the administrator has set the value for the maximum days until link expires if password is not set to "10"
    When the user creates a new public link for folder "simple-folder" using the webUI with
      | expiration | +10 days |
    And the public accesses the last created public link using the webUI
    Then file "lorem.txt" should be listed on the webUI

  Scenario: user creates a public share with password and expiration date when "days maximum until link expires if password is set" is enabled and the default maximum days is changed
    Given the administrator has enabled the days until link expires if password is set public link password policy
    And the administrator has set the value for the maximum days until link expires if password is set to "10"
    When the user creates a new public link for folder "simple-folder" using the webUI with
      | expiration | +10 days |
      | password   | abcdefgh |
    And the public accesses the last created public link with password "abcdefgh" using the webUI
    Then file "lorem.txt" should be listed on the webUI

  Scenario: user tries to create a public share with expiration date exceeding the default value when "days maximum until link expires if password is not set" is enabled and the default maximum days is changed
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    And the administrator has set the value for the maximum days until link expires if password is not set to "2"
    When the user tries to create a new public link for folder "simple-folder" using the webUI with
      | expiration | +10 days |
    Then the user should see an error message on the public link share dialog saying "The expiration date cannot exceed 2 days."
    And the public link should not have been generated

  Scenario: user tries to create a public share with password, and expiration date exceeding the default value when "days maximum until link expires if password is set" is enabled and the default maximum days is changed
    Given the administrator has enabled the days until link expires if password is set public link password policy
    And the administrator has set the value for the maximum days until link expires if password is set to "2"
    When the user tries to create a new public link for folder "simple-folder" using the webUI with
      | expiration | +10 days |
      | password   | abcdefgh |
    Then the user should see an error message on the public link share dialog saying "The expiration date cannot exceed 2 days."
    And the public link should not have been generated

  Scenario: user tries to create a public share with expiration date exceeding the default value when "days maximum until link expires if password is not set" is disabled but the default maximum days is changed
    Given the administrator has set the value for the maximum days until link expires if password is not set to "10"
    When the user creates a new public link for folder "simple-folder" using the webUI with
      | expiration | +20 days |
    And the public accesses the last created public link using the webUI
    Then file "lorem.txt" should be listed on the webUI

  Scenario: user tries to create a public share with password, and expiration date exceeding the default value when "days maximum until link expires if password is not set" is disabled but the default maximum days is changed
    Given the administrator has set the value for the maximum days until link expires if password is set to "10"
    When the user creates a new public link for folder "simple-folder" using the webUI with
      | expiration | +20 days |
      | password   | abcdefgh |
    And the public accesses the last created public link with password "abcdefgh" using the webUI
    Then file "lorem.txt" should be listed on the webUI

  Scenario: user decreases the default maximum days until link expires if password is set and then edits expiration date of the already created public link
    Given the administrator has enabled the days until link expires if password is set public link password policy
    And the user has created a new public link for folder "simple-folder" using the webUI with
      | expiration | +6 days  |
      | password   | abcdefgh |
    And the administrator has set the value for the maximum days until link expires if password is set to "3"
    And the user has reloaded the current page of the webUI
    When the user changes the expiration of the public link "Public link" of folder "simple-folder" to "+5 days"
    Then the user should see an error message on the public link share dialog saying "The expiration date cannot exceed 3 days."

  Scenario: user decreases the default maximum days until link expires if password is not set and then edits expiration date of the already created public link
    Given the administrator has enabled the days until link expires if password is not set public link password policy
    And the user has created a new public link for folder "simple-folder" using the webUI with
      | expiration | +6 days |
    And the administrator has set the value for the maximum days until link expires if password is not set to "3"
    And the user has reloaded the current page of the webUI
    When the user changes the expiration of the public link "Public link" of folder "simple-folder" to "+5 days"
    Then the user should see an error message on the public link share dialog saying "The expiration date cannot exceed 3 days."
