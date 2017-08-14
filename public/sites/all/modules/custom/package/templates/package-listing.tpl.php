<?php foreach ($packages as $taxonomy_id => $term) {
      print render($term);
      foreach($term['packages'] as $package) {
         print render($package);
      }
   }
