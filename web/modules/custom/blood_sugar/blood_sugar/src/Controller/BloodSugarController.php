<?php
/**
 * @file
 * Contains \Drupal\blood_sugar\Controller\BloodSugarController.
 */
namespace Drupal\blood_sugar\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\User;

/**
 * {@inheritdoc}
 */
class BloodSugarController extends ControllerBase {

  /**
   * Manage the generation of blocks in the controller.
   *
   * @var Drupal\Core\Block\BlockManager
   */
  private $blockManager;

  /**
   * {@inheritdoc}
   */
  public function __construct() {
    $this->blockManager = \Drupal::service('plugin.manager.block');
  }


  /**
   * Function userDashboard.
   *
   * @return array
   *   Return user dashboard.
   */
  public function Dashboard() {   
    return [
      '#type' => 'markup',
      '#markup' => $this->t(''),
      '#attached' => [
        'library' => [
          'blood_sugar/blood_sugar_library',
        ],
      ],
    ];
  }
/**
 * @file
 * Contains \Drupal\blood_sugar\Controller\BloodSugarController.
 */
  public function userDashboard() {
    $render_array['add_blood_sugar_record_form_block'] = $this->addBlock('add_blood_sugar_record_form_block');
    $render_array['blood_sugar_level'] = views_embed_view('blood_sugar_level', 'block_1');
    $render_array['admin_menu_block'] = $this->addBlock('admin_menu_block');
    $render_array['prescription_list'] = views_embed_view('prescription_list', 'block_1');
    return $render_array;
  }

 /**
  * Admin dashoard.
  */
  public function adminDashboard() {
    $render_array['admin_menu_block'] = $this->addBlock('admin_menu_block');
    $render_array['user_list'] = views_embed_view('user_list', 'block_1');
    $render_array['blood_sugar_level'] = views_embed_view('blood_sugar_level', 'block_2');
    $render_array['prescription_list'] = views_embed_view('prescription_list', 'block_2');
    return $render_array;
  }

  /**
   * Return render array for the block to be added.
   */
  private function addBlock($block_id) {
    $config = [];
    $render = [];
    $plugin_block = $this->blockManager->createInstance($block_id, $config);
    // Some blocks might implement access check.
    $access_result = $plugin_block->access(\Drupal::currentUser());
    // Return empty render array if user doesn't have access.
    // $access_result can be boolean or an AccessResult class.
    if (is_object($access_result) && !$access_result->isForbidden() || is_bool($access_result) && $access_result) {
      // You might need to add some cache tags/contexts.
      $render = $plugin_block->build();
    }
    return $render;
  }
  /**
   * manage user for site admin (Extra added for further reference).
   */
  public function manageUser($id, Request $request, RouteMatchInterface $route_match) {
    $user = User::load($id);
    if ($user->isActive() == 0) {
      $user->set("status", 1);
      $user->activate();
      $this->messenger()->addMessage($this->t('User activated'));
    }
    else {
      $user->set("status", 0);
      $user->block();
      $this->messenger()->addMessage($this->t('User deactivated'));
    }
    $user->save();
    return new RedirectResponse(\Drupal::url('blood_sugar.admin_dashboard'));
  }
}