@webUI
Feature: enforce combinations of password policies on public link share

	As an administrator
	I want public share link to always have some combination of minimum length, lowercase, uppercase, numbers and special characters
	So that users cannot set passwords that are too easy to guess

	Background:
		Given the administrator has enabled the minimum characters password policy
		And the administrator has set the minimum characters required to "15"
		And the administrator has enabled the lowercase letters password policy
		And the administrator has set the lowercase letters required to "4"
		And the administrator has enabled the uppercase letters password policy
		And the administrator has set the uppercase letters required to "3"
		And the administrator has enabled the numbers password policy
		And the administrator has set the numbers required to "2"
		And the administrator has enabled the special characters password policy
		And the administrator has set the special characters required to "3"
		And these users have been created:
			| username | password        | displayname | email        |
			| user1    | aA1!bB2#cC&deee | User One    | u1@oc.com.np |
		And the user has browsed to the login page
		And the user has logged in with username "user1" and password "aA1!bB2#cC&deee" using the webUI

	Scenario Outline: user creates a public share link with valid password
		When the user creates a new public link for the folder "simple-folder" using the webUI with
			| password | <password> |
		And the public accesses the last created public link with password "<password>" using the webUI
		Then the file "lorem.txt" should be listed on the webUI
		Examples:
			| password                  |
			| 15***UPPloweZZZ           |
			| More%Than$15!Characters-0 |

	Scenario Outline: user tries to create a public share link with invalid password
		When the user tries to create a new public link for the folder "simple-folder" using the webUI with
			| password | <password> |
		Then the user should see a error message on public dialog saying "<message>"
		And public link should not be generated
		Examples:
			| password                       | message                                                                                       |
				# where just one of the requirements is not met
			| aA1!bB2#cC&d                   | The password is too short. At least 15 characters are required.                               |
			| aA1!bB2#cNOT&ENOUGH#LOWERCASE  | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |
			| aA1!bB2#cnot&enough#uppercase  | The password contains too few uppercase letters. At least 3 uppercase letters are required.   |
			| Not&Enough#Numbers=1           | The password contains too few numbers. At least 2 numbers are required.                       |
			| Not&Enough#Special8Characters2 | The password contains too few special characters. At least 3 special characters are required. |
				# where multiple requirements are not met, only the first error message is shown to the user
			| aA!1                           | The password is too short. At least 15 characters are required.                               |
			| aA!123456789012345             | The password contains too few lowercase letters. At least 4 lowercase letters are required.   |

	Scenario Outline: user creates a public share link using valid restricted special characters
		Given the administrator has enabled the restrict to these special characters password policy
		And the administrator has set the restricted special characters required to "$%^&*"
		When the user creates a new public link for the folder "simple-folder" using the webUI with
			| password | <password> |
		And the public accesses the last created public link with password "<password>" using the webUI
		Then the file "lorem.txt" should be listed on the webUI
		Examples:
			| password                  |
			| 15%&*UPPloweZZZ           |
			| More^Than$15&Characters*0 |

	Scenario Outline: user tries to create a public link using invalid restricted special characters
		Given the administrator has enabled the restrict to these special characters password policy
		And the administrator has set the restricted special characters required to "$%^&*"
		When the user tries to create a new public link for the folder "simple-folder" using the webUI with
			| password | <password> |
		Then the user should see a error message on public dialog saying "<message>"
		And public link should not be generated
		Examples:
			| password        | message                                                                                     |
			| 15#!!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
			| 15&%!UPPloweZZZ | The password contains invalid special characters. Only $%^&* are allowed.                   |
				# where multiple requirements are not met, only the first error message is shown to the user
			| 15&%!UPPlowZZZZ | The password contains too few lowercase letters. At least 4 lowercase letters are required. |
