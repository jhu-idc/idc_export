<?php

namespace Drupal\idc_export\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'TaxonomyTermEntityReferenceCSVFormatter'.
 *
 * @FieldFormatter(
 *   id = "tax_term_entity_reference_csv",
 *   label = @Translation("Taxonomy Term Entity Reference CSV Formatter"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class TaxonomyTermEntityReferenceCSVFormatter extends EntityReferenceCSVFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {

      // The markup needs to end up in the format shown below so that the
      // parse_entity_lookup in the migration (idc_migrate) can later import the material.
      //
      // `<entity_type>:<bundle>:<value_key>:<value>`
      //
      // This assumes that `<entity_type>` is the default of `taxonomy_term` and
      // that the `<value_key>` is `name`, so they are not included here.

      $value = $entity->value;
      if (str_contains($value, self::DELIMITER)) {
        $value = $this->encode($value);
      }
      $elements[$delta] = [
        '#markup' => self::DELIMITER . $entity->bundle() . self::DELIMITER . self::DELIMITER . $value
      ];
      if (array_key_exists("#plain_text", $elements[$delta])) {
        unset($elements[$delta]["#plain_text"]);
      }
    }
    return $elements;

  }

}
