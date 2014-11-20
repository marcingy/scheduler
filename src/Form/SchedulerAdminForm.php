<?php

/**
 * @file
 * Contains \Drupal\scheduler\Form\SchedulerAdminForm.
 */

namespace Drupal\scheduler\Form;

use Drupal\Core\Datetime\Entity\DateFormat;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Configure scheduler settings for this site.
 */
class SchedulerAdminForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'scheduler_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory()->get('scheduler.settings');
    $options = [];
    foreach (DateFormat::loadMultiple() as $name => $data) {
      $options[$name] = $this->t('@name - [@format]', ['@name' => $name, '@format' => $data->getPattern()]);
    }
    $form['scheduler_date_format'] = array(
      '#type' => 'select',
      '#title' => t('Date format'),
      '#default_value' => $config->get('date_format', 'html_datetime'),
      '#options' => $options,
      '#required' => TRUE,
    );

    $form['scheduler_extra_info'] = array(
      '#type' => 'textarea',
      '#title' => t('Extra Info'),
      '#default_value' => $config->get('extra_info', ''),
      '#description' => t('The text entered into this field will be displayed above the scheduling fields in the node edit form.'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('scheduler.settings')
      ->set('extra_info', $form_state->getValue('scheduler_extra_info'))
      ->set('date_format', $form_state->getValue('scheduler_date_format'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
