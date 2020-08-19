<?php

namespace Drupal\blood_sugar\Plugin\Block;

use Drupal\user\Entity\User;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a 'Admin List Menu' block.
 *
 * @Block(
 *   id = "admin_menu_block",
 *   admin_label = @Translation("Admin List Menu")
 * )
 */
class AdminListMenuBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $user = User::load(\Drupal::currentUser()->id());
    $current_user = \Drupal::currentUser();
    $roles = $current_user->getRoles();
    $link_option = [
      'attributes' => [
        'class' => [
          'use-ajax',
        ],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => '{"width":"70%"}'
      ],
    ];
    if ($user->hasPermission('manage all admin functions')) {
      $link_option['attributes']['class'][] = 'admin-operations-class';
      $url_list = [
          '/admin/people/create' => 'Create More Admins',
          '/admin/config/blood_sugar/settings' => 'Configure Settings',
        ];
    }
    if ($user->hasPermission('manage all user functions')) {
      $link_option['attributes']['class'][] = 'user-operations-class';
      $url_list = [
        '/dashboard/blood_sugar/add_prescription' => 'Add Prescription',
      ];
    }

    foreach ($url_list as $path => $title) {
      $url = Url::fromUserInput($path);
      // Check if the current user has access to this URL.
      // Add to array for the menu links only if the user has access.
      // This menu is cached by user by page. So should be good.
      if ($url->access()) {
        $url = Url::fromUserInput($path);
        $url->setOptions($link_option);
        $list[] = Link::fromTextAndUrl($title, $url);
      }
    }
    $output['admin_menu_block'] = [
      '#attributes' => [
        'class' => ['contextual-menu'],
        'id' => 'appeal-list-menus',
      ],
      '#theme' => 'item_list',
      '#items' => $list,
      '#cache' => [
        'contexts' => ['user', 'url.path'],
        'tags' => $user->getCacheTags(),
      ],
    ];
    return $output;
  }

}
