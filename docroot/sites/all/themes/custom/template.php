<?php

/**
 * @file
 * template.php
 */

/**
 * Theme function to display a link, optionally buttonized.
 */
function custom_advanced_forum_l(&$variables) {
  $text = $variables['text'];
  $path = empty($variables['path']) ? NULL : $variables['path'];
  $options = empty($variables['options']) ? array() : $variables['options'];
  $button_class = empty($variables['button_class']) ? NULL : $variables['button_class'];

  $l = '';
  if (!isset($options['attributes'])) {
    $options['attributes'] = array();
  }
  if (!is_null($button_class)) {
    // Buttonized link: add our button class and the span.
    if (!isset($options['attributes']['class'])) {
      $options['attributes']['class'] = array("af-button-$button_class");
    }
    else {
      $options['attributes']['class'][] = "af-button-$button_class";
    }
    $options['html'] = TRUE;
    $l = l('<span class="btn btn-primary">' . $text . '</span>', $path, $options);
  }
  else {
    // Standard link: just send it through l().
    $l = l($text, $path, $options);
  }

  return $l;
}

/**
* Implements hook_preprocess_node().
*/
function custom_preprocess_node(&$variables) {
  if (isset($variables['content']['links']['comment']['#links']['post-edit'])) {
    $variables['content']['links']['comment']['#links']['post-edit']['attributes']['class'] = "btn btn-default btn-xs";
  }
  if (isset($variables['content']['links']['comment']['#links']['post-delete'])) {
    $variables['content']['links']['comment']['#links']['post-delete']['attributes']['class'] = "btn btn-danger btn-xs";
  }
  if (isset($variables['content']['links']['comment']['#links']['comment-add'])) {
    $variables['content']['links']['comment']['#links']['comment-add']['attributes']['class'] = "btn btn-default btn-xs";
  }
}
/**
* Implements hook_preprocess_comment().
*/
function custom_preprocess_comment(&$variables) {
  if (isset($variables['content']['links']['comment']['#links']['comment-edit'])) {
    $variables['content']['links']['comment']['#links']['comment-edit']['attributes']['class'] = "btn btn-default btn-xs";
  }
  if (isset($variables['content']['links']['comment']['#links']['comment-delete'])) {
    $variables['content']['links']['comment']['#links']['comment-delete']['attributes']['class'] = "btn btn-danger btn-xs";
  }
  if (isset($variables['content']['links']['comment']['#links']['comment-reply'])) {
    $variables['content']['links']['comment']['#links']['comment-reply']['attributes']['class'] = "btn btn-default btn-xs";
  }
}

function custom_preprocess_html(&$vars) {
  global $user;
	$page_data = menu_get_object();
  if(isset($page_data->field_rochester) && $page_data->field_rochester['und'][0]['value'] == 1) {
    $vars['classes_array'][] = 'rochester';
  }
	$vars['classes_array'][] = 'uid-' . $user->uid;
}

function custom_preprocess_page(&$variables) {
  if (!empty($variables['node']) && !empty($variables['node']->type)) {
    $variables['theme_hook_suggestions'][] = 'page__node__' . $variables['node']->type;
  }
}

