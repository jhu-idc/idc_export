<?php

namespace Drupal\idc_export\Service;

/* this code is originally from https://github.com/fsulib/citations/ */

use Drupal\node\Entity\Node;
use Seboettg\CiteProc\StyleSheet;
use Seboettg\CiteProc\CiteProc;

/**
 * Class CitationsService output citation data.
 */
class CitationsService implements CitationsServiceInterface {

  /**
   * Constructs a new CitationsService object.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public function renderCitationMetadataFromNode($nid) {

  }

  /**
   * {@inheritdoc}
   */
  public function renderFromMetadata($metadata, $style, $mode) {
    $stylesheet = StyleSheet::loadStyleSheet($style);
    $citeProc = new CiteProc($stylesheet);
    $citation = $citeProc->render($metadata, $mode);

    // We might, at some point, decide we need to send back css information, as well.
    // If so, we might want to rethink how this part of the project is done.
    // Right now this service is used by a few dynamic fields in the Citation View.
    // If we need to pass back styles, we might want to have the UI code call
    // this service directly and then we can passback an object with the citations in it
    // and their individual styles as well.
    //
    // From experience so far, none of the citation styles we are rendering need any special
    // formatting.
    //
    // $cssStyles = $citeProc->renderCssStyles();
    return $citation;
  }

  /**
   * {@inheritdoc}
   */
  public function renderCitationFromMetadata($metadata, $style) {
    return \Drupal::service('citations.default')->renderFromMetadata($metadata, $style, 'citation');
  }

  /**
   * {@inheritdoc}
   */
  public function renderBibliographyFromMetadata($metadata, $style) {
    return \Drupal::service('citations.default')->renderFromMetadata($metadata, $style, 'bibliography');
  }

  /**
   * {@inheritdoc}
   */
  public function getStylePath($style) {
  }

  /**
   * {@inheritdoc}
   */
  public function getStyleMetadata($style) {
    $style_path = \Drupal::service('citations.default')->getStylePath($style);
    $xml = simplexml_load_file($style_path);
    $style_metadata['path'] = $style_path;
    $style_metadata['title'] = (string) $xml->info->title;
    foreach ($xml->info->link as $link) {
      switch ((string) $link['rel']) {
        case 'self':
          $style_metadata['url'] = (string) $link['href'];
          break;

        case 'documentation':
          $style_metadata['documentation'] = (string) $link['href'];
          break;
      }
    }
    return $style_metadata;
  }

}
