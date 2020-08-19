<?php

namespace Drupal\blood_sugar\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Prescription entity.
 *
 * @ingroup prescription
 *
 * @ContentEntityType(
 *   id = "prescription",
 *   label = @Translation("Prescription entity"),
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "form" = {
 *       "default" = "Drupal\blood_sugar\Form\AddPrescriptionForm",
 *       "add" = "Drupal\blood_sugar\Form\AddPrescriptionForm",
 *       "edit" = "Drupal\blood_sugar\Form\AddPrescriptionForm",
 *       "delete" = "Drupal\blood_sugar\Form\DeletePrescriptionForm",
 *     },
 *     "access" = "Drupal\blood_sugar\PrescriptionAccessControlHandler",
 *   },
 *   controllers = {
 *     "storage" = "Drupal\Core\Entity\DatabaseStorageController",
 *     "view_builder" = "Drupal\blood_sugar\MyViewBuilder"
 *   },
 *   base_table = "prescription",
 *   admin_permission = "administer Prescription entity",
 *   fieldable = FALSE,
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid"
 *   },
 * )
 */
class Prescription extends ContentEntityBase implements PrescriptionInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
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
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
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
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
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
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Prescription entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'hidden',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['prescription_doc'] = BaseFieldDefinition::create('file')
      ->setLabel('Prescription file')
      ->setDescription(t('Only files with the following extensions are allowed: pdf, jpg, jpeg, png, doc, xls, txt, docx and size should be less than 2MB'))
      ->setSettings([
        'uri_scheme' => 'public',
        'file_directory' => 'PRESCRIPTIONS',
        'file_extensions' => 'pdf jpg jpeg png doc xls txt docx',
      ])
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'file',
        'weight' => 1,
      ])
      ->setDisplayOptions('form', [
        'type' => 'file',
        'weight' => 1,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['prescription_description'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Description'))
      ->setSettings([
        'default_value' => '',
        'max_length' => 2,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => 2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string',
        'weight' => 2,
      ])
      ->setRequired(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['file_name'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('File Name'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['file_size'] = BaseFieldDefinition::create('string')
      ->setLabel(t('File Size'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
