<?php

namespace Drupal\Core;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * The interface for DrupalKernel, the core of Drupal.
 *
 * This interface extends Symfony's KernelInterface and adds methods for
 * responding to modules being enabled or disabled during its lifetime.
 */
interface DrupalKernelInterface extends HttpKernelInterface {

  /**
   * Event fired when the service container finished initializing in subrequest.
   *
   * This event allows you to initialize overrides such as language to the
   * services.
   *
   * @var string
   */
  const CONTAINER_INITIALIZE_SUBREQUEST_FINISHED = 'kernel.container.finish_container_initialize_subrequest';

  /**
   * Boots the current kernel.
   *
   * @return $this
   */
  public function boot();

  /**
   * Shuts down the kernel.
   */
  public function shutdown();

  /**
   * Discovers available serviceProviders.
   *
   * @return array
   *   The available serviceProviders.
   */
  public function discoverServiceProviders();

  /**
   * Returns all registered service providers.
   *
   * @param string $origin
   *   The origin for which to return service providers; one of 'app' or 'site'.
   *
   * @return array
   *   An associative array of ServiceProvider objects, keyed by name.
   */
  public function getServiceProviders($origin);

  /**
   * Gets the current container.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface
   *   A ContainerInterface instance.
   */
  public function getContainer();

  /**
   * Returns the cached container definition - if any.
   *
   * This also allows inspecting a built container for debugging purposes.
   *
   * @return array|null
   *   The cached container definition or NULL if not found in cache.
   */
  public function getCachedContainerDefinition();

  /**
   * Set the current site path directory.
   *
   * Format: "folder-name/child-folder" usually uses "sites/default".
   *
   * @param string $path
   *   The current site path.
   *
   * @throws \LogicException
   *   In case the kernel is already booted.
   */
  public function setSitePath($path);

  /**
   * Gets the site path directory.
   *
   * @return string
   *   The current site path directory.
   */
  public function getSitePath();

  /**
   * Gets the app root.
   *
   * @return string
   *   The path of the application root.
   */
  public function getAppRoot();

  /**
   * Updates the kernel's list of modules to the new list.
   *
   * The kernel needs to update its bundle list and container to match the new
   * list.
   *
   * @param array $module_list
   *   The new list of modules.
   * @param array $module_filenames
   *   List of module filenames, keyed by module name.
   */
  public function updateModules(array $module_list, array $module_filenames = []);

  /**
   * Force a container rebuild.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface
   *   The rebuilt Symfony container.
   */
  public function rebuildContainer();

  /**
   * Force a container reset.
   *
   * @return \Symfony\Component\DependencyInjection\ContainerInterface
   *   The Symfony container.
   */
  public function resetContainer(): ContainerInterface;

  /**
   * Invalidate the service container for the next request.
   */
  public function invalidateContainer();

  /**
   * Helper method that does request related initialization.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request.
   */
  public function preHandle(Request $request);

  /**
   * Helper method that loads legacy Drupal include files.
   */
  public function loadLegacyIncludes();

}
