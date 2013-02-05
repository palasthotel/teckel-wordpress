<?php

function teckel_sub_comment_field() {
	if (is_feed() || is_trackback()) {
		return;
	}	
	if (is_singular())
		ob_start('teckel_replace');
}

function teckel_replace($data) {
	if (empty($data))
		return;
	if (strpos($data, "wp-comments-post.php") && !strpos($data, "teckel_marker")) { // only if comment form exists && no markers found yet
		$data = preg_replace("/<!--teckel_marker-->.*<!--teckel_marker-->/imsU", $replace_div, $data);	
	}
	return $data;
}