Feature: Detect files having extensions with incorrect MIME-types

  Scenario: Detect a .png file with PHP code
    Given a WP install
    And a wp-content/uploads/image.png file:
    """
    <?php malicious_code();
    """

    When I run `wp doctor check validate-mime`
    Then STDOUT should be a table containing rows:
      | name          | status  | message                                  |
      | validate-mime | warning | Files detected with different MIME type. |

  Scenario: Detect a .png file with PHP code with error status
    Given a WP install
    And a config.yml file:
      """
      validate-mime:
        check: Validate_Mime
        options:
          status_for_failure: error
      """
    And a wp-content/uploads/image.png file:
    """
    <?php malicious_code();
    """

    When I run `wp doctor check validate-mime`
    Then STDOUT should be a table containing rows:
      | name          | status  | message                                  |
      | validate-mime | error | Files detected with different MIME type. |
