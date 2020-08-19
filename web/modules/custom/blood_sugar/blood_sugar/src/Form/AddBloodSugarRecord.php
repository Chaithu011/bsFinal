<?php

namespace Drupal\blood_sugar\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;



/**
 * Form controller for the blood_sugar_record entity edit forms.
 *
 * @ingroup blood_sugar_record
 */
class AddBloodSugarRecord extends ContentEntityForm {

  /**
   * The current user account.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
  */
  public function getFormId() {
    return 'add_blood_sugar_record';
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    $instance = parent::create($container);
    $instance->account = $container->get('current_user');
    $instance->database = $container->get('database');
    $instance->now = \Drupal::time()->getCurrentTime();
    $instance->isAdmin = in_array('administrator', $instance->account->getAccount()->getRoles());
    $instance->wait = 0;
    return $instance;
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\blood_sugar\Entity\BloodSugarRecord */
    $form = parent::buildForm($form, $form_state);
    $form['#attached']['library'][] = 'blood_sugar/blood_sugar_library';
    $form['blood_sugar_value']['widget']['0']['value']['#placeholder'] = 'Enter Blood Sugar Value';
    $form['actions']['submit']['#value'] = 'Save BS Value';
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $min_time_interval = \Drupal::config('blood_sugar.settings')->get('minimum_time_interval');
    // Converting to secs.
    $min_time_interval *= 60;
    $uid = \Drupal::currentUser()->id();
    $database = \Drupal::database();
    $last_record = $database->select('blood_sugar_record', 'bs')
      ->fields('bs', ['created'])
      ->condition('user_id', $uid)
      ->orderBy('bs.id', 'DESC')
      ->execute()->fetch()->created;
    if (($last_record + $min_time_interval) > $this->now) {
      $diff = $last_record + $min_time_interval - $this->now;
      $this->wait = $diff;
    }
    parent::validateForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function cancelSubmit($form, FormStateInterface $form_state) {

  }
  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    if ($this->wait == 0) {
      $status = parent::save($form, $form_state);
      $this->messenger()->addMessage($this->t('Successfully Submitted the BS value.'));
      $form_state->setRedirect('blood_sugar.user_dashboard');
    }
    else {
      $remaining = ($this->wait < 60) ? $this->wait . ' seconds' : intval($this->wait / 60) . ' minutes';
      $this->messenger()->addWarning($this->t('You have to wait %time for next entry.', [
        '%time' => $remaining,
      ]));
      $form_state->setRedirect('blood_sugar.user_dashboard');
    }
  }
}
