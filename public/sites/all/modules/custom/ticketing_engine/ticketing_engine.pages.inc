<?php

/**
 * @param TicketingEngineTicket $ticket
 */
function ticketing_engine_ajax_tour_description_callback($ticket){
  $description = field_view_field('taxonomy_term', $ticket->getTour(), 'description_field', array('label' => 'hidden'));
  echo render($description);
  drupal_exit();
}