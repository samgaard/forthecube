<?php

/**
 * @file
 * The PHP page that serves all page requests on a Drupal installation.
 *
 * The routines here dispatch control to the appropriate handler, which then
 * prints the appropriate page.
 *
 * All Drupal code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt.
 */

/**
 * Root directory of Drupal installation.
 */
define('DRUPAL_ROOT', getcwd());

require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);


return mtg_helper_card_importer(array("Masters Edition III"));
/*
	$query = new EntityFieldQuery();
	
	$query->entityCondition('entity_type', 'node')
	  ->entityCondition('bundle', 'card')
		->fieldCondition('field_set', 'tid', '1019', '=');
	
	$result = $query->execute();
		print_r(count($result['node']));
		exit();
		/*
	if (!empty($entities['node'])) {
		
		
		$nid = current($entities['node'])->nid;
		if($nid != '' && is_numeric($nid)) {
			$node = node_load($nid);
			
		}
	}

/* 

//print_r(node_load(13814));

$node = node_load(13834);
$last_comment_id = advanced_forum_last_post_in_topic($node->nid);
$comment_stats = comment_load($last_comment_id);

$last_page = advanced_forum_get_last_page($node);
if ($last_page > 0) {
  $query = array('page' => $last_page);
}
print l($node->title, 'node/' . $node->nid, array('query' => (empty($query) ? array() : $query), 'fragment' => ($last_comment_id ? 'comment-' . $last_comment_id : ''))) . '<br />' . theme('username', array('account' => user_load($comment_stats->uid))) . '  ' . mtg_helper_time_ago($comment_stats->changed) . ' ago';

$cube_node = node_load(13840);
$nids = array();
foreach($cube_node->field_main_deck[$cube_node->language] as $node_ref) {
	$nids[] = $node_ref['value'];
}
print_r(implode('+', $nids));

$entity = entity_load_single('field_collection_item', 28545);

print_r($entity);

print draft_system_seats_viewer(15465);

//
$scrape = drupal_http_request("http://games.crossfit.com/scores/leaderboard.php?stage=0&sort=0&division=101&region=17&numberperpage=100&page=0&competition=1&frontpage=0&expanded=0&full=0&year=14&showtoggles=0&hidedropdowns=1&showathleteac=0&athletename=&fittest=1&fitSelect=17&scaled=0");

$dom = new DOMDocument;
$dom->loadHTML($scrape->data);
$rows = array();
foreach($dom->getElementsByTagName('tr') as $tr) {
	$cells = array();
	foreach($tr->getElementsByTagName('td') as $td) {
		$cells[] = $td->nodeValue;
	}
	$rows[] = $cells;
}

print theme('table', array('rows' => $rows));
print_r(node_load(15538));

$seats_query = new EntityFieldQuery();
$seats_query
 ->entityCondition('entity_type', 'node')
 ->entityCondition('bundle', 'draft_seat')
 ->propertyCondition('uid', 1)
 ->fieldCondition('field_draft_reference', 'nid', 15984, '=');
$seats_results = $seats_query->execute();	
if (isset($seats_results['node'])) {
	foreach($seats_results['node'] as $seats_results_node) {
		if(isset($seats_results_node->nid)) {
      $seat_node = node_load($seats_results_node->nid);
      if(count($seat_node->field_seat_picks['und']) != 45) {
        print 'true';
      } else {
				print 'false';
			}
    }
  }
}
*/

?>
