<?php

namespace Drupal\blood_sugar\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SettingsForm.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'blood_sugar.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'settings_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('blood_sugar.settings');
    $form['minimum_time_interval'] = [
      '#type' => 'number',
      '#title' => $this->t('Minimum Time Interval'),
      '#description' => $this->t('The time interval between to BS entry (minutes)'),
      '#default_value' => $config->get('minimum_time_interval'),
      '#max' => 10,
      '#min' => 1,
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('blood_sugar.settings')
      ->set('minimum_time_interval', $form_state->getValue('minimum_time_interval'))
      ->save();
    $form_state->setRedirect('blood_sugar.admin_dashboard');
  }

}
