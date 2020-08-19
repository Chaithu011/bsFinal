<?php
namespace Drupal\blood_sugar\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\blood_sugar\Entity\BloodSugarRecord;


/**
 * Provides a block with a simple text.
 *
 * @Block(
 *   id = "add_blood_sugar_record_form_block",
 *   admin_label = @Translation("Add Blood Sugar Record Block"),
 * )
 */
class AddBloodSugarRecordBlock extends BlockBase {
  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity = BloodSugarRecord::create();
    $user_form = \Drupal::service('entity.form_builder')->getForm($entity, 'default');
        return $user_form;
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }
}