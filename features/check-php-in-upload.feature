Feature: Check for presence of .php files in the uploads folder

  Scenario: Detect PHP files
    Given a WP install
    And a wp-content/uploads/malicious.php file:
      """
      <?php some_malicious_code(); ?>
      """

    When I run `wp doctor check php-in-upload`
    Then STDOUT should be a table containing rows:
      | name          | status  | message                                   |
      | php-in-upload | warning | PHP files detected in the Uploads folder. |

  Scenario: Detect PHP files with error status
    Given a WP install
    And a config.yml file:
      """
      php-in-upload:
        check: PHP_In_Upload
        options:
          status_for_failure: error
      """
    And a wp-content/uploads/malicious.php file:
      """
      <?php some_malicious_code(); ?>
      """

    When I run `wp doctor check php-in-upload --config=config.yml`
    Then STDOUT should be a table containing rows:
      | name          | status  | message                                   |
      | php-in-upload | error | PHP files detected in the Uploads folder. |
