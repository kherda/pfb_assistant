<?php

namespace Drupal\pfb_assistant\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'Example: configurable text string' block.
 *
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "gam_ad_units",
 *   admin_label = @Translation("GAM - Ad Unit")
 * )
 */
class GAM_Ad_Units extends BlockBase {

  /**
   * {@inheritdoc}
   *
   * This method sets the block default configuration. This configuration
   * determines the block's behavior when a block is initially placed in a
   * region. Default values for the block configuration form should be added to
   * the configuration array. System default configurations are assembled in
   * BlockBase::__construct() e.g. cache setting and block title visibility.
   *
   * @see \Drupal\block\BlockBase::__construct()
   */
  // public function defaultConfiguration() {
  //   return [
  //     'block_example_string' => $this->t('A default value. This block was created at %time', ['%time' => date('c')]),
  //   ];
  // }

  /**
   * {@inheritdoc}
   *
   * This method defines form elements for custom block configuration. Standard
   * block configuration fields are added by BlockBase::buildConfigurationForm()
   * (block title and title visibility) and BlockFormController::form() (block
   * visibility settings).
   *
   * @see \Drupal\block\BlockBase::buildConfigurationForm()
   * @see \Drupal\block\BlockFormController::form()
   */
  public function blockForm($form, FormStateInterface $form_state) {

    $form['div_id_target'] = [
      '#type' => 'textfield',
      '#title' => $this->t('DIV ID / Target'),
      '#description' => $this->t('This is the selector GAM will target the ad unit to.'),
      '#default_value' => isset($this->configuration['div_id_target']) ? $this->configuration['div_id_target'] : NULL,
      '#required' => TRUE,
    ];

    //@todo: API to get all the ad units from PFB?

    $form['ad_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Ad Type'),
      '#description' => $this->t('This gives the rough dimensions for the ad, which is intended to stop page shifting.  This does not have to be exact, but close.'),
      '#options' => [
        'leaderboard' => 'Leaderboard [728x90]',
        'hero' => 'Hero [1600x450]',
        'boombox' => 'Boombox [300x250]',
        'mini-boom' => 'Mini Boombox [300x100]',
        'mico-boom' => 'Mini Boombox [300x50]',
        'sticky_footer' => 'Sticky Footer [728x90]',
        'interstitial' => 'Interstitial [600x400]',
      ],
      '#default_value' => isset($this->configuration['ad_type']) ? $this->configuration['ad_type'] : NULL,
      '#required' => TRUE,
    ];

    $form['parallax'] = [
      '#type' => 'checkbox',
      '#title' => t('Parallax'),
      '#description' => t('This allows the website to scroll over the Hero ad unit'),
      '#states' => [
        'visible' => [
          ':input[name="settings[ad_type]"]' => ['value' => 'hero'],
        ],
      ],
      '#default_value' => isset($this->configuration['parallax']) ? $this->configuration['parallax'] : 0,
    ];

    $form['interstitial_timer'] = [
      '#type' => 'number',
      '#title' => t('Interstitial Timer (seconds)'),
      '#description' => t('A countdown timer will be shown and will close the interstitial on 0'),
      '#states' => [
        'visible' => [
          ':input[name="settings[ad_type]"]' => ['value' => 'interstitial'],
        ],
      ],
      '#default_value' => isset($this->configuration['interstitial_timer']) ? $this->configuration['interstitial_timer'] : 10,
    ];

    $form['hide_by_default'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Hide by default'),
      '#description' => $this->t('Hide the ad unit by default.  It will not show unless GAM slotOnload completed.  If not set, a preloader animation will show in its place.'),
      '#default_value' => isset($this->configuration['hide_by_default']) ? $this->configuration['hide_by_default'] : 0,
    ];
    $form['show_label'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show Advertisment Label'),
      '#description' => $this->t('This is the label that will show the word "Advertisement" above the ad unit.'),
      '#options' => [
        0 => 'Hide',
        1 => 'Yes',
      ],
      '#default_value' => isset($this->configuration['show_label']) ? $this->configuration['show_label'] : 0,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * This method processes the blockForm() form fields when the block
   * configuration form is submitted.
   *
   * The blockValidate() method can be used to validate the form submission.
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['div_id_target'] = $form_state->getValue('div_id_target');
    $this->configuration['ad_type'] = $form_state->getValue('ad_type');
    $this->configuration['hide_by_default'] = $form_state->getValue('hide_by_default');
    $this->configuration['show_label'] = $form_state->getValue('show_label');
    $this->configuration['parallax'] = $form_state->getValue('parallax');
    $this->configuration['interstitial_timer'] = $form_state->getValue('interstitial_timer');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    $additional_block_classes = NULL;
    if (!empty($this->configuration['parallax']) && $this->configuration['parallax'] == 1) {
      $additional_block_classes = 'parallax';
    }

    $interstitial_timer = 10;
    $interstitial_text = NULL;
    if (!empty($this->configuration['interstitial_timer']) && $this->configuration['interstitial_timer'] > 0) {
      $interstitial_timer = $this->configuration['interstitial_timer'];
      $interstitial_text = 'Closing in <span>' . $interstitial_timer . '</span> seconds';
    }

    // The target is required.
    if (empty($this->configuration['div_id_target'])) {
      return;
    }
    $target = $this->configuration['div_id_target'];

    if (empty($this->configuration['ad_type'])) {
      return;
    }
    $ad_type = $this->configuration['ad_type'];

    $label = NULL;
    if ($this->configuration['show_label']) {
      $label = 'advertisement';
    }

    $hide = NULL;
    if ($this->configuration['hide_by_default']) {
      $hide = 'hide ';
    }


    if ($ad_type == 'sticky_footer') {
      $markup = '
      <div class="gam-ad-unit ' . $ad_type . '">
        <div class="offcanvas offcanvas-bottom gam-offcanvas-stickyfooter" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1" id="' . $target . '-Sticky" aria-labelledby="offcanvasScrollingLabel">

          <div class="offcanvas-body container">
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            ' . $label . '
            <div class="gam-advertising sticky-footer" id="' . $target . '"></div>

          </div>
        </div>
      </div>';
    }
    elseif($ad_type == 'interstitial') {

      $markup = '
      <div class="gam-ad-unit ' . $ad_type . '">
        <div class="modal fade" id="' . $target . '-Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="false">
          <div class="modal-dialog modal-dialog-centered modal-interstitial-md modal-lg">

            <div class="modal-content">

              <div class="modal-header">
                  <div id="pfb-gam-interstitial-timer" data-timer="' . $interstitial_timer . '">' . $interstitial_text . '</div>
                  <div id="gam-placement-text">' . $label . '</div>
                  <div id="pfb-gam-close-button">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
              </div>

              <div class="modal-body">
                <div class="gam-advertising interstitial" id="' . $target . '"></div>
              </div>
            </div>
          </div>
        </div>
      </div>';

    }
    else{

      $markup = '
      <div class="gam-ad-unit ' . $ad_type . ' gam-card ' . $hide . '">
        ' . $label . '
        <div class="gam-card-placeholder gam-loader-animation" id="' . $target . '"></div>
      </div>
      ';
    }

    $build = [];
    $build['#cache']['max-age'] = 0;
    $build['#markup'] = t($markup);
    $build['#attached']['library'][] = 'pfb_assistant/gam';

    if ($additional_block_classes) {
      $build['#attributes']['class'][] = $additional_block_classes;
    }

    return $build;
  }

}
