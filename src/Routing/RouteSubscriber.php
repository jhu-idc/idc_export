<?php

namespace Drupal\idc_export\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {

    // Alter the canonical node route to our custom route
    if ($route = $collection->get('entity.node.canonical')) {
      $route->setDefault('_controller', '\Drupal\idc_export\Controller\NodeDisplayController::view');
    }
  }
}
