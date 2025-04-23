<?php

namespace Drupal\hello_world\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller for the salutation message.
 */
class HelloWorldController extends ControllerBase
{
    public function helloWorld()
    {
        return [
            '#markup' => $this->t('Hello World how are you doinglan'),
        ];
    }
}
