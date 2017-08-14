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
 *\
 * Other variables:
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 *
 * @see template_preprocess()
 * @see template_preprocess_entity()
 * @see template_process()
 */
?>
<?php if($background_slideshow):?>
  <style>
      <?php foreach($content['field_slideshow_image']['#items'] as $delta => $item):?>
        #<?=$attributes_array['id']?> .slide-<?=$delta?>{
          background: url('<?=$item["image_url"]?>') center center no-repeat transparent;
          filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=$item["image_url"]?>', sizingMethod='scale');
          -ms-filter: "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=$item["image_url"]?>', sizingMethod='scale')";
        }
      <?php endforeach;?>
  </style>
<?php endif;?>

<div data-ride="carousel" class="<?php print $classes; ?> clearfix"<?php print $attributes; ?>>

  <?php print render($title_prefix); ?>
  <?php print render($title_suffix); ?>

  <?php if($display_indicators):?>
  <ol class="carousel-indicators">
    <?php foreach($content['field_slideshow_image']['#items'] as $delta => $item):?>
      <li data-target="#<?=$attributes_array['id']?>" data-slide-to="<?=$delta?>"<?=$delta == 0 ? ' class="active"' : ''?>></li>
    <?php endforeach;?>
  </ol>
  <?php endif;?>

  <div class="carousel-inner">
    <?php foreach($content['field_slideshow_image']['#items'] as $delta => $item):?>
      <div <?= isset($content['field_slideshow_link'][$delta]) && $content['field_slideshow_link'][$delta]['#element']['url'] != '#' ? 'data-href="'.render($content['field_slideshow_link'][$delta]).'"' : ''?> class="item slide-<?=$delta?><?=$delta == 0 ? ' active' : ''?>">
        <?=render($content['field_slideshow_image'][$delta]);?>
        <?=render($content['field_slideshow_promo'][$delta]);?>
        <?php if(isset($content['field_slideshow_caption'][$delta])):?>
          <div class="carousel-caption">
            <?=render($content['field_slideshow_caption'][$delta]);?>
          </div>
        <?php endif;?>
      </div>
    <?php endforeach;?>
  </div>

</div>
