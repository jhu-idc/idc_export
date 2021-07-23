<?php

// Borrowed from ASU's asu_migrate module.
//
namespace Drupal\idc_export\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
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
class TypedRelationCSVFormatter extends EntityReferenceLabelFormatter {

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
    $agent_vocab = $this->getSetting('agent_type');

    foreach ($items as $delta => $item) {
      $term = $item->entity;
      if (isset($elements[$delta])) {
        // Even if the config is to output links, this is not ever intended
        // for CSV output of these.
        if (array_key_exists("#title", $elements[$delta])) {
          $elements[$delta]["#markup"] = $elements[$delta]["#title"];
          unset($elements[$delta]["#url"]);
          unset($elements[$delta]["#options"]);
          unset($elements[$delta]["#type"]);
        }

        // TODO - fix this to use value_field above
        $string_value = (array_key_exists("#plain_text", $elements[$delta]) ?
          $elements[$delta]['#plain_text'] : $elements[$delta]["#title"]);

        if (str_contains($string_value, $delimter)) {
          $string_value = $this->encode($string_value);
        }

        // The markup needs to end up in the format shown below so that the
        // parse_entity_lookup in the migration (idc_migrate) can later import the material.
        //
        // `<entity_type>:<bundle>:<value_key>:<value>`
        //
        // This assumes that `<entity_type>` is the default of `taxonomy_term` and
        // that the `<value_key>` is `name`, so they are not included here.
        //
        $the_value = self::delimiter . $term->bundle() . self::delimiter . self::delimiter . $string_value;
        $elements[$delta]['#markup'] = $item->rel_type . ';' . $the_value;

        if (array_key_exists("#plain_text", $elements[$delta])) {
          unset($elements[$delta]["#plain_text"]);
        }
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
