<?php
/**
 * @file
 * block.vars.php
 */

/**
 * Implements hook_preprocess_block().
 */
function opentourny_preprocess_block(&$variables) {
  switch ($variables['block_html_id']) {
    //Footer Content
    case 'block-block-1':
    case 'block-block-19':
    case 'block-block-20':
      $variables['classes_array'][] = 'col-md-3';
      break;
    case 'block-footer-footer-social':
      $variables['classes_array'][] = 'col-md-3';
      $variables['classes_array'][] = 'hidden-xs';
      break;
    case 'block-menu-menu-footer':
    case 'block-footer-footer-copyright':
    case 'block-block-12':
      $variables['classes_array'][] = 'col-xs-12';
      break;
    case 'block-opentourny-feedback':
      $variables['classes_array'][] = 'text-right';
      $variables['classes_array'][] = 'col-md-4';
      break;
    case 'block-opentourny-addthis':
      $variables['classes_array'][] = 'text-right';
      $variables['classes_array'][] = 'col-sm-6';
      $variables['classes_array'][] = 'col-md-4';
      $variables['classes_array'][] = 'hidden-xs';
      break;
   case 'block-review-reviews':
      $variables['classes_array'][] = 'text-center';
      if($node = menu_get_object()){
        if($node->type == 'homepage'){
          $variables['classes_array'][] = 'hidden-xs';
        }
      }
      break;
    case 'block-locale-language-content':
      $variables['classes_array'][] = 'text-right';
      $variables['classes_array'][] = 'col-xs-12';
      $variables['classes_array'][] = 'hidden-xs';
      $variables['content'] = l(t('Find Us'), variable_get('find_us_url', ''), array('attributes' => array('class' => array('find-us-link')))).
                              $variables['content'].
                              l('<img class="img-responsive" src="/'.drupal_get_path('theme', 'opentourny').'/images/logo-e.png" alt="Extrapolitan" />','https://www.extrapolitan.com',
                                array(
                                  'html' => TRUE,
                                  'attributes' => array(
                                    'target' => '_blank',
                                    'title' => 'Extrapolitan',
                                  )
                                ));
      break;
  }

  if ($variables['block_html_id'] == 'block-footer-footer-copyright') {
    $variables['content'] .= '<p class="avatar-design">' . l(t('New York web design'), 'http://avatarnewyork.com/company/website-design-development', array('attributes' => array('target' => '_blank'))) . ' ' . t('by @avatar', array('@avatar' => 'Avatar New York')) . '</p>';
  }
}

function opentourny_footer_social($variables) {
  global $language;

  $output       = '<h5>Connect with us</h5>';
  $options      = $variables['options'];
  $delta        = 1;
  $option_count = count($options);

  $output .= '<div class="social-links">';
  foreach ($options as $machine_name => $option) {
    $link = variable_get('footer_block_' . $machine_name, '');
    if ($link) {
      $class_name = str_replace('_', '-', $machine_name);
      $classes    = array('footer-social', $class_name);
      if ($delta == 1) {
        $classes[] = 'first';
      }
      else if ($delta == $option_count) {
        $classes[] = 'last';
      }
      $output .= l('<img src="/' . drupal_get_path('theme', 'opentourny') . '/images/ico-footer-' . $class_name . '.png" alt="' . $option . '" />', $link, array(
        'html'       => TRUE,
        'attributes' => array('target' => '_blank', 'class' => $classes),
      ));
      $delta++;
    }
  }
  $output .= '</div>';

  $output .= '<div class="promo-block">';
  $output .= variable_get('promo_block_'.$language->language);
  $output .= '</div>';
  return $output;
}
