@webUI
Feature:
  As an administrator
  I want to expire user's passwords with different rules
  So that users can be forced to change their password as appropriate

  Background:
    Given user "Alice" has been created with default attributes and skeleton files

  Scenario: user tries to reset their password to one of their last 3 passwords after user password has expired
    Given the administrator has enabled the last passwords user password policy
    And the administrator has set the number of last passwords that should not be used to "3"
    And the administrator has reset the password of user "Alice" to "Number2"
    And the administrator has reset the password of user "Alice" to "Number3"
    And the administrator has reset the password of user "Alice" to "Number4"
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When user "Alice" enters the current password, chooses a new password "Number2" and confirms it using the webUI
    Then an error message with the following text should be displayed on the webUI:
    """
    The password must be different than your previous 3 passwords.
    """

  Scenario: user tries to update expired password with enabled lowercase letters password policy
    Given the administrator has enabled the lowercase letters password policy
    And the administrator has set the lowercase letters required to "3"
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When user "Alice" enters the current password, chooses a new password "0LOWERCASE" and confirms it using the webUI
    Then an error message with the following text should be displayed on the webUI:
    """
    The password contains too few lowercase letters. At least 3 lowercase letters are required.
    """

  Scenario: user tries to update expired password with enabled minimum characters password policy
    Given the administrator has enabled the minimum characters password policy
    And the administrator has set the minimum characters required to "10"
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When user "Alice" enters the current password, chooses a new password "A" and confirms it using the webUI
    Then an error message with the following text should be displayed on the webUI:
    """
    The password is too short. At least 10 characters are required.
    """

  Scenario: user tries to update expired password with enabled numbers password policy
    Given the administrator has enabled the numbers password policy
    And the administrator has set the numbers required to "3"
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When user "Alice" enters the current password, chooses a new password "hello" and confirms it using the webUI
    Then an error message with the following text should be displayed on the webUI:
    """
    The password contains too few numbers. At least 3 numbers are required.
    """

  Scenario: user tries to update expired password with enabled special characters password policy
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When user "Alice" enters the current password, chooses a new password "NoSpecialCharacters123" and confirms it using the webUI
    Then an error message with the following text should be displayed on the webUI:
    """
    The password contains too few special characters. At least 3 special characters are required.
    """

  Scenario Outline: user tries to update expired password with enabled special characters
  password policy with required restricted special characters
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When user "Alice" enters the current password, chooses a new password "<password>" and confirms it using the webUI
    Then an error message with the following text should be displayed on the webUI:
    """
    <message>
    """
    Examples:
      | password                        | message                                                                                               |
      | Only#Invalid!Special@Characters | The password contains invalid special characters. Only $%^&* are allowed.                             |
      | NoSpecialCharacters123          | The password contains too few special characters. At least 3 special characters ($%^&*) are required. |

  Scenario: user tries to update expired password with enabled uppercase letters password policy
    Given the administrator has enabled the uppercase letters password policy
    And the administrator has set the uppercase letters required to "3"
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When user "Alice" enters the current password, chooses a new password "0uppercase" and confirms it using the webUI
    Then an error message with the following text should be displayed on the webUI:
    """
    The password contains too few uppercase letters. At least 3 uppercase letters are required.
    """

  Scenario: user tries to update expired password with a combination of password policy settings
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
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When user "Alice" enters the current password, chooses a new password "15&%!UPPlowZZZZ" and confirms it using the webUI
    Then an error message with the following text should be displayed on the webUI:
    #  where multiple requirements are not met, only the first error message is shown to the user
    """
    The password contains too few lowercase letters. At least 4 lowercase letters are required.
    """

  Scenario: user updates expired password with a password that meets the policy requirements
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
    And the administrator has enabled the days until user password expires user password policy
    And the administrator has expired the password of user "Alice"
    And user "Alice" has logged in with the expired password using the webUI
    When user "Alice" enters the current password, chooses a new password "15#!!UPPloweZZZ" and confirms it using the webUI
    Then the user should be redirected to a webUI page with the title "Files - %productname%"
