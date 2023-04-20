<?php

namespace Drupal\pfb_assistant\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\image\Entity\ImageStyle;


/**
 * Opening class for the Access Controller.
 */
class API {

  /**
   * [__construct description].
   */
  public function __construct() {}

  /**
   * This is the Boomerand Endpoint.
   *
   * The primary purpose is to generate HTML for URI's.
   */
  public function boomerang() {

    $output = [];
    $uuid = $_GET['uuid'];
    $unread = json_decode($_GET['unread_content'], TRUE);
    $last_read_uri = $_GET['last_read_uri'];

    $settings_domain_uuid = \Drupal::config('pfb_assistant.settings')->get('domain_uuid');

    // Very simple check. Why not?
    if ($uuid != $settings_domain_uuid) {
      return new JsonResponse(['Error.']);
    }

    $markup = '<div id="boomerang-markup">';
    foreach ($unread as $obj) {

      if ($obj['uri']) {

        $data = self::fetchNodeInfoByURI($obj['uri']);

        if (is_object($data) && is_numeric($data->nid)) {

          $url= Url::fromRoute("entity.node.canonical", array('node' => $data->nid), array('absolute' => true));
          $link = [
            '#type' => 'link',
            '#url' => $url,
            '#title' => t($data->title)
          ];

          // if (isset($node->get('field_image')->get(0))) {
          //   echo "<pre>"; print_r('asdf'); die();
          // }

          //echo "<pre>"; print_r($node); die();

          $image = NULL;
          if ($data->uri) {
            $styled_image_url = ImageStyle::load('thumbnail')->buildUrl($data->uri);
            $image = '<img src="' . $styled_image_url . '" width="100" align="left" style="padding: 0 20px 20px" />';
          }

          $markup .= '
          <div class="boomerang-row" style="border-bottom: 1px solid #ccc; padding: 20px 0; min-height: 80px;">

            ' . $image . '
            <div class="boomerang-title" style="font-weight: bold">' . render($link) . '</div>
            <div class="boomerang-created">Created on: ' . \Drupal::service('date.formatter')->format($data->created, 'custom', 'F d, Y') . '</div>
          </div>';
        }
      }
    }

    $markup .= '</div>';

    // Load up the unread, formatted markup.
    $output['unread_articles_markup'] = $markup;

    //Optionally, let's include some information on the last article the user has read.  This might be useful in the email.
    if (strlen($last_read_uri) > 0) {

      $data = self::fetchNodeInfoByURI($last_read_uri);

      $url= Url::fromRoute("entity.node.canonical", array('node' => $data->nid), array('absolute' => true));
      $link = [
        '#type' => 'link',
        '#url' => $url,
        '#title' => t($data->title)
      ];

      $output['last_read_article_markup'] = render($link);
    }

    return new JsonResponse($output);
  }

  /**
   * Common function we will use for general theming.
   *
   * Someone else can utilize the entity query and all that if you want.
   */
  private function fetchNodeInfoByURI($uri) {

    $query = \Drupal::database()->query("
      SELECT node.nid, node.created, node.title, file.uri
      FROM path_alias a
      INNER JOIN node_field_data node ON node.nid = SUBSTRING(a.path,7)
      LEFT JOIN node__field_image image ON node.nid = image.entity_id AND image.delta = 0
      LEFT JOIN file_managed file ON image.field_image_target_id = file.fid
      WHERE a.alias = :uri
      ", [':uri' => $uri])->fetchObject();

    return $query;

  }

  /**
   * [__destruct description].
   */
  public function __destruct() {
  }

}
