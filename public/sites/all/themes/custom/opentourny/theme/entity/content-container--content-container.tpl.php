<?php

/**
 * @file
 * Default theme implementation for entities.
 *
 * Available variables:
 * - $content: An array of comment items. Use render($content) to print them all, or
 *   print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $title: The (sanitized) entity label.
 * - $url: Direct url of the current entity if specified.
 * - $page: Flag for the full page state.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. By default the following classes are available, where
 *   the parts enclosed by {} are replaced by the appropriate values:
 *   - entity-{ENTITY_TYPE}
 *   - {ENTITY_TYPE}-{BUNDLE}
 *
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess()
 * @see template_preprocess_entity()
 * @see template_process()
 */
?>
<?php if($background_url):?>
  <style>
    #<?=$attributes_array['id']?>{
      background: url('<?=$background_url?>') right bottom no-repeat transparent;
      filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=$background_url?>', sizingMethod='scale');
      -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=$background_url?>', sizingMethod='scale')";
      background-size:cover;
    }
  </style>
<?php endif;?>
<div class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>
  <?php print render($title_prefix); ?>
  <h4><?=$title?></h4>
  <?php print render($title_suffix); ?>
  <div class="content row"<?php print $content_attributes; ?>>
    <?php
      hide($content['field_cc_link']);
      hide($content['field_cc_link_mobile']);
      hide($content['field_alt_title']);
      print render($content);
    ?>
    <?=render($content['field_cc_link']);?>
  </div>
</div>
