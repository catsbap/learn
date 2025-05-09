<?php

declare(strict_types=1);

namespace Drupal\Tests\rest\Functional;

use Drupal\Core\Session\AccountInterface;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\rest\Entity\RestResourceConfig;
use Drupal\rest\RestResourceConfigInterface;
use Drupal\Tests\BrowserTestBase;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;
use GuzzleHttp\RequestOptions;

/**
 * Tests the structure of a REST resource.
 *
 * @group rest
 */
class ResourceTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['rest', 'entity_test', 'rest_test'];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * The entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Create an entity programmatic.
    $this->entity = EntityTest::create([
      'name' => $this->randomMachineName(),
      'user_id' => 1,
      'field_test_text' => [
        0 => [
          'value' => $this->randomString(),
          'format' => 'plain_text',
        ],
      ],
    ]);
    $this->entity->save();

    Role::load(AccountInterface::ANONYMOUS_ROLE)
      ->grantPermission('view test entity')
      ->save();
  }

  /**
   * Tests that a resource without formats cannot be enabled.
   */
  public function testFormats(): void {
    RestResourceConfig::create([
      'id' => 'entity.entity_test',
      'granularity' => RestResourceConfigInterface::METHOD_GRANULARITY,
      'configuration' => [
        'GET' => [
          'supported_auth' => [
            'basic_auth',
          ],
        ],
      ],
    ])->save();

    // Verify that accessing the resource returns 406.
    $this->drupalGet($this->entity->toUrl()->setRouteParameter('_format', 'json'));
    // \Drupal\Core\Routing\RequestFormatRouteFilter considers the canonical,
    // non-REST route a match, but a lower quality one: no format restrictions
    // means there's always a match and hence when there is no matching REST
    // route, the non-REST route is used, but can't render into
    // application/json, so it returns a 406.
    $this->assertSession()->statusCodeEquals(406);
  }

  /**
   * Tests that a resource without authentication cannot be enabled.
   */
  public function testAuthentication(): void {
    RestResourceConfig::create([
      'id' => 'entity.entity_test',
      'granularity' => RestResourceConfigInterface::METHOD_GRANULARITY,
      'configuration' => [
        'GET' => [
          'supported_formats' => [
            'json',
          ],
        ],
      ],
    ])->save();

    // Verify that accessing the resource returns 401.
    $this->drupalGet($this->entity->toUrl()->setRouteParameter('_format', 'json'));
    // \Drupal\Core\Routing\RequestFormatRouteFilter considers the canonical,
    // non-REST route a match, but a lower quality one: no format restrictions
    // means there's always a match and hence when there is no matching REST
    // route, the non-REST route is used, but can't render into
    // application/json, so it returns a 406.
    $this->assertSession()->statusCodeEquals(406);
  }

  /**
   * Tests that serialization_class is optional.
   */
  public function testSerializationClassIsOptional(): void {
    RestResourceConfig::create([
      'id' => 'serialization_test',
      'granularity' => RestResourceConfigInterface::METHOD_GRANULARITY,
      'configuration' => [
        'POST' => [
          'supported_formats' => [
            'json',
          ],
          'supported_auth' => [
            'cookie',
          ],
        ],
      ],
    ])->save();
    \Drupal::service('router.builder')->rebuildIfNeeded();

    Role::load(RoleInterface::ANONYMOUS_ID)
      ->grantPermission('restful post serialization_test')
      ->save();

    $serialized = $this->container->get('serializer')->serialize(['foo', 'bar'], 'json');
    $request_options = [
      RequestOptions::HEADERS => ['Content-Type' => 'application/json'],
      RequestOptions::BODY => $serialized,
    ];
    /** @var \GuzzleHttp\ClientInterface $client */
    $client = $this->getSession()->getDriver()->getClient()->getClient();
    $response = $client->request('POST', $this->buildUrl('serialization_test', ['query' => ['_format' => 'json']]), $request_options);
    $this->assertSame(200, $response->getStatusCode());
    $this->assertSame('["foo","bar"]', (string) $response->getBody());
  }

  /**
   * Tests that resource URI paths are formatted properly.
   */
  public function testUriPaths(): void {
    /** @var \Drupal\rest\Plugin\Type\ResourcePluginManager $manager */
    $manager = \Drupal::service('plugin.manager.rest');

    foreach ($manager->getDefinitions() as $definition) {
      foreach ($definition['uri_paths'] as $uri_path) {
        $this->assertStringNotContainsString('//', $uri_path, 'The resource URI path does not have duplicate slashes.');
      }
    }
  }

}
