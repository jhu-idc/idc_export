<?php

namespace Drupal\idc_export\Plugin\views\field;

/**
 * @file
 * Definition of Drupal\idc_export\Plugin\views\field\CitationAPA.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\CiteProc;

/**
 * Field handler to get the local_id for a node.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("citation_apa")
 */
class CitationAPA extends CitationField {

  // phpcs:ignore
  /**
   * @{inheritdoc}
   */
  // phpcs:ignore
  public function render(ResultRow $values) {
    $metadata = $this->formatMetadata($values);
    $style = 'apa';
    $citation = \Drupal::service('citations.default')->renderFromMetadata($metadata, $style, 'bibliography');

    return $this->t($citation);

  }

}
