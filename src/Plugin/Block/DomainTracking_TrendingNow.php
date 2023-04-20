<?php

namespace Drupal\pfb_assistant\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\Core\Database\Database;

/**
 * Provides a 'Example: configurable text string' block.
 *
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "domain_tracking_trending_now",
 *   admin_label = @Translation("Domain Tracking: Trending Now")
 * )
 */
class DomainTracking_TrendingNow extends BlockBase {

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

    $form['start'] = [
      '#type' => 'select',
      '#title' => $this->t('Start Time'),
      '#description' => $this->t('The time trending will start the query'),
      '#options' => [
        60 * 5 => '-5 Minutes',
        60 * 60 => '-1 Hour',
        60 * 60 * 2 => '-2 Hours',
        60 * 60 * 6 => '-6 Hours',
        60 * 60 * 12 => '-12 Hours',
        60 * 60 * 24 => '-24 Hours',
        60 * 60 * 24 * 2 => '-2 days',
      ],
      '#default_value' => isset($this->configuration['start']) ? $this->configuration['start'] : DrupalDateTime::createFromTimestamp(time()),
    ];

    // $form['end'] = [
    //   '#title' => $this->t('End Time'),
    //   '#description' => $this->t('When we will stop the query from searching'),
    //   '#type' => 'select',
    //   '#options' => [
    //     0 => 'Now',
    //     60 * 5 => '-5 Minutes',
    //     60 * 60 => '-1 Hour',
    //     60 * 60 * 2 => '-2 Hours',
    //     60 * 60 * 6 => '-6 Hours',
    //     60 * 60 * 12 => '-12 Hours',
    //     60 * 60 * 24 => '-24 Hours',
    //     60 * 60 * 24 * 2 => '-2 days',
    //   ],
    //   '#default_value' => isset($this->configuration['end']) ? $this->configuration['end'] : DrupalDateTime::createFromTimestamp(time()),
    // ];

    // $form['expires'] = [
    //   '#type' => 'select',
    //   '#title' => $this->t('Expires'),
    //   '#description' => $this->t('when the query results will expire.  Note:  Upon expiration, new results with the start and end settings will execute again.'),
    //   '#options' => [
    //     60 => '1 Minute',
    //     60 * 30 => '30 Minutes',
    //     60 * 60 => '1 Hour',
    //     60 * 60 * 2 => '2 Hours',
    //     60 * 60 * 6 => '6 Hours',
    //     60 * 60 * 12 => '12 Hours',
    //     60 * 60 * 24 => '24 Hours',
    //   ],
    //   '#default_value' => isset($this->configuration['expires']) ? $this->configuration['expires'] : DrupalDateTime::createFromTimestamp(time()),
    // ];

    // $form['limit'] = [
    //   '#title' => $this->t('Limit Results'),
    //   '#type' => 'select',
    //   '#options' => [
    //     1 => 1,
    //     2 => 2,
    //     3 => 3,
    //     4 => 4,
    //     5 => 5,
    //     6 => 6,
    //     7 => 7,
    //     8 => 8,
    //     9 => 9,
    //     10 => 10,
    //   ],
    //   '#default_value' => isset($this->configuration['limit']) ? $this->configuration['limit'] : 3,
    // ];

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
    $this->configuration['start'] = $form_state->getValue('start');
    // $this->configuration['block_example_string'] = $form_state->getValue('block_example_string_text');
    // $this->configuration['end'] = $form_state->getValue('end');
    // $this->configuration['expires'] = $form_state->getValue('expires');
    // $this->configuration['limit'] = $form_state->getValue('limit');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {

    // $db = Database::getConnection();

    // // @todo:  start cannot be before end.  Maybe a form validation error.
    // $start = time() - $this->configuration['start'];
    // $end = time() - $this->configuration['end'];
    // $expires = time() + $this->configuration['expires'];
    // $limit = $this->configuration['limit'];

    // $domain_uuid = \Drupal::config('pfb_assistant.settings')->get('domain_uuid');
    // $domain = \Drupal::config('pfb_assistant.settings')->get('domain');

    // // Items for the rendered output.
    // $items = [];
    // $json = [];
    // $nids = [];

    // // Note:  this API was whitelisted in pfb eventsubscriber.
    // $url = $domain . '/api/v1/domain-tracking/trending-content.json?domain_uuid=' . $domain_uuid . '&start=' . $start . '&end=' . $end . '&expires=' . $expires;

    // // /*

    // Library with new JS file
    // Return the response
    // Send response out to twig
    // preprocess twig to get more information from the nid





      // Disable, or catch all exceptions, incase PFB endpoint is down, we don't want to crash the site.
    // $response = \Drupal::httpClient()->get($url, [
    //   'http_errors' => false,
    //   'headers' => ['Accept' => 'text/json'],
    //   'verify' => stristr($_SERVER['HTTP_HOST'], '.test') ? FALSE : TRUE,
    // ]);
    // $data = (string) $response->getBody();
    // $json = json_decode($data, TRUE);

    // if (!isset($json['obj'])) {
    //   $items[] = 'Pending...';
    //   //\Drupal::logger('pfb_domain_tracking')->error('404 Error in Trending Now');
    // }
    // else{
    //   foreach (@$json['obj'] as $obj) {

    //     // Note: PFB only has the alias' from the site.  We can attempt to dig up the node titles to make this more friendly.
    //     // @todo:  ship this data to the theme layer or views, instead of drawing up the title here.
    //     $node = $db->query(
    //       "SELECT n.nid, n.title
    //       FROM node_field_data n
    //       INNER JOIN path_alias p ON CONCAT('/node/', n.nid) = p.path
    //       AND p.alias = :uri",
    //      [':uri' => $obj['uri']])->fetchObject();

    //     if (is_object($node)) {
    //       $link = Link::fromTextAndUrl($node->title, Url::fromUri('internal:' . $obj['uri']))->toString();
    //       $items[] = $link;
    //       $nids[] = $node->nid;
    //     }
    //   }
    // }

    // $items = array_slice($items, 0, $limit);



    //  */

    $build = [];
    $build['#cache']['max-age'] = 0;
    $build['#markup'] = t('123123');

    $computed_settings = [
      'foo' => 'bar',
      'baz' => 'qux',
    ];

    $build['#attached']['library'][] = 'pfb_assistant/domain_tracking_trending_now';
    $build['#attached']['drupalSettings']['trending_now']['config'] = $computed_settings;


    return [
      '#theme' => 'custom_bloc',
      '#someVariable' => 'name',
      '#attached' => [
        'library' => [
          'pfb_assistant/domain_tracking_trending_now',
        ],
        'drupalSettings' => [
          'name' => 'kevin'
        ]
      ],
    ];

    //$build['#attached']['library'][] = 'pfb_assistant/gam';

    return $build;
  }

}
