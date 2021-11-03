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
use Drupal\controlled_access_terms\EDTFConverter;
use Symfony\Polyfill\Mbstring\Mbstring;


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


  /**
   * This gets the term for each creator/contributor passed in
   *
   * @param Array of creators with keys for target_ids and rel_types
   * @param Array of contributors with keys for target_ids and rel_types
   * @return An array of taxonomy terms (tid->term) for all the creators/contributors passed in
   */
  private function getCreatorsAndContributors($creators, $contributors) {

    $tids = Array();
    foreach ($creators['rel_types'] as $key => $relType) {
      $tids[] = $creators['target_ids'][$key];
    }

    foreach ($contributors['rel_types'] as $key => $relType) {
      $tids[] = $contributors['target_ids'][$key];
    }

    $tids = array_unique($tids, SORT_NUMERIC);
    return \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadMultiple($tids);
  }


  /**
   * This organizes the creators/contributors into MARC relator categories, so you end up with an
   * array of MARC relators, each with an array of users in the system associated with that role
   * for a specific object.
   *
   * @param Array of creators with keys for target_ids and rel_types
   * @param Array of contributors with keys for target_ids and rel_types
   * @return An array of arrays, of MARC relator => Users associated with that role
   *         for this particular node
   */
  private function formatCreatorsAndContributors($creators, $contributors) {
    $terms = $this->getCreatorsAndContributors($creators, $contributors);
    // "relator" -> Array(Array(names)))
    $results = Array();

    foreach ($creators['target_ids'] as $key => $tid) {
      $name_array = $this->getNameArray($terms[$tid]);
      if (!isset($results[$creators['rel_types'][$key]])) {
        $results[$creators['rel_types'][$key]] = Array();
      }
      $results[$creators['rel_types'][$key]][$name_array['unique_id']] =  (object) $name_array;
    }

    foreach ($contributors['target_ids'] as $key => $tid) {
      $name_array = $this->getNameArray($terms[$tid]);
      if (!isset($results[$contributors['rel_types'][$key]])) {
        $results[$contributors['rel_types'][$key]] = Array();
      }
      $results[$contributors['rel_types'][$key]][$name_array['unique_id']] = (object) $name_array;
    }
    return $results;
  }


  /**
   * This will take a taxonomy term from the Person, Corporate Body or Family taxonomy and return
   * an array of data from it, including "family", "given", and "suffix", etc.  This array is intended
   * to be passed to the citeproc algorithm, so it can generate a citation with it.
   *
   * @param A taxonomy term, should be from the Person, Corporate Body or Family taxonomy.
   * @return An array that provides information about the term formatted to go into the
   *         citeproc function
   */
  private function getNameArray($term) {
    // goal: return list of array formatted like so:
    //     array(
    //        "family" => "Doe",
    //        "given" => "James",
    //        "suffix" => "III"
    //        )
    // expecting things to be from corporate_body, family or person vocab
    $author = Array();

    switch ($term->bundle()) {
      case'person':
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

    $author['unique_id'] = $term->field_unique_id->value;
    return $author;
  }


  /**
   * This takes in an array of EDTF formatted dates and returns them in a new array, which
   * is in the format that the citation algorithm expects.
   *
   * @param An array of dates that are in EDTF format
   * @return  An array of dates in the proper format expected by the citations algorithm
   */
  private function formatDates($node_dates) {
    $dates = Array();

    foreach($node_dates as $adate) {
      $iso_date = EDTFConverter::dateIso8601Value(Array("value" => $adate));
      if (strpos($iso_date, 'T') > -1) {
        list($date, $time) = explode('T', $iso_date);
      }
      else {
        $date = (string) $iso_date;
        $time = NULL;
      }

      $exploded_date= explode('-', $date);
      $date_info = Array();

      foreach ($exploded_date as $index => $part) {
        $date_info[$index] = $part;
      }

      $dates[] = $date_info;
    }

    return $dates;
  }


  /**
   * This method will populate the appropriate areas in the $data_array for
   * creators / contributors (like the "author" field).
   *
   * @param An array of data about creators/contributors for the node we are working on
   * @param The array that will be passed to the citation processor
   */
  private function populateCreatorsContributors($cc_data, &$data_array) {
    $mapping = Array (
      "author" =>             ["relators:aut", "relators:pht", "relators:art"],
      "chair" =>              [],
      "collection-editor" =>  [],
      "compiler" =>           ["relators:com"],
      "composer" =>           ["relators:cmp"],
      "container-author" =>   [],
      "contributor" =>        ["relators:ctb"],
      "curator" =>            ["relators:cur"],
      "director" =>           ["relators:adi", "relators:ard", "relators:drt", "relators:fld",
                               "relators:fmd", "relators:msd", "relators:rdd", "relators:pbd",
                               "relators:pdr", "relators:sgd", "realtors:tcd", "relators:tld"],
      "editor" =>             ["relators:edt", "relators:edc", "relators:edm","relators:flm"],
      "editorial-director" => [],
      "executive-producer" => ["relators:pro"],
      "guest" =>              ["relators:ive"],
      "host" =>               ["relators:hst", "relators:his"],
      "interviewer" =>        ["relators:ivr"],
      "illustrator" =>        ["relators:ill"],
      "narrator" =>           ["relators:nrt", "relators:cmm"],
      "organizer" =>          ["relators:orm"],
      "original-author" =>    [],
      "performer" =>          ["relators:prf", "relators:act", "relators:dnc"],
      "producer" =>           ["relators:pro"],
      "recipient" =>          ["relators:rcp"],
      "reviewed-author" =>    [],
      "script-writer" =>      ["relators:aus"],
      "series-creator" =>     [],
      "translator" =>         ["relators:trl"]
    );

    foreach($mapping as $item => $relators) {
      if (isset($relators) && count($relators) > 0) {
        foreach($relators as $rel) {
          if (isset($cc_data[$rel]) && count($cc_data[$rel]) > 0) {
            if (!isset($data_array[$item])) {
              $data_array[$item] = Array();
            }
            $data_array[$item] = array_merge($data_array[$item], $cc_data[$rel]);
          }
        }
      }
    }
  }

  /**
   * This method will map the resource type data into the proper mapping for
   * the citation processor.
   *
   * @param An array of resource types attached to the node we are gathering metadata for
   * @return An array of citation style language resource types that the passed in ones map to
   */
  private function getResourceTypes($types) {
    // TODO -- look into a better mapping.
    // These are the current options
    //
    // Islandora Resource Types  =>  CSL type
    //    https://github.com/citation-style-language/schema/blob/master/schemas/input/csl-data.json#L9
    //
    //    collection

    $mapping = Array(
      'Dataset' => 'dataset',
      'Event' => 'event',
      'Image' => 'graphic',
      'Interactive Resource' => 'webpage',
      'Moving Image' => 'motion_picture',
      'Physical Object' => 'book', // this isn't quite right, as there are more that just books
      'Service' => 'event',
      'Software' => 'software',
      'Sound' => 'interview',  // song?
      'Still Image' => 'graphic',
      'Text' => 'document'
    );

    $resource_types = Array();
    foreach ($types as $type) {
      $resource_types[] = $mapping[$type];
    }

    return $resource_types;
  }


  /**
   * This will format the metadata on an object and put it into the proper
   * format that the citation processing expects.  Note that this is using
   * search results from solr, as the citation fields are created from
   * solr search results and are not real fields on an object.
   *
   * @param The row of results for the current search
   * @return Returns a formatted array of data that can be handed to the citation processor.
   */
  protected function formatMetadata(ResultRow $values) {

    // NOTE TO SELF: theses are search api fields, not node fields.
    // Another note to self: another way to get info, but below we will just use the solr
    // data for now.
    //$nid = $values->_item->getField('nid')->getValues()[0];
    //$node = \Drupal\node\Entity\Node::load($nid);
    //$node = $this->entityTypeManager->getStorage('node')->load($nid);

    $creators = Array();
    $contributors = Array();
    $creators['target_ids'] = $values->_item->getField('field_creator_id')->getValues();
    $creators['rel_types'] = $values->_item->getField('field_creator_rel_type')->getValues();
    $contributors['target_ids'] = $values->_item->getField('field_contributor')->getValues();
    $contributors['rel_types'] = $values->_item->getField('field_contributor_rel_type')->getValues();
    $creatorsContributorsArray = $this->formatCreatorsAndContributors($creators, $contributors);

    $title = $values->_item->getField('title')->getValues()[0];
    $resource_types = $this->getResourceTypes($values->_item->getField('field_resource_type')->getValues());
    $digital_publisher = $values->_item->getField('field_digital_publisher')->getValues()[0];

    // EDTF is not supported by this processor so we have to use date-parts instead
    $dates = $this->formatDates($values->_item->getField('field_date_available')->getValues());

    $entry = Array(
        "id" => $values->_item->getField('nid')->getValues()[0],
        "type" => $resource_types[0],
        "issued" => (object) Array(
          "date-parts" => $dates
        ),
        "ISSN" => @$values->_item->getField('field_issn')->getValues()[0],
        "collection-number" => @$values->_item->getField('field_collection_number')->getValues()[0],
        "publisher" => $digital_publisher,
        "title" => $title,
        "URL" => $values->_item->getField('field_citable_url')->getValues()[0]
      );

    $this->populateCreatorsContributors($creatorsContributorsArray, $entry);

    //dpm($entry);
    $data = Array(
      (object) $entry
    );

    return $data;
  }
}
