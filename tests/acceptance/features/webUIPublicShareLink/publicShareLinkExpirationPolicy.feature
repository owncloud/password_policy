@webUI @insulated @disablePreviews
Feature: enforce public link expiration policies

  As an administrator
  I want to enforce expiration date for public shares
  So public shares have default expiration if the expiration date is not changed

  Background:
	Given user "user1" has been created with default attributes and skeleton files
	And user "user1" has logged in using the webUI

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
