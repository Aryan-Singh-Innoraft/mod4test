<?php

namespace Drupal\related_blogs_custom\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;

/**
 * Provides a related blogs block.
 *
 * @Block(
 *   id = "related_blogs_custom_related_blogs",
 *   admin_label = @Translation("Related Blogs"),
 *   category = @Translation("Custom"),
 * )
 */
class RelatedBlogsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');
    $nid = $node instanceof Node ? $node->id() : 0;
    $storage = \Drupal::entityTypeManager()->getStorage('node');
    $query = $storage->getQuery()->accessCheck(FALSE);
    $query->condition('type', 'blog');
    $query->condition('nid', $nid);
    $nids = $query->execute();
    $node = $storage->load(reset($nids));
    $items = [];
    if ($node) {
      $author_id = $node->getOwner()->id();
      $query = $storage->getQuery()->accessCheck(FALSE);
      $query->condition('type', 'blog');
      $query->condition('uid', $author_id);
      $query->condition('nid', $node->id(), '!=');
      $query->accessCheck(FALSE);
      $query->sort('field_like', 'DESC');
      $query->range(0, 3);
      $nids = $query->execute();
      $nodes = $storage->loadMultiple($nids);

      foreach ($nodes as $node) {
        $items[] = [
          'title' => [
            '#type' => 'link',
            '#title' => $node->label(),
            '#url' => $node->toUrl('canonical'),
          ],
        ];
      }

    }
    return $items;
  }

}
