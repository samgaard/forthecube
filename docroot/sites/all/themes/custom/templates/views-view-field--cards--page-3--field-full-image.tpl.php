<?php

global $user;
//draftpick(cardnid, packnid, draftnid, drafteruid

$pack_node = node_load($view->args[0]);
if ($pack_node->uid == $user->uid) {
  print '<a href="javascript:void(0);" onClick="if(confirm(\'Are you sure you want to draft \' + \'' . str_replace("'", "\'", $row->node_title) . '\' + \'?\')) { draftpick(' . $row->nid . ',' . $view->args[0] . ',' . $pack_node->field_draft_reference['und'][0]['nid'] . ',' . $user->uid . '); }">' . $output . '</a>';
}
else {
  print $output;
}

?>

