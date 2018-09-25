@webUI
Feature: enforce the minimum length of a password on public link share

	As an administrator
	I want user passwords to always be a certain minimum length
	So that users cannot set passwords that are too short (easy to crack)

	Background:
		Given the administrator has enabled the minimum characters password policy
		And the administrator has set the minimum characters required to "10"
		And these users have been created:
			| username | password   | displayname | email        |
			| user1    | 1234567890 | User One    | u1@oc.com.np |
		And the user has browsed to the login page
		And the user has logged in with username "user1" and password "1234567890" using the webUI

	Scenario Outline:  user creates a public share link with enough letters
		When the user creates a new public link for the folder "simple-folder" using the webUI with
			| password | <password> |
		And the public accesses the last created public link with password "<password>" using the webUI
		Then the file "lorem.txt" should be listed on the webUI
		Examples:
			| password                  |
			| 10tenchars                |
			| moreThan3LowercaseLetters |

	Scenario Outline: user tries to create a public share link with too few letters
		When the user tries to create a new public link for the folder "simple-folder" using the webUI with
			| password | <password> |
		Then the user should see a error message on public dialog saying "The password is too short. At least 10 characters are required."
		And public link should not be generated
		Examples:
			| password  |
			| A         |
			| 123456789 |