<?php declare(strict_types = 1);

namespace PHPStan\ExtensionInstaller;

/**
 * This class is generated by phpstan/extension-installer.
 * @internal
 */
final class GeneratedConfig
{

	public const EXTENSIONS = array (
  'composer/composer' => 
  array (
    'install_path' => '/Users/cathlenebaptista/Projects/drupal/vendor/composer/composer',
    'relative_install_path' => '../../../composer/composer',
    'extra' => 
    array (
      'includes' => 
      array (
        0 => 'phpstan/rules.neon',
      ),
    ),
    'version' => '2.8.3',
    'phpstanVersionConstraint' => NULL,
  ),
  'composer/pcre' => 
  array (
    'install_path' => '/Users/cathlenebaptista/Projects/drupal/vendor/composer/pcre',
    'relative_install_path' => '../../../composer/pcre',
    'extra' => 
    array (
      'includes' => 
      array (
        0 => 'extension.neon',
      ),
    ),
    'version' => '3.3.2',
    'phpstanVersionConstraint' => NULL,
  ),
  'mglaman/phpstan-drupal' => 
  array (
    'install_path' => '/Users/cathlenebaptista/Projects/drupal/vendor/mglaman/phpstan-drupal',
    'relative_install_path' => '../../../mglaman/phpstan-drupal',
    'extra' => 
    array (
      'includes' => 
      array (
        0 => 'extension.neon',
        1 => 'rules.neon',
      ),
    ),
    'version' => '2.0.0',
    'phpstanVersionConstraint' => '>=2.0.0.0-dev, <3.0.0.0-dev',
  ),
  'phpstan/phpstan-deprecation-rules' => 
  array (
    'install_path' => '/Users/cathlenebaptista/Projects/drupal/vendor/phpstan/phpstan-deprecation-rules',
    'relative_install_path' => '../../phpstan-deprecation-rules',
    'extra' => 
    array (
      'includes' => 
      array (
        0 => 'rules.neon',
      ),
    ),
    'version' => '2.0.1',
    'phpstanVersionConstraint' => '>=2.0.0.0-dev, <3.0.0.0-dev',
  ),
  'phpstan/phpstan-phpunit' => 
  array (
    'install_path' => '/Users/cathlenebaptista/Projects/drupal/vendor/phpstan/phpstan-phpunit',
    'relative_install_path' => '../../phpstan-phpunit',
    'extra' => 
    array (
      'includes' => 
      array (
        0 => 'extension.neon',
        1 => 'rules.neon',
      ),
    ),
    'version' => '2.0.1',
    'phpstanVersionConstraint' => '>=2.0.0.0-dev, <3.0.0.0-dev',
  ),
);

	public const NOT_INSTALLED = array (
);

	/** @var string|null */
	public const PHPSTAN_VERSION_CONSTRAINT = '>=2.0.0.0-dev, <3.0.0.0-dev';

	private function __construct()
	{
	}

}
