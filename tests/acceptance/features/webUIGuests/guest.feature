@webUI @insulated @disablePreviews @mailhog
Feature:

  Background:
    Given user "Alice" has been created with default attributes and large skeleton files
    And the administrator has created guest user "guest" with email "guest@example.com"
    And user "Alice" has shared folder "/simple-folder" with user "guest@example.com"


  Scenario: A guest user does not need to set password twice if force password change on first login is enabled
    Given the administrator has enabled the force password change on first login user password policy
    When guest user "guest" registers and sets password to "<password>" using the webUI
    And user "guest@example.com" logs in using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
    And folder "simple-folder" should be listed on the webUI
