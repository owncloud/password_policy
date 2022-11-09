@webUI @insulated @disablePreviews @mailhog
Feature: expire guest user's password using the occ command

  As an administrator
  I want to expire guest user's password
  So that guest users change their password


  Scenario: admin expires password of a guest user
    Given the administrator has created guest user "guest" with email "guest@example.com"
    And the administrator has enabled the days until user password expires user password policy
    When the administrator expires the password of user "guest@example.com" using the occ command
    Then the command should have been successful
    # Guest user does not get any emails when the command is invoked
    And the email address "guest@example.com" should not have received an email
    # The above step can be removed and the below can be implemented when the guest user gets a password reset email
    # When the user follows the password reset link received by "guest@example" using the webUI
    # Then the user can set new password that meets the password policy using webUI
