<?php
/**
 * Genesis Framework.
 *
 * WARNING: This file is part of the core Genesis Framework. DO NOT edit this file under any circumstances.
 * Please do all modifications in the form of a child theme.
 *
 * @package Genesis\Templates
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://my.studiopress.com/themes/genesis/
 */

// This file handles single entries, but only exists for the sake of child theme forward compatibility.
genesis();

global $post;
if($post->ID == "76043") {
	$args = array( 'post_type' => 'contributions', 'posts_per_page' => -1, 'cat'=>-151);
	$loop = new WP_Query($args);
	$j = 1;
	while ( $loop->have_posts() ) : $loop->the_post();
		global $post;
		$pod3 = pods( 'contributions', $post->ID );
		$empty_doc = 1;
		$total_docs = 20;
		for($i=1;$i<=$total_docs;$i++)
		{
			$doc = $pod3->field( 'document_'.$i );
			if(!empty($doc)) {
				$empty_doc = 0;
			}
		}
		if($empty_doc == 1){
			echo "<p>";
			echo get_the_permalink($post->ID);
			echo "</p>";*/
			echo "<p style='text-align:center;'>".$j."</p>"; 
			echo "<p  style='text-align:center;'>".$post->ID."</p>"; 
			echo "<p style='text-align:center;'>". get_the_title($post->ID) . "</p>
			<a  style='text-align:center;display:block;' href='https://thedockforlearning.org/wp-admin/post.php?post=". $post->ID. "&action=edit' target='_blank'>Edit</a> </p><hr>";		
	
		$j++;
		}
	endwhile;
}