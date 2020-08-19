<?php

namespace Drupal\blood_sugar\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;

/**
 * Form controller for Prescription edit forms.
 *
 * @ingroup prescription
 */
class AddPrescriptionForm extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    $instance->isAdmin = in_array('administrator', $instance->account->getAccount()->getRoles());
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\blood_sugar\Entity\Prescription $entity */
    $form = parent::buildForm($form, $form_state);
    // Hiding the user_id field for non-admin users.
    if (!$this->isAdmin) {
      $form['user_id']['#access'] = FALSE;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);
    $formfile = $form_state->getValue('prescription_doc')[0]['fids'][0];
    if ($formfile) {
      $oNewFile = File::load(($formfile));
      $file_size = $oNewFile->getSize();
      $file_size = number_format($file_size / 1048576, 2) . ' MB';
      $file_name = $oNewFile->getFilename();
      $entity = $this->entity;
      $entity->file_size = $file_size;
      $entity->file_name = $file_name;
      $entity->save();
    }
    $this->messenger()->addMessage($this->t('Successfully Submitted Prescription.'));
    $form_state->setRedirect('blood_sugar.user_dashboard');
  }

}
