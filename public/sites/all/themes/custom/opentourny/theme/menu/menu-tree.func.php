<?php
/**
 * @file
 * menu-tree.func.php
 */

/**
 * Overrides theme_menu_tree().
 */
function opentourny_menu_tree(&$variables) {
  return '<ul class="menu">' . $variables['tree'] . '</ul>';
}