<?php

/**
 * @file
 * Default theme implementation to display a node.
 *
 * Available variables:
 * - $title: the (sanitized) title of the node.
 * - $content: An array of node items. Use render($content) to print them all,
 *   or print a subset such as render($content['field_example']). Use
 *   hide($content['field_example']) to temporarily suppress the printing of a
 *   given element.
 * - $user_picture: The node author's picture from user-picture.tpl.php.
 * - $date: Formatted creation date. Preprocess functions can reformat it by
 *   calling format_date() with the desired parameters on the $created
 *   variable.
 * - $name: Themed username of node author output from theme_username().
 * - $node_url: Direct url of the current node.
 * - $terms: the themed list of taxonomy term links output from theme_links().
 * - $display_submitted: whether submission information should be displayed.
 * - $classes: String of classes that can be used to style contextually through
 *   CSS. It can be manipulated through the variable $classes_array from
 *   preprocess functions. The default values can be one or more of the
 *   following:
 *   - node: The current template type, i.e., "theming hook".
 *   - node-[type]: The current node type. For example, if the node is a
 *     "Blog entry" it would result in "node-blog". Note that the machine
 *     name will often be in a short form of the human readable label.
 *   - node-teaser: Nodes in teaser form.
 *   - node-preview: Nodes in preview mode.
 *   The following are controlled through the node publishing options.
 *   - node-promoted: Nodes promoted to the front page.
 *   - node-sticky: Nodes ordered above other non-sticky nodes in teaser
 *   listings.
 *   - node-unpublished: Unpublished nodes visible only to administrators.
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 *
 * Other variables:
 * - $node: Full node object. Contains data that may not be safe.
 * - $type: Node type, i.e. story, page, blog, etc.
 * - $comment_count: Number of comments attached to the node.
 * - $uid: User ID of the node author.
 * - $created: Time the node was published formatted in Unix timestamp.
 * - $classes_array: Array of html class attribute values. It is flattened
 *   into a string within the variable $classes.
 * - $zebra: Outputs either "even" or "odd". Useful for zebra striping in
 *   teaser listings.
 * - $id: Position of the node. Increments each time it's output.
 *
 * Node status variables:
 * - $view_mode: View mode, e.g. 'full', 'teaser'...
 * - $teaser: Flag for the teaser state (shortcut for $view_mode == 'teaser').
 * - $page: Flag for the full page state.
 * - $promote: Flag for front page promotion state.
 * - $sticky: Flags for sticky post setting.
 * - $status: Flag for published status.
 * - $comment: State of comment settings for the node.
 * - $readmore: Flags true if the teaser content of the node cannot hold the
 *   main body content.
 * - $is_front: Flags true when presented in the front page.
 * - $logged_in: Flags true when the current user is a logged-in member.
 * - $is_admin: Flags true when the current user is an administrator.
 *
 * Field variables: for each field instance attached to the node a
 *   corresponding
 * variable is defined, e.g. $node->body becomes $body. When needing to access
 * a field's raw values, developers/themers are strongly encouraged to use
 *   these
 * variables. Otherwise they will have to explicitly specify the desired field
 * language, e.g. $node->body['en'], thus overriding any language negotiation
 * rule that was previously applied.
 *
 * @see template_preprocess()
 * @see template_preprocess_node()
 * @see template_process()
 */
global $user;
$player_uids = [];
foreach ($node->field_players['und'] as $player_info) {
  $player_uids[] = $player_info['uid'];
}
if (!in_array($user->uid, $player_uids)) {
  drupal_set_message("You are not in this draft.");
  drupal_goto('forum');
}

$picks_query = new EntityFieldQuery();
$picks_query
  ->entityCondition('entity_type', 'node')
  ->entityCondition('bundle', 'draft_seat')
  ->fieldCondition('field_draft_reference', 'nid', $node->nid, '=')
  ->propertyCondition('uid', $user->uid);
$picks_results = $picks_query->execute();
if (isset($picks_results['node'])) {
  foreach ($picks_results['node'] as $picks_results_node) {
    $picks_node = node_load($picks_results_node->nid);
  }
}

$pick_count = 0;
$picks = '';
$picks_delimeter = '';
if (isset($picks_node->field_seat_picks['und'])) {
  $pick_count = count($picks_node->field_seat_picks['und']);
  foreach ($picks_node->field_seat_picks['und'] as $pick_info) {
    $picks .= $picks_delimeter . $pick_info['nid'];
    $picks_delimeter = '+';
  }
}

$rochester = FALSE;
if (isset($node->field_rochester['und']) && $node->field_rochester['und'][0]['value'] == 1) {
  $rochester = TRUE;
}

$query = new EntityFieldQuery();
$query
  ->entityCondition('entity_type', 'node')
  ->entityCondition('bundle', 'draft_pack')
  ->fieldCondition('field_draft_reference', 'nid', $node->nid, '=')
  ->fieldOrderBy('field_pick_number', 'value', 'ASC')
  ->range(0, 1);
if (!$rochester) {
  $query->propertyCondition('uid', $user->uid);
}
$result = $query->execute();
$cards = '';
$delimeter = '';
if (isset($result['node'])) {
  foreach ($result['node'] as $result_node) {
    //watchdog('test', '$result_node->nid = ' . $result_node->nid);
    $pack_node = node_load($result_node->nid);
    if (isset($pack_node->field_card['und']) && !$rochester) {
      foreach ($pack_node->field_card['und'] as $card_info) {
        $cards .= $delimeter . $card_info['nid'];
        $delimeter = '+';
      }
    }
  }
}


$draft_finished = FALSE;
if ($pick_count == 45) {
  $draft_finished = TRUE;
}

if ($draft_finished) {
  print draft_system_picks_viewer($node, $user->uid);
}

$block = module_invoke('mtg_helper', 'block_view', 'mana_symbols');
$mana_symbols = '<div id="#block-mtg-helper-mana-symbols">' . render($block['content']) . '</div>';

if ($rochester) {
  $is_my_pick = ($pack_node->uid == $user->uid);
}
else {
  $is_my_pick = (isset($pack_node->field_pick_number['und']) && ($pick_count + 1) == $pack_node->field_pick_number['und'][0]['value']);
}

?>
<?php if (!$draft_finished && $rochester): ?>
    <div class="col-xs-12">
        <div class="col-xs-12 col-sm-2"><?php print draft_system_seats_viewer($node->nid); ?></div>
        <div class="col-xs-12 col-sm-2"><h2>Pick Count</h2>
            <div class="pick-count-wrapper"><?php print $pick_count; ?></div>
        </div>
    </div>
<?php endif; ?>

<article id="node-<?php print $node->nid; ?>"
         class="col-xs-12<?php print (!$draft_finished && !$rochester ? ' col-sm-10 ' : ' ');
         print $classes; ?>"<?php print $attributes; ?>>
    <div<?php echo(isset($pack_node->nid) ? ' id="pack-' . $pack_node->nid . '"' : ''); ?>
            class="pack-wrapper">
      <?php

      if ($rochester) {
          print '<h2>The Cube</h2>' . (isset($pack_node->nid) ? views_embed_view('cards', 'page_3', $pack_node->nid) : '')  . (!$is_my_pick ? '<div id="no-picks"><h2>Not currently your pick.</h2></div>' : '');
      }
      else {
          print ($is_my_pick ? '<h2>Your pack</h2>' . (isset($pack_node->nid) ? views_embed_view('draft_pack', 'default', $cards, $pack_node->nid, $node->nid) : '') : (!$draft_finished ? '<div id="no-picks"><h2>No packs for you!</h2></div>' : ''));
      }

      ?>

    </div>
    <div id="picks-wrapper">
      <?php print ($rochester ? draft_system_picks_viewer($node, $user->uid) : ($pick_count > 0 ? '<h2>Your picks</h2>' . views_embed_view('draft_pack', 'page_1', $picks) : '<h2>Your picks</h2>No picks yet.')); ?>
    </div>
</article>

<?php if (!$draft_finished && !$rochester): ?>
    <div class="col-xs-12 col-sm-2">
      <?php print draft_system_seats_viewer($node->nid); ?>
        <h2>Pick Count</h2>
        <div class="pick-count-wrapper"><?php print $pick_count; ?></div>
    </div>
<?php endif; ?>

<?php //print ($pick_count == 45 ? render($content['comments']) : ''); ?>
