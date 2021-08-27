<?php

/**
 * @file
 * Definition of Drupal\idc_export\Plugin\views\field\CitationAPA
 */

namespace Drupal\idc_export\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\CiteProc;

/**
 * Field handler to get the local_id for a node
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("citation_apa")
 */
class CitationAPA extends CitationField {

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    \Drupal::logger('idc_export')->info('rendering the node citation_apa field');
    //\Drupal::logger('idc_export')->info('originalObject: ' . $values->_originalObject->title[0]);
    //\Drupal::logger('idc_export')->info('title via index: ' . $values->_item->getField('title')->getValues()[0]);
    //\Drupal::logger('idc_export')->info('description via index: ' . $values->_item->getField('field_description')->getValues()[0]);

    $metadata = $this->formatMetadata($values);
    //dpm($metadata);
    $style = 'apa';
    $citation = \Drupal::service('citations.default')->renderFromMetadata($metadata, $style, 'bibliography');

    \Drupal::logger('idc_export')->info('citation is ' . $citation);
    return $this->t($citation);
  }
}
