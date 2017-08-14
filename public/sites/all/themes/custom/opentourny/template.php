<?php

require_once('theme/block/block.vars.php');

require_once('theme/entity/entity.vars.php');

require_once('theme/field/field.vars.php');

require_once('theme/menu/menu-link.func.php');
require_once('theme/menu/menu-tree.func.php');

require_once('theme/node/node.vars.php');
require_once('theme/system/breadcrumb.func.php');
require_once('theme/system/button.func.php');
require_once('theme/system/form-element.func.php');
require_once('theme/system/html.vars.php');
require_once('theme/system/page.vars.php');
require_once('theme/system/region.vars.php');

require_once('theme/process.inc');
require_once('theme/alter.inc');

function opentourny_call_now($variables){
  $output = '<img src="/'.drupal_get_path('theme', 'opentourny').'/images/ico-phone.png" />'.t('Call Now').': <span class="phone-number visible-xs">'.$variables['phone_number_mobile'].'</span><span class="phone-number hidden-xs">'.$variables['phone_number_large'].'</span>';
  $output .= '<span class="phone-number-small hidden-xs">('.$variables['phone_number_small'].')</span>';
  return $output;
}

function opentourny_feedback($variables){
  $output = '';
  $image = theme('image',array(
    'path' => drupal_get_path('theme', 'opentourny').'/images/btn-feedback.png',
    'attributes' => array('class' => array('img-responsive')),
  ));
  $output .= l($image,$variables['feedback_link'],array('html' => true, 'attributes' => array('target' => '_blank')));
  return $output;
}

function opentourny_add_this($variables){
  $output = '';
  $output .= '<div class="addthis_sharing_toolbox"></div>';
  $output .= '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-553fcfdc2e71afb5" async="async"></script>';
  return $output;
}

function opentourny_facebook_pixel($variables){
   $output = '';
   if($variables['enabled']){

         $output .= "
         !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
         n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
         n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
         t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
         document,'script','https://connect.facebook.net/en_US/fbevents.js');";

         $output .= "fbq('init', '1186223921390117');
                     fbq('track', 'PageView');";

     if($variables['start_checkout']){
       $output .= "fbq('track', 'InitiateCheckout');";
     }

     if($variables['start_payment']){
       $output .= "fbq('track', 'AddPaymentInfo');";
     }

     if($variables['order_total']){
       $output .= "fbq('track', 'Purchase', {value: '".$variables['order_total']."', currency: 'USD'});";
     }

         $output = "<script>" . $output . "</script>";
         $output .= '<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=1186223921390117&ev=PageView&noscript=1"/></noscript>';
   }
   return $output;
}

/**
Uncomment to have flags for the language selection.
Removed as per https://avatarnewyork.mydonedone.com/issuetracker/projects/22196/issues/107.

function opentourny_links__locale_block($variables) {
  $links = $variables['links'];
  $attributes = $variables['attributes'];
  $heading = $variables['heading'];
  global $language_url;
  $output = '';

  if (count($links) > 0) {
    $output = '';

    // Treat the heading first if it is present to prepend it to the
    // list of links.
    if (!empty($heading)) {
      if (is_string($heading)) {
        // Prepare the array that will be used when the passed heading
        // is a string.
        $heading = array(
          'text' => $heading,
          // Set the default level of the heading.
          'level' => 'h2',
        );
      }
      $output .= '<' . $heading['level'];
      if (!empty($heading['class'])) {
        $output .= drupal_attributes(array('class' => $heading['class']));
      }
      $output .= '>' . check_plain($heading['text']) . '</' . $heading['level'] . '>';
    }

    $output .= '<ul' . drupal_attributes($attributes) . '>';

    $num_links = count($links);
    $i = 1;

    foreach ($links as $key => $link) {
      $class = array($key);

      // Add first, last and active classes to the list of links to help out themers.
      if ($i == 1) {
        $class[] = 'first';
      }
      if ($i == $num_links) {
        $class[] = 'last';
      }
      if (isset($link['href']) && ($link['href'] == $_GET['q'] || ($link['href'] == '<front>' && drupal_is_front_page()))
          && (empty($link['language']) || $link['language']->language == $language_url->language)) {
        $class[] = 'active';
      }
      $output .= '<li' . drupal_attributes(array('class' => $class)) . '>';

      if (isset($link['href'])) {
        // Pass in $link as $options, they share the same keys.
        $link['html'] = TRUE;
        $output .= l(theme('image',array('path' => drupal_get_path('theme', 'opentourny').'/images/ico-flag-'.$key.'.png')), $link['href'], $link);
      }
      elseif (!empty($link['title'])) {
        // Some links are actually not links, but we wrap these in <span> for adding title and class attributes.
        if (empty($link['html'])) {
          $link['title'] = check_plain($link['title']);
        }
        $span_attributes = '';
        if (isset($link['attributes'])) {
          $span_attributes = drupal_attributes($link['attributes']);
        }
        $output .= '<span' . $span_attributes . '>' . $link['title'] . '</span>';
      }

      $i++;
      $output .= "</li>\n";
    }

    $output .= '</ul>';
  }

  return $output;
}
*/

function opentourny_package_booking_container($variables){
  $output = '';

  $output .= '<div class="booking-container otny-color-black otny-bg-grey">';

  $output .= '<div class="row">';

  $output .= '<div class="col-sm-6">';
  $output .= render($variables['booking_copy']);
  $output .= '</div>';

  $output .= '<div class="col-sm-6">';
  $output .= render($variables['ticket_form']);
  $output .= '</div>';

  $output .= '</div>';

  $output .= '</div>';

  return $output;
}

function opentourny_cart_checkout_header($variables) {
  $steps = array(
    1 => t('Select Your Tour'),
    2 => t('Your Details'),
    3 => t('Confirmation'),
  );

  $items = array();
  $output = array();

  foreach ($steps as $step_num => $step) {
    $classes = array('checkout-step');
    if ($variables['current_step'] == $step_num) {
      $classes[] = 'current-step';
    }
    $items[] = array(
      'data' => $step,
      'class' => $classes,
    );

    $output[] = '<div class="col-sm-4"><span class="' . implode(' ', $classes) . '">' . $step_num . '. ' . $step . '</span></div>';
  }

  $output = '<div class="hidden-xs row">'.implode('',$output).'</div>';
  $output .= '<div class="visible-xs">'.theme('item_list',array('items' => $items, 'type' => 'ol')).'</div>';

  return $output;
}
