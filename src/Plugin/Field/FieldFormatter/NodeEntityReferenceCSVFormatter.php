<?php


namespace Drupal\idc_export\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'NodeEntityReferenceCSVFormatter'.
 *
 * @FieldFormatter(
 *   id = "node_entity_reference_csv",
 *   label = @Translation("Node Entity Reference CSV Formatter"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class NodeEntityReferenceCSVFormatter extends EntityReferenceCSVFormatter {

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
      // This function assumes that `<entity_type>` is the default of `node` and
      // that the `<value_key>` is `title`, so they are not included here.

      $value = $entity->get(self::value_field)->getString();
      if (str_contains($value, self::delimiter)) {
        $value = $this->encode($value);
      }

      $elements[$delta] = array(
        '#markup' => self::delimiter . $entity->bundle() . self::delimiter . self::delimiter . $value
      );
      if (array_key_exists("#plain_text", $elements[$delta])) {
        unset($elements[$delta]["#plain_text"]);
      }
    }
    return $elements;
  }
}
