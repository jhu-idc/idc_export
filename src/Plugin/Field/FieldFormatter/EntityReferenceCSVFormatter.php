<?php


namespace Drupal\idc_export\Plugin\Field\FieldFormatter;


use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;

class EntityReferenceCSVFormatter extends EntityReferenceLabelFormatter {

  /**
   * the delimiter used to separate fields in the formatting of the value
   */
  protected const delimiter = ':';

  /**
   * the name of the field on the entity to pull the value from
   */
  protected const value_field = 'field_unique_id';

  /**
   * Encode the provided string. If the delimiter is present in the string, this function will
   * encode any uses of in into it's proper urlencoded form.
   *
   * @param string $delimiter the delimiter used to separate the fields
   * @param string $string a string that may contain the $delimiter
   * @return string the encoded string
   */
  protected function encode(string $delimiter, string $string): string {
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
