Feature: Check whether themes are up to date

  Scenario: Verify check description
    Given an empty directory

    When I run `wp doctor list --fields=name,description`
    Then STDOUT should be a table containing rows:
      | name                       | description                                                                    |
      | theme-update               | Warns when there are theme updates available.                                  |

  Scenario: Themes are up to date
    Given a WP install

    When I run `wp doctor check theme-update`
    Then STDOUT should be a table containing rows:
      | name          | status  | message                                 |
      | theme-update  | success | Themes are up to date.                  |

  Scenario: One theme has an update available
    Given a WP install
    And I run `wp theme install p2 --version=1.5.1`

    When I run `wp doctor check theme-update`
    Then STDOUT should be a table containing rows:
      | name          | status  | message                                 |
      | theme-update  | warning | 1 theme has an update available.       |

  Scenario: One theme has an update available with data field
    Given a WP install
    And I run `wp theme install p2 --version=1.5.1`

    When I run `wp doctor check theme-update --fields=name,status,message,data`
    Then STDOUT should be a table containing rows:
      | name          | status  | message                                 | data |
      | theme-update  | warning | 1 theme has an update available.       | [{"name":"p2","status":"inactive","update":"available","version":"1.5.1"}] |

  Scenario: One theme has an update available with data field and JSON format
    Given a WP install
    And I run `wp theme install p2 --version=1.5.1`

    When I run `wp doctor check theme-update --fields=name,status,message,data --format=json`
    Then STDOUT should be JSON containing:
      """
      [{"name":"theme-update","status":"warning","message":"1 theme has an update available.","data":[{"name":"p2","status":"inactive","update":"available","version":"1.5.1"}]}]
      """

  Scenario: One theme has an update available with error status
    Given a WP install
    And a config.yml file:
      """
      theme-update:
        check: Theme_Update
        options:
          status_for_failure: error
      """
    And I run `wp theme install p2 --version=1.5.1`

    When I run `wp doctor check theme-update --config=config.yml`
    Then STDOUT should be a table containing rows:
      | name          | status  | message                                 |
      | theme-update  | error   | 1 theme has an update available.        |
