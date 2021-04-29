<?php


namespace Drupal\idc_export\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;

  /**
   * Plugin implementation of the 'LanguageTaxonomyCSVFormatter'.
   *
   * @FieldFormatter(
   *   id = "language_taxonomy_csv",
   *   label = @Translation("Language Taxonomy CSV Formatter"),
   *   field_types = {
   *     "entity_reference"
   *   }
   * )
   */
class LanguageTaxonomyCSVFormatter extends EntityReferenceLabelFormatter {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $elements[$delta] = array(
        '#markup' => $items[$delta]->entity->get('field_language_code')->getString()
      );
      if (array_key_exists("#plain_text", $elements[$delta])) {
        unset($elements[$delta]["#plain_text"]);
      }
    }
    return $elements;
  }
}
