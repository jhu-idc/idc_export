<?php

namespace Drupal\idc_export\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;

/**
 * Plugin implementation of the "entity_reference_csv" formatter.
 *
 * @FieldFormatter(
 *   id = "entity_reference_csv",
 * )
 */
class EntityReferenceCSVFormatter extends EntityReferenceLabelFormatter {
  /**
   * The delimiter used to separate fields in the formatting of the value.
   */
  protected const DELIMITER = ':';

  /**
   * The name of the field on the entity to pull the value from.
   */
  protected const VALUE_FIELD = 'field_unique_id';

  /**
   * Encode the provided string. If the DELIMITER is present in the string, this function will encode any uses of in into it's proper urlencoded form.
   *
   * @param string $DELIMITER
   *   The DELIMITER used to separate the fields.
   * @param string $string
   *   A string that may contain the $DELIMITER.
   *
   * @return string
   *   The encoded string.
   */
  // phpcs:ignore
  protected function encode(string $DELIMITER, string $string): string {
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
