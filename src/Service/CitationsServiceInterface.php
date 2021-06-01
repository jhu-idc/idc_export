<?php

/* borrowed from Brian Brown's code at https://github.com/fsulib/citations */

namespace Drupal\idc_export\Service;

/**
 * Interface CitationsServiceInterface.
 */
interface CitationsServiceInterface {

  public function renderCitationMetadataFromNode($nid);
  public function renderFromMetadata($metadata, $style, $mode);
  public function renderCitationFromMetadata($metadata, $style);
  public function renderBibliographyFromMetadata($metadata, $style);
  public function getStylePath($style);
  public function getStyleMetadata($style);

}
