<?php

namespace Drupal\idc_export\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceLabelFormatter;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'LanguageValuePairCSVFormatter'.
 *
 * @FieldFormatter(
 *   id = "language_value_pair_csv",
 *   label = @Translation("Language Value Pair CSV Formatter"),
 *   field_types = {
 *     "language_value_pair"
 *   }
 * )
 */
class LanguageValuePairCSVFormatter extends EntityReferenceLabelFormatter {
  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = parent::viewElements($items, $langcode);

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {

      $elements[$delta] = array(
        '#markup' => $items[$delta]->value . ';' . $items[$delta]->entity->get('field_language_code')->getString()
      );
      if (array_key_exists("#plain_text", $elements[$delta])) {
        unset($elements[$delta]["#plain_text"]);
      }
    }
    return $elements;
  }
}
