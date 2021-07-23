<?php


namespace Drupal\idc_export\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;

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
class NodeEntityReferenceCSVFormatter extends EntityReferenceLabelFormatter {

  /**
   * the delimiter used to separate fields in the formatting of the value
   */
  private const delimiter = ':';

  /**
   * the name of the field on the entity to pull the value from
   */
  private const value_field = 'field_unique_id';

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
      if (str_contains($value, $delimiter)) {
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


  /**
   * Encode the provided string. If the delimiter is present in the string, this function will
   * encode any uses of in into it's proper urlencoded form.
   *
   * @param string $delimiter the delimiter used to separate the fields
   * @param string $string a string that may contain the $delimiter
   * @return string the encoded string
   */
  function encode(string $delimiter, string $string): string {
      $encoded_delimiter = '';
      foreach (str_split($delimiter) as $char) {
          if (array_key_exists($char, self::reserved_char_map)) {
              $encoded_delimiter .= self::reserved_char_map[$char];
              continue;
          }
          $encoded_delimiter .= rawurlencode($char);
      }

      $encoded_string = str_ireplace($delimiter, $encoded_delimiter, $string);

      return $encoded_string;
    }
}
