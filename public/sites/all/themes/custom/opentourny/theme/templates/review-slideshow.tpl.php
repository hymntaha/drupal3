<div data-ride="carousel" data-interval="7500" class="<?php print $classes; ?> clearfix" <?php print $attributes; ?>>
   <div class="carousel-inner">
      <?php foreach ($reviews as $delta => $review):?>
         <div class="item slide-<?php echo $review['#node']->nid?> <?php echo $delta == 0 ? 'active' : '' ?>">
            <?php print render($review); ?>
         </div>
       <?php endforeach;?>
   </div>
</div>
