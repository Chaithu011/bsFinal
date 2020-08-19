<?php

namespace Drupal\blood_sugar;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Blood sugar record entities.
 *
 * @ingroup blood_sugar_record
 */
class BloodSugarRecordListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Blood sugar record ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\blood_sugar\Entity\BloodSugarRecord $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.blood_sugar_record.edit_form',
      ['blood_sugar_record' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
