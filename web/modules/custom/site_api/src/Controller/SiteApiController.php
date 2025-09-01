<?php

namespace Drupal\site_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for Site api routes.
 */
final class SiteApiController extends ControllerBase {

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
  public function content(Request $request) {
    // Getting stored token from config.
    $config = $this->config('site_api.settings');
    $storedDate = $config->get('date');
    $storedTag = $config->get('tags');

    // Get all published nodes nid.
    $storage = $this->entityTypeManager()->getStorage('node');
    $nids = $storage->getQuery()->accessCheck(FALSE)->condition('status', 1)->condition('type', 'blog')->condition('field_published_date', $storedDate, '<=')->condition('field_blog_tags', $storedTag, '=')->execute();
    $nodes = $storage->loadMultiple($nids);
    $data = [];

    foreach ($nodes as $node) {
      $author = $node->getOwner();
      $data[] = [
        'title' => $node->label(),
        'body' => $node->get('body')->value,
        'tags' => $node->get('field_blog_tags')->target_id,
        'author' => $author->getDisplayName(),
        'published-date' => $node->get('field_published_date')->value,
      ];
    }
    return new JsonResponse($data);
  }

}
