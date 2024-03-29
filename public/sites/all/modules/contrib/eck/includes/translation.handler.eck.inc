<?php

/**
 * @file
 * ECK translation handler for the entity translation module.
 */

/**
 * ECK translation handler.
 *
 * Overrides default behaviours for ECK properties.
 */
class EntityTranslationECKHandler extends EntityTranslationDefaultHandler {
  public function __construct($entity_type, $entity_info, $entity) {
    parent::__construct($entity_type, $entity_info, $entity);

    // Since we are using different bundle paths then use the bundle's path scheme.
    $this->setPathScheme($entity->type);
  }
}
