@webUI
Feature: enforce password_policy on publicly shared links

	As an admin
	I want to enforce password_policy on publicly shared links
	So that publicly shared links are protected from un-authorized access

	Background:
		Given these users have been created:
			|username|password|displayname|email       |
			|user1   |ABC123  |User One   |u1@oc.com.np|
		And the administrator has enabled the uppercase letters password policy
		And the administrator has set the uppercase letters required to "3"
		And the user has browsed to the login page
		And the user has logged in with username "user1" and password "ABC123" using the webUI

	Scenario: user tries to share a folder with invalid password
		When the user tries to create a new public link for the folder "simple-folder" using the webUI with
			| password | PAs123 |
		Then the user should see a error message on public dialog saying "The password contains too few uppercase letters. At least 3 uppercase letters are required."
		And public link should not be generated

	Scenario: user shares a folder with password
		When the user creates a new public link for the folder "simple-folder" using the webUI with
			| password | PAS123 |
		And the public accesses the last created public link with password "PAS123" using the webUI
		Then the file "lorem.txt" should be listed on the webUI
