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
        $string_value = (array_key_exists("#plain_text", $elements[$delta]) ?
          $elements[$delta]['#plain_text'] : $elements[$delta]["#title"]);

        // The markup needs to end up in the format shown below so that the
        // parse_entity_lookup in the migration (idc_migrate) can later import the material.
        //
        // `<entity_type>:<bundle>:<value_key>:<value>`
        // 
        // This assumes that `<entity_type>` is the default of `taxonomy_term` and
        // that the `<value_key>` is `name`, so they are not included here.
        //
        $the_value = ':' . $term->bundle() . '::' . $string_value;
        $elements[$delta]['#markup'] = $item->rel_type . ';' . $the_value;

        if (array_key_exists("#plain_text", $elements[$delta])) {
          unset($elements[$delta]["#plain_text"]);
        }
      }
    }
    return $elements;
  }
}
