<?php

/**
 * @file
 * Definition of Drupal\idc_export\Plugin\views\field\CitationField
 */

namespace Drupal\idc_export\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\NodeType;
use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * Parent class to help the Citation Fields
 *
 */
class CitationField extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  protected function getAuthors($creators, $contributors) {
    $relators = Array( 'relators:aut' );

    $tids = Array(); 
    foreach ($creators['rel_types'] as $key => $relType) {
      if (in_array($relType,$relators)) {
        // add it
        $tids[] = $creators['target_ids'][$key];
      }
    }

    foreach ($contributors['rel_types'] as $key => $relType) {
      if (in_array($relType, $relators)) {
        // add it
        $tids[] = $creators['target_ids'][$key];
      }
    }

    $tids = array_unique($tids, SORT_NUMERIC);

    return \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);
  }

  protected function formatAuthors($terms) {

    foreach ($terms as $term) {
      //dpm($term);
      \Drupal::logger('idc_export')->info('term is from: ' . $term->bundle());
      // expecting things to be from corporate_body, family or person vocab 
      switch ($term->bundle) {
        case'person':
         
          break;
        case 'corporate_body':
          break;
        case 'family':
          break;
        default:
      }

      //var_dump($term->tid->value); //return tid of term
      //var_dump($term->name->value); //return title of term

      //$value = $term->FIELD_MACHINENAME->value;
      // look up item 
    }
  }

  protected function formatMetadata(ResultRow $values) {

    // NOTE TO SELF: theses are search api fields, not node fields. 
    // values needed: 
    // $title = $values->_item->getField('title')->getValues()[0];
    $creators = Array();
    $contributors = Array();
    $creators['target_ids'] = $values->_item->getField('field_creator_id')->getValues();
    $creators['rel_types'] = $values->_item->getField('field_creator_rel_type')->getValues();
    $contributors['target_ids'] = $values->_item->getField('field_contributor')->getValues();
    $contributors['rel_types'] = $values->_item->getField('field_contributor_rel_type')->getValues();

    $authors = $this->getAuthors($creators, $contributors);

    //"title" => $values->_item->getField('title')->getValues()[0],

    $json_data = '
      [
          {
              "author": [
                  {
                      "family": "Doe",
                      "given": "James",
                      "suffix": "III"
                  }
              ],
              "id": "item-1",
              "issued": {
                  "date-parts": [
                      [
                          "2001"
                      ]
                  ]
              },
              "title": "My Anonymous Heritage",
              "type": "book"
          }
      ]';

    $decoded = json_decode($json_data);
    //dpm($decoded);
    return $decoded;
  } 
}
