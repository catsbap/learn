<?php

declare(strict_types=1);

namespace Drupal\Tests\navigation\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\PerformanceTestBase;

/**
 * Tests performance with the navigation toolbar enabled.
 *
 * Stark is used as the default theme so that this test is not Olivero specific.
 *
 * @todo move this coverage to StandardPerformanceTest when Navigation is
 * enabled by default.
 *
 * @group Common
 * @group #slow
 * @requires extension apcu
 */
class PerformanceTest extends PerformanceTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected $profile = 'standard';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Uninstall the toolbar.
    \Drupal::service('module_installer')->uninstall(['toolbar']);
    \Drupal::service('module_installer')->install(['navigation']);
  }

  /**
   * Tests performance of the navigation toolbar.
   */
  public function testLogin(): void {
    $user = $this->drupalCreateUser();
    $user->addRole('administrator');
    $user->save();
    $this->drupalLogin($user);
    // Request the front page twice to ensure all cache collectors are fully
    // warmed. The exact contents of cache collectors depends on the order in
    // which requests complete so this ensures that the second request completes
    // after asset aggregates are served.
    $this->drupalGet('');
    sleep(1);
    $this->drupalGet('');
    // Flush the dynamic page cache to simulate visiting a page that is not
    // already fully cached.
    \Drupal::cache('dynamic_page_cache')->deleteAll();
    $performance_data = $this->collectPerformanceData(function () {
      $this->drupalGet('');
    }, 'navigation');

    $expected_queries = [
      'SELECT "session" FROM "sessions" WHERE "sid" = "SESSION_ID" LIMIT 0, 1',
      'SELECT * FROM "users_field_data" "u" WHERE "u"."uid" = "2" AND "u"."default_langcode" = 1',
      'SELECT "roles_target_id" FROM "user__roles" WHERE "entity_id" = "2"',
      'SELECT "name", "value" FROM "key_value" WHERE "name" IN ( "theme:stark" ) AND "collection" = "config.entity.key_store.block"',
    ];
    $recorded_queries = $performance_data->getQueries();
    $this->assertSame($expected_queries, $recorded_queries);

    $expected = [
      'QueryCount' => 4,
      'CacheGetCount' => 61,
      'CacheGetCountByBin' => [
        'config' => 10,
        'data' => 6,
        'access_policy' => 1,
        'bootstrap' => 6,
        'dynamic_page_cache' => 2,
        'discovery' => 11,
        'render' => 23,
        'menu' => 1,
        'default' => 1,
      ],
      'CacheSetCount' => 2,
      'CacheSetCountByBin' => [
        'dynamic_page_cache' => 2,
      ],
      'CacheDeleteCount' => 0,
      'CacheTagChecksumCount' => 3,
      'CacheTagIsValidCount' => 31,
      'CacheTagInvalidationCount' => 0,
      'ScriptCount' => 2,
      'ScriptBytes' => 215500,
      'StylesheetCount' => 1,
      'StylesheetBytes' => 92000,
    ];
    $this->assertMetrics($expected, $performance_data);
    $this->assertSame(['core.extension.list.module'], $performance_data->getCacheOperations()['get']['default']);

    // Check that the navigation toolbar is cached without any high-cardinality
    // cache contexts (user, route, query parameters etc.).
    $this->assertIsObject(\Drupal::cache('render')->get('navigation:navigation:[languages:language_interface]=en:[theme]=stark:[user.permissions]=is-admin'));
  }

}
