<?php

/**
 * @file
 * Definition of Drupal\idc_export\Plugin\views\field\CitationMLA
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
 * @ViewsField("citation_mla")
 */
class CitationMLA extends CitationField {

  /**
   * @{inheritdoc}
   */
  public function render(ResultRow $values) {
    \Drupal::logger('idc_export')->info('rendering the node citation_mla field');

    //FIX THIS FIX THIS -- print_r not working
    //\Drupal::logger('idc_export')->info('what\'s avail: ' . print_r($values->_entity, true));
    
    //\Drupal::logger('idc_export')->info('title is: ' . $values->title[0]['raw']['value']);
    //\Drupal::logger('idc_export')->info('description is: ' . $values->field_field_description[0]['raw']['value']);
    //\Drupal::logger('idc_export')->info('item id: ' . $values->_itemId);
    //\Drupal::logger('idc_export')->info('originalObject: ' . $values->_originalObject->title[0]);
    \Drupal::logger('idc_export')->info('title via index: ' . $values->_item->getField('title')->getValues()[0]);
    \Drupal::logger('idc_export')->info('title via index: ' . $values->_item->getField('field_description')->getValues()[0]);
    //\Drupal::logger('idc_export')->info('title via index: ' . $values->_item->getField('description'));
    //dpm($values);
    //dpm($values->_item->getField('title')->getValues()[0]);

    // create json file of information
    //$data = file_get_contents("metadata.json");
    //
    //
    $metadata = $this->formatMetadata($values);
    $style = "modern-language-association";
    $citation = \Drupal::service('citations.default')->renderFromMetadata($metadata, $style, 'bibliography');
 
    \Drupal::logger('idc_export')->info('citation is ' . $citation);
    return $this->t($citation);

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
