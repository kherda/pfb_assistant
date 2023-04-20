<?php

namespace Drupal\pfb_assistant\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure example settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'pfb_assistant.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'pfb_assistant_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['domain_uuid'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain UUID'),
      '#default_value' => $config->get('domain_uuid'),
    ];
    $form['domain'] = array(
      '#type' => 'textfield',
      '#title' => t('Domain'),
      '#description' => t('Allows you to utilize development.  Prod = https://profilebuilder.app'),
      '#default_value' => !empty($config->get('domain')) ? $config->get('domain') : 'https://v4.profilebuilder.app',
    );

    $form['js_script_version'] = array(
      '#type' => 'number',
      '#step' => '.1',
      '#title' => t('Javascript Version'),
      '#description' => t('Gives you the ability to change versions (perhaps for testing)'),
      '#default_value' => $config->get('js_script_version') ? $config->get('js_script_version') : '4.2',
    );

    $form['ip_recheck'] = array(
      '#type' => 'select',
      '#options' => [
        'always' => 'Every pageload',
        60 => '1 minute',
        60 * 30 => '30 minutes',
        60 * 60 => '1 Hour',
        60 * 60 * 12 => '12 Hours',
        60 * 60 * 24 => '1 Day',
      ],
      '#title' => t('How often to recheck the clients IP Address?'),
      '#description' => t('Rechecking the users IP address on every pageload is not necessary.  However you may want to be more granular for precise targeting.'),
      '#default_value' => $config->get('ip_recheck') ? $config->get('ip_recheck') : 60 * 60,
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration.
    $this->configFactory->getEditable(static::SETTINGS)
      // Set the submitted configuration setting.
      ->set('domain_uuid', $form_state->getValue('domain_uuid'))
      ->set('domain', $form_state->getValue('domain'))
      ->set('js_script_version', $form_state->getValue('js_script_version'))
      ->set('ip_recheck', $form_state->getValue('ip_recheck'))
      // You can set multiple configurations at once by making
      // multiple calls to set().
      ->save();

    parent::submitForm($form, $form_state);
  }

}
