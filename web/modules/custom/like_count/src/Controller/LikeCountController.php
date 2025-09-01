<?php

namespace Drupal\like_count\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns responses for Like count routes.
 */
class LikeCountController extends ControllerBase {

  /**
   * The entity manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
    );
  }

  /**
   * Builds the response.
   */
  public function add($node) {
    $storage = $this->entityTypeManager()->getStorage('node');
    $node_ids = $storage->getQuery()->accessCheck(FALSE)->condition('type', 'blog')
      ->condition('nid', $node)->execute();
    $node = $storage->load(reset($node_ids));
    $node->field_like = $node->get('field_like')->value + 1;
    $node->save();
    $this->messenger()->addMessage($node->label() . ' Liked');
    return $this->redirect('entity.node.canonical', ['node' => $node->id()]);
  }

}
