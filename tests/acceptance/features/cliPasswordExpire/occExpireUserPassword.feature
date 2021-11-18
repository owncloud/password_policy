@cli
Feature: expire user's password using the occ command

  As an administrator
  I want to expire user's password
  So that users change their password regularly

  Background:
    Given user "Alice" has been created with default attributes and small skeleton files


  Scenario: admin tries to expire user password without setting expiration rule
    When the administrator expires the password of user "Alice" using the occ command
    Then the command should have failed with exit code 1
    And the command output should contain the text 'Cannot use this command because no expiration rule was configured'


  Scenario: admin expires user password after setting expiration rule
    Given the administrator has enabled the days until user password expires user password policy
    When the administrator expires the password of user "Alice" using the occ command
    Then the command should have been successful
    And the command output should contain the text 'The password for Alice is set to expire'


  Scenario: admin expires password of a user and then the user downloads a file
    Given the administrator has enabled the days until user password expires user password policy
    When the administrator expires the password of user "Alice" using the occ command
    Then user "Alice" downloading the file "textfile0.txt" and using the password "%regular%" should receive hint to change their password


  Scenario: admin expires password of a user and then the user deletes a file
    Given the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    When user "Alice" deletes file "/textfile0.txt" using the WebDAV API
    Then the HTTP status code should be "503"


  Scenario: admin expires password of a user and then the user tries to share a file
    Given user "Brian" has been created with default attributes and without skeleton files
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    When user "Alice" shares file "/textfile0.txt" with user "Brian" using the sharing API
    Then the HTTP status code should be "204"
    But as "Brian" file "/textfile0.txt" should not exist
