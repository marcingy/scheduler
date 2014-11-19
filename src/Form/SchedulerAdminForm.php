<?php

/**
 * @file
 * Contains \Drupal\scheduler\Form\SchedulerAdminForm.
 */

namespace Drupal\scheduler\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure scheduler settings for this site.
 */
class SchedulerAdminForm extends ConfigFormBase {

  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The url generator.
   *
   * @var \Drupal\Core\Routing\UrlGeneratorInterface
   */
  protected $urlGenerator;

  /**
   * Constructs a SiteInformationForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter
   * @param \Drupal\Core\Routing\UrlGeneratorInterface $url_generator
   *   The url generator.
   */
  public function __construct(ConfigFactoryInterface $config_factory, DateFormatter $date_formatter, UrlGeneratorInterface $url_generator) {
    parent::__construct($config_factory);

    $this->dateFormatter = $date_formatter;
    $this->urlGenerator = $url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('date.formatter'),
      $container->get('url_generator')
    );
  }

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
    $date_format = $config->get('date_format', 'Y-m-d H:i:s');
    $form['scheduler_date_format'] = array(
      '#type' => 'textfield',
      '#title' => t('Date format'),
      '#default_value' => $date_format,
      '#size' => 20,
      '#maxlength' => 20,
      '#required' => TRUE,
      '#field_suffix' => ' <small>' . $this->t('Example: %date', array('%date' => $this->dateFormatter->format(REQUEST_TIME, 'custom', $date_format))) . '</small>',
      '#description' => $this->t('The format displayed to the user when displaying publication time. See <a href="!url">the PHP date() function</a> for more details.', array(
        '!url' => $this->urlGenerator->generateFromPath('http://www.php.net/manual/en/function.date.php')
      )),
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
