<?php

namespace Drupal\hello_world\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Drupal\Core\Url;

/**
 * Class HelloController.
 *
 * @package Drupal\hello_world\Controller
 */
class HelloController extends ControllerBase {
  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($nid) {
	  $node = Node::load($nid);
	  $url = Url::fromRoute('entity.node.canonical',['node'=>$nid]);
	  $internal_link = \Drupal::l(t('Link to the node'),$url);
    return [
      '#type' => 'markup',
      //'#markup' => $this->t('Hello @title!',['@title'=>$node->getTitle()])
	  '#markup' => $this->t('Hello @title!',['@title'=>$internal_link])
    ];
  }

}
