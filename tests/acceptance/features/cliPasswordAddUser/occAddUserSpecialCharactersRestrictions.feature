@cli
Feature: enforce the restricted special characters in a password when creating a user

  As an administrator
  I want user passwords to always contain some of a restricted list of special characters
  So that users cannot set passwords that have unusual hard-to-type characters

  Background:
    Given the administrator has enabled the special characters password policy
    And the administrator has set the special characters required to "3"
    And the administrator has enabled the restrict to these special characters password policy
    And the administrator has set the restricted special characters required to "$%^&*"

  Scenario Outline: admin creates a user with a password that has enough restricted special characters
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have been successful
    And the command output should contain the text 'The user "Alice" was created successfully'
    And user "Alice" should exist
    And the content of file "textfile0.txt" for user "Alice" using password "<password>" should be "ownCloud test text file 0" plus end-of-line
    Examples:
      | password              |
      | 3$Special%Characters^ |
      | 1*2&3^4%5$6           |

  @skipOnOcV10.2
  # The command output for errors is coming on stdout from core 10.3 onwards
  Scenario Outline: admin creates a user with a password that does not have enough restricted special characters
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have failed with exit code 1
    # Long text output comes on multiple lines. Here we just check for enough that will fit on one of the lines.
    And the command error output should contain the text 'The password contains too few special characters. At least 3 special char'
    And user "Alice" should not exist
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special&Characters |

  @skipOnOcV10.3
  # The command output for errors comes on stderr in core 10.2
  Scenario Outline: admin creates a user with a password that does not have enough restricted special characters
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have failed with exit code 1
    # Long text output comes on multiple lines. Here we just check for enough that will fit on one of the lines.
    And the command error output should contain the text 'The password contains too few special characters. At least 3 special char'
    And user "Alice" should not exist
    Examples:
      | password                 |
      | NoSpecialCharacters123   |
      | Only2$Special&Characters |

  @skipOnOcV10.2
  # The command output for errors is coming on stdout from core 10.3 onwards
  Scenario Outline: admin creates a user with a password that has invalid special characters
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have failed with exit code 1
    # Long text output comes on multiple lines. Here we just check for enough that will fit on one of the lines.
    And the command error output should contain the text 'The password contains invalid special characters. Only $%^&* are allowed.'
    And user "Alice" should not exist
    Examples:
      | password                                 |
      | Only#Invalid!Special@Characters          |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! |

  @skipOnOcV10.3
  # The command output for errors comes on stderr in core 10.2
  Scenario Outline: admin creates a user with a password that has invalid special characters
    When the administrator creates this user using the occ command:
      | username | password   |
      | Alice    | <password> |
    Then the command should have failed with exit code 1
    # Long text output comes on multiple lines. Here we just check for enough that will fit on one of the lines.
    And the command error output should contain the text 'The password contains invalid special characters. Only $%^&* are allowed.'
    And user "Alice" should not exist
    Examples:
      | password                                 |
      | Only#Invalid!Special@Characters          |
      | 1*2&3^4%5$6andInvalidSpecialCharacters#! |
