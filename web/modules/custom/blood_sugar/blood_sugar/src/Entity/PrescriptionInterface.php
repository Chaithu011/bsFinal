<?php

namespace Drupal\blood_sugar\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface defining a adding an priscription entity.
 *
 * @ingroup priscription
 */
interface PrescriptionInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface {
}
