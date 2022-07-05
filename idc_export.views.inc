<?php

/**
 * @file
 * Contains hooks for Views.
 */

/**
 * Implements hook_views_data_alter().
 */
function idc_export_views_data_alter(&$data) {

  // This simply adds a dummy field as a placeholder for local_id.
  // We don't have access to local_id, but need a column for it in the exported
  // file so that round-tripping the file works w/o having to add a column.
  // This field will not contain anything.
  $data['search_api_index_default_solr_index']['new_local_id'] = [
    'title' => t('dummy local_id'),
    'field' => [
      'title' => t('dummy local_id field'),
      'help' => t('Creates column local_id, it is empty for now and will not be populated.'),
      'id' => 'local_id',
    ],
  ];

  // Add some citations fields - these will be populated fields
  // MLA Citation.
  $data['search_api_index_default_solr_index']['citation_mla'] = [
    'title' => t('MLA Citation Field'),
    'field' => [
      'title' => t('MLA Citation Field'),
      'help' => t('Dynamically populated MLA Citation Field'),
      'id' => 'citation_mla',
    ],
  ];

  // APA Citation Field.
  $data['search_api_index_default_solr_index']['citation_apa'] = [
    'title' => t('APA Citation Field'),
    'field' => [
      'title' => t('APA Citation Field'),
      'help' => t('Dynamically populated APA Citation Field'),
      'id' => 'citation_apa',
    ],
  ];

  // Chicago Citation Field.
  $data['search_api_index_default_solr_index']['citation_chicago'] = [
    'title' => t('Chicago Citation Field'),
    'field' => [
      'title' => t('Chicago Citation Field'),
      'help' => t('Dynamically populated Chicago Citation Field'),
      'id' => 'citation_chicago',
    ],
  ];

}
