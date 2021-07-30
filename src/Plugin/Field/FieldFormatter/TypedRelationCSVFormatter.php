<?php

// Borrowed from ASU's asu_migrate module.
//
namespace Drupal\idc_export\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'TypedRelationCSVFormatter'.
 *
 * @FieldFormatter(
 *   id = "typed_relation_csv",
 *   label = @Translation("Typed Relation CSV Formatter"),
 *   field_types = {
 *     "typed_relation"
 *   }
 * )
 */
class TypedRelationCSVFormatter extends EntityReferenceCSVFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);
    $agent_vocab = $this->getSetting('agent_type');

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $term) {
      $value = $term->get(self::value_field)->getString();
      if (str_contains($value, self::delimiter)) {
        $value = $this->encode($value);
      }

      // The markup needs to end up in the format shown below so that the
      // parse_entity_lookup in the migration (idc_migrate) can later import the material.
      //
      // `<entity_type>:<bundle>:<value_key>:<value>`
      //
      // This assumes that `<entity_type>` is the default of `taxonomy_item` and
      // that the `<value_key>` is `name`, so they are not included here.
      
      $the_value = self::delimiter . $term->bundle() . self::delimiter . self::delimiter . $value;
      $elements[$delta]['#markup'] = $term->rel_type . ';' . $the_value;

      if (array_key_exists("#plain_text", $elements[$delta])) {
        unset($elements[$delta]["#plain_text"]);
      }
    }
    return $elements;
  }
}
