<?php

declare(strict_types=1);

namespace Drupal\Tests\Composer\Plugin\Scaffold;

/**
 * Holds result of a scaffold test.
 */
class ScaffoldTestResult {

  /**
   * The location of the scaffold fixture.
   */
  protected $docroot;

  /**
   * The stdout from the test.
   */
  protected $scaffoldOutput;

  /**
   * Holds the location of the scaffold fixture and the stdout from the test.
   *
   * @param string $docroot
   *   The location of the scaffold fixture.
   * @param string $scaffoldOutput
   *   The stdout from the test.
   */
  public function __construct($docroot, $scaffoldOutput) {
    $this->docroot = $docroot;
    $this->scaffoldOutput = $scaffoldOutput;
  }

  /**
   * Returns the location of the docroot from the scaffold test.
   *
   * @return string
   */
  public function docroot() {
    return $this->docroot;
  }

  /**
   * Returns the standard output from the scaffold test.
   *
   * @return string
   */
  public function scaffoldOutput() {
    return $this->scaffoldOutput;
  }

}
