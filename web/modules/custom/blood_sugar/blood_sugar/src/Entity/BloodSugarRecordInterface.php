<?php

namespace Drupal\blood_sugar\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a adding an blood_sugar_record entity.
 *
 * @ingroup blood_sugar_record
 */
interface BloodSugarRecordInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {
}
