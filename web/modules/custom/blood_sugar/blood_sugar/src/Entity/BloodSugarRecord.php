<?php

namespace Drupal\blood_sugar\Entity;

/**
 * @file
 * Contains \Drupal\blood_sugar\Entity\blood_sugar_record.
 */
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;

/**
 * Defines the BloodSugarRecord entity.
 *
 * @ingroup blood_sugar_record
 *
 * @ContentEntityType(
 *   id = "blood_sugar_record",
 *   label = @Translation("BloodSugarRecord entity"),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "list_builder" = "Drupal\blood_sugar\BloodSugarRecordListBuilder",
 *     "form" = {
 *       "add" = "Drupal\blood_sugar\Form\AddBloodSugarRecord",
 *     },
 *     "form" = {
 *       "default" = "Drupal\blood_sugar\Form\AddBloodSugarRecord",
 *       "add" = "Drupal\blood_sugar\Form\BloodSugarRecordForm",
 *       "delete" = "Drupal\blood_sugar\Form\BloodSugarRecordDeleteForm",
 *       "edit" = "Drupal\blood_sugar\Form\AddBloodSugarRecord",
 *     },
 *     "access" = "Drupal\blood_sugar\BloodSugarRecordAccessControlHandler",
 *   },
 *   controllers = {
 *     "storage" = "Drupal\Core\Entity\DatabaseStorageController",
 *     "view_builder" = "Drupal\blood_sugar\MyViewBuilder"
 *   },
 *   base_table = "blood_sugar_record",
 *   admin_permission = "administer BloodSugarRecord entity",
 *   fieldable = FALSE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 * )
 */
class BloodSugarRecord extends ContentEntityBase implements BloodSugarRecordInterface {

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    $this->set('changed', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTimeAcrossTranslations() {
    $changed = $this->getUntranslated()->getChangedTime();
    foreach ($this->getTranslationLanguages(FALSE) as $language) {
      $translation_changed = $this->getTranslation($language->getId())
        ->getChangedTime();
      $changed = max($translation_changed, $changed);
    }
    return $changed;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    // Standard field, used as unique if primary index.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the BloodSugarRecord entity.'))
      ->setReadOnly(TRUE);
    // Standard field, unique outside of the scope of the current project.
    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the BloodSugarRecord entity.'))
      ->setReadOnly(TRUE);
    $fields['blood_sugar_value'] = BaseFieldDefinition::create('float')
      ->setSettings([
        'step' => 1,
        'min' => 0,
        'max' => 10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'settings' => [
          'display_label' => TRUE,
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'number_decimal',
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);
    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setSetting('target_type', 'user');
    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code of Organization entity.'));
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));
    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));
    return $fields;
  }

}
