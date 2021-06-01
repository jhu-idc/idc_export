<?php

/**
 * @file
 * Definition of Drupal\idc_export\Plugin\views\field\CitationChicago
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
 * @ViewsField("citation_chicago")
 */
class CitationChicago extends CitationField {

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    \Drupal::logger('idc_export')->info('rendering the node citation_chicago field');

    \Drupal::logger('idc_export')->info('title via index: ' . $values->_item->getField('title')->getValues()[0]);
    \Drupal::logger('idc_export')->info('title via index: ' . $values->_item->getField('field_description')->getValues()[0]);

    $metadata = $this->formatMetadata($values);
    $style = 'chicago-author-date';  // TODO: does this include URL? time will tell... 
    $citation = \Drupal::service('citations.default')->renderFromMetadata($metadata, $style, 'bibliography');
 
    \Drupal::logger('idc_export')->info('citation is ' . $citation);
    return $this->t($citation);
  }
}
