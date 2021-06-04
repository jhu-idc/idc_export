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
    \Drupal::logger('idc_export')->info('getAuthors');
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
    // goal: return list of array objects formatted like so:
    //     array(
    //        "family" => "Doe",
    //        "given" => "James",
    //        "suffix" => "III"
    //        )
    $authors = Array();
    foreach ($terms as $nid => $term) {
      // expecting things to be from corporate_body, family or person vocab
      $author = Array();

      switch ($term->bundle()) {
        case'person':
          \Drupal::logger('idc_export')->info('term is still from: ' . $term->bundle());
            if (count($term->field_primary_part_of_name->getValue())) {
              $author['family'] = $term->field_primary_part_of_name->value;
            }
            if (count($term->field_preferred_name_rest->getValue())) {
              $author['given'] = $term->field_preferred_name_rest->value;
            }
            if (count($term->field_preferred_name_suffix->getValue())) {
              $author['suffix'] = $term->field_preferred_name_suffix->value;
            }
          break;
        case 'corporate_body':
            if (count($term->field_primary_name->getValue())) {
              $author['family'] = $term->field_primary_name->value;
            }
            if (count($term->field_subordinate_name->getValue())) {
              $author['given'] = $term->field_subordinate_name->value;
            }
          break;
        case 'family':
            if (count($term->field_family_name->getValue())) {
              $author['family'] = $term->field_family_name->value;
            }
          break;
        default:
      }
      $authors[] = (object) $author;
    }
    return $authors;
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

    $authors = $this->formatAuthors($this->getAuthors($creators, $contributors));

    $title = $values->_item->getField('title')->getValues()[0];
    $resourceTypes = $this->getResourceTypes($values->_item->getField('field_resource_type')->getValues());
    $dateAvail = $values->_item->getField('field_date_available')->getValues()[0];
    // aka published.
    /* This is a WIP and is not functioning yet.
    $dateIssued = $values->_item->getField('field_date_published')->getValues();
    $diArray = Array();
    foreach($dateIssued as $date){
      $diArray[] = Array(
        "edtf" => Array($date)
      );
    }
     */

    $data = Array(
      (object) Array(
        "author" => $authors,
        "id" => $values->_item->getField('nid')->getValues()[0],
        "issued" => (object) Array(
          "edtf" => Array(
            Array($dateAvail)
          )
        ),
        //"issued" => (object) $diArray,
        "available-date" => (object) Array(
          "edtf" => Array($dateAvail)
        ),
        "title" => utf8_encode($title),
        "type" => $resourceTypes[0],
        "URL" => $values->_item->getField('field_citable_url')->getValues()[0]
      )
    );

 /*   $json_data = '
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
*/
    //$decoded = json_decode($json_data);
    //dpm($decoded);
    //dpm($data);
    return $data;
  }

  protected function getResourceTypes($type) {
    // TODO -- look into a better mapping. Options are limited here: https://github.com/citation-style-language/schema/blob/master/schemas/input/csl-data.json#L9
    $mapping = Array(
      'Dataset' => 'dataset',
      'Image' => 'graphic',
      'Interactive Resource' => 'webpage',
      'Moving Image' => 'motion_picture',
      'Physical Object' => 'book',
      'Service' => 'event',
      'Sound' => 'song',
      'Still Image' => 'graphic',
      'Software' => 'software',
      'Text' => 'document'
    );

    return Array($mapping[$type[0]]);
  }
}
