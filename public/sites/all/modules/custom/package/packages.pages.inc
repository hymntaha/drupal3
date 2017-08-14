<?php

   function packages_listing_page() {

      //$nodes  = node_load_multiple($nids);
      $nodes = package_get_packages();

      $vocab = taxonomy_vocabulary_machine_name_load('package_type');

      $taxonomy_terms = taxonomy_get_tree($vocab->vid,0,null,true);
      foreach($taxonomy_terms as $term) {

         $packages[$term->tid]['term'] = taxonomy_term_view($term);

         foreach ($nodes as $node) {

            $package_type = field_get_items('node',$node, 'field_package_type');
            if ($package_type[0]['tid'] === $term->tid) {

               $packages[$term->tid]['packages'][] = node_view($node,'teaser');

            }

         }

      }
      return array(
         '#theme'    => 'package_listing',
         '#packages' => $packages,
      );

   }

   function packages_ajax_ticket_table_callback($package) {
      echo theme('package_ticket_table', array('package' => $package));
      drupal_exit();
   }

