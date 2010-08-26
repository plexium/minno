<?php

/*
 * Function: rss
 * Displays an rss feed.
 * Usage: <minno:rss params="http://urltorssfeed.com/rss,10" />
 */

function rss( $url = '', $limit = 10 )
{
	$items = _rss_fetch_feeds( $url, $limit );

	ob_start();
	?>
	<ul class="rss">
	<?php foreach ($items as $item) { ?>
		<li><a title="<?php echo strip_tags($item['description']); ?>" href="<?php echo htmlspecialchars($item['link']); ?>">
		<?php echo strip_tags($item['title']); ?></a>
		<span class="description"><?php echo strip_tags($item['description'],'<a><b><i><em><strong>'); ?></span>
		</li>
	<?php } ?>
	</ul>
	<?php
	
	return ob_get_clean();
}

function _rss_fetch_feeds( $url, $limit )
{
	require_once( 'magpierss/rss_fetch.inc');
	
	define('MAGPIE_CACHE_DIR', $GLOBALS['store'] . '_cache/');

	$all = array();
	$reorder = array();

	if ( $rss = fetch_rss($url) )
	{
		foreach ( $rss->items as $item )
		{
			$allid = count($all);

			if ( $item['pubdate'] )
				$item['rawdate'] = strtotime( $item['pubdate'] );
			elseif ( $item['dc']['date'] )
				$item['rawdate'] = strtotime( $item['dc']['date'] );
			else
				$item = strtotime( $id );

			if ( $item['description'] )
				$item['description'] = $item['description'];                    
			else
				$item['description'] = $item['atom']['summary'];                    
				
			$reorder[$allid] = $item['rawdate'];
			$item['channel'] = $rss->channel['title'];
			$item['channel-link'] = $rss->channel['link'];
			$all[] = $item;
		}
	}
	
	if ( count($all) == 0 )
	{
		return false;
	}

	arsort($reorder);

	$items = array();
	$i = 0;
	foreach ( $reorder as $id => $date )
	{
		if ( $i++ > $limit )
		{
			break;
		}
		
		$items[] = $all[$id];
	}
	
	return $items;  
}