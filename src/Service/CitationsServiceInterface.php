<?php

namespace Drupal\idc_export\Service;

/* Borrowed from Brian Brown's code at https://github.com/fsulib/citations */

/**
 * Interface CitationsServiceInterface initiates the public functions.
 */
interface CitationsServiceInterface {

  /**
   * {@inheritdoc}
   */
  public function renderCitationMetadataFromNode($nid);

  /**
   * {@inheritdoc}
   */
  public function renderFromMetadata($metadata, $style, $mode);

  /**
   * {@inheritdoc}
   */
  public function renderCitationFromMetadata($metadata, $style);

  /**
   * {@inheritdoc}
   */
  public function renderBibliographyFromMetadata($metadata, $style);

  /**
   * {@inheritdoc}
   */
  public function getStylePath($style);

  /**
   * {@inheritdoc}
   */
  public function getStyleMetadata($style);

}
