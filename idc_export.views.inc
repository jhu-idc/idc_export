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
  // We don't have access to local_id, but need a column for it in the exported file
  // so that round-tripping the file works w/o having to add a column.  This field 
  // will not contain anything. 
  $data['search_api_index_default_solr_index']['new_local_id'] = array(
    'title' => t('dummy local_id'),
    'field' => array(
      'title' => t('dummy local_id field'),
      'help' => t('Creates column local_id, it is empty for now and will not be populated.'),
      'id' => 'local_id',
    ),
  );
}
