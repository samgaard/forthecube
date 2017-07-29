<?php

/**
 * @file
 *
 * Theme implementation: Template for each forum post whether node or comment.
 *
 * All variables available in node.tpl.php and comment.tpl.php for your theme
 * are available here. In addition, Advanced Forum makes available the following
 * variables:
 *
 * - $top_post: TRUE if we are formatting the main post (ie, not a comment)
 * - $reply_link: Text link / button to reply to topic.
 * - $total_posts: Number of posts in topic (not counting first post).
 * - $new_posts: Number of new posts in topic, and link to first new.
 * - $links_array: Unformatted array of links.
 * - $account: User object of the post author.
 * - $name: User name of post author.
 * - $author_pane: Entire contents of the Author Pane template.
 */
	$node = menu_get_object();
?>

<?php if ($top_post): ?>
  <?php print $topic_header ?>
<?php endif; ?>

<div id="<?php print $post_id; ?>" class="<?php print $classes; echo (isset($node->taxonomy_forums['und'][0]['taxonomy_term']) && $node->taxonomy_forums['und'][0]['taxonomy_term']->name == 'P1P1' && !isset($comment->nid) ? ' p1p1-forum-post' : ''); ?>" <?php print $attributes; ?>>
  <div class="forum-post-wrapper">
    <div class="forum-post-panel-sub">
      <?php if (!empty($author_pane)): ?>
        <?php print $author_pane; ?>
      <?php endif; ?>
    </div>

    <div class="forum-post-panel-main clearfix">
      <?php /*if (!empty($title)): ?>
        <div class="forum-post-title">
          <?php print $title ?>
        </div>
      <?php endif; */ ?>
      <span class="forum-post-number"><?php print $permalink; ?></span>

      <div class="forum-post-content">
        <?php
          // We hide the comments and links now so that we can render them later.
          hide($content['taxonomy_forums']);
          hide($content['comments']);
          hide($content['links']);
          if (!$top_post)
            hide($content['body']);
          print render($content);
        ?>
      </div>

      <?php if (!empty($post_edited)): ?>
        <div class="post-edited">
          <?php print $post_edited ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($signature)): ?>
        <div class="author-signature">
          <?php print $signature ?>
        </div>
      <?php endif; ?>
    <div class="forum-post-links">

      <div class="forum-posted-on">
        <?php
				$timesplit = explode(' - ', $date);
				$caldate = substr($timesplit[0], 5);
				$time = $timesplit[1];				
				print l(mtg_helper_time_ago(strtotime($caldate . ' ' . $time)), substr($_SERVER['REQUEST_URI'], 1), array('attributes' => array('data-original-title' => date('m/d/y h:iA', strtotime($caldate . ' ' . $time)), 'data-html' => 'true', 'data-placement' => 'right', 'class' => 'post-date', 'data-toggle' => 'tooltip'), 'fragment' => (isset($comment->nid) ? 'comment-'.$comment->cid : '#')));
				?>
    		
        <?php
        // This whole section is for printing the "new" marker. With core comment
        // we just need to check a variable. With Node Comment, we need to do
        // extra work to keep the views caching used for Node Comment from
        //caching the new markers.
        ?>
        <?php if (!$top_post): ?>
          <?php if (!empty($new)): ?>
            <a id="new"><span class="new">(<?php print $new ?>)</span></a>
          <?php endif; ?>
    
          <?php if (!empty($first_new)): ?>
            <?php print $first_new; ?>
          <?php endif; ?>
    
          <?php if (!empty($new_output)): ?>
            <?php print $new_output; ?>
          <?php endif; ?>
        <?php endif; ?>
      </div>  <?php // End of posted on div ?>
      <?php 
			global $user;
			print (isset($comment->cid) && $user->uid != 0 ? '<a class="btn btn-default btn-xs" href="Javascript:void(0);" onclick="quoteComment('.$comment->cid.');"><span>Quote</span></a>' : '');
			
			print render($content['links']); ?>
    </div> <?php // End of post wrapper div ?>
    </div>
    </div>

  <div class="forum-post-info clearfix">
    <?php if (!empty($in_reply_to)): ?>
   	 <span class="forum-in-reply-to"><?php print $in_reply_to; ?></span>
    <?php endif; ?>

    <?php // Add a note when a post is unpublished so it doesn't rely on theming. ?>
    <?php if (!$node->status): ?>
      <span class="unpublished-post-note"><?php print t("Unpublished post") ?></span>
    <?php endif; ?>  
  </div> <?php // End of post info div ?>

  <div class="forum-post-footer clearfix">
    <div class="forum-jump-links">
      <a href="#forum-topic-top" title="<?php print t('Jump to top of page'); ?>" class="af-button-small"><span><?php print t("Top"); ?></span></a>
    </div>

  </div> <?php // End of footer div ?>
</div> <?php // End of main wrapping div ?>

<?php print render($content['comments']); ?>


