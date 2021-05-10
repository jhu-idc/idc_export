<?php

/**
 * @file
 * Definition of Drupal\idc_export\Plugin\views\field\NodeTypeFlagger
 */

namespace Drupal\idc_export\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * Field handler to get the local_id for a node
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("local_id")
 */
class NodeLocalId extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }


  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    \Drupal::logger('idc_export')->info('rendering the node local_id field');

   // we don't need a value right now, but could add in migration id.  Test this as is first. 
   return $this->t('');  
   /*
    $node = $values->_entity;
    if ($node->bundle() == $this->options['node_type']) {
      return $this->t('Hey, I\'m of the type: @type', array('@type' => $this->options['node_type']));
    }
    else {
      return $this->t('Hey, I\'m something else.');
    }
    */
  }
}
