<?php/*Plugin Name: TeckelDescription: Simple Plugin to prevent spam commentsAuthor: MauriceAuthor URI: Plugin URI: Version: 0.5*//** wp_enque_scripts  * enqueue teckel-comments.js on front end (on posts and pages with comments open), setup ajaxurl and nonce  **/  add_action('wp_enqueue_scripts', 'teckel_add_script');function teckel_add_script() {	if (is_singular() && comments_open()) {		wp_enqueue_script('teckel-comments', plugin_dir_url(__FILE__) . 'teckel-comments.js', array('jquery'));		$data = array(			'ajaxurl' => admin_url('admin-ajax.php'),			'teckel_nonce' => wp_create_nonce('teckel-nonce')		);		wp_localize_script('teckel-comments', 'TheAjax', $data);	}}/**	comment_form_before, comment_form_after, modify comment form   * adds HTML comments used as markers before and after comment form, so the form can later be replaced easier  **/add_action('comment_form_before',"teckel_modify_comments");add_action('comment_form_after',"teckel_modify_comments");function teckel_modify_comments($fields) {		echo '<!--teckel_marker-->';}/** wp_ajax_teckel_replace, wp_ajax_nopriv_teckel_replace, callback for admin-ajax  * gets comment_form() of post via new WP_Query   * (a bit dirty, but WP doesn't seem to provide an easier way to programmatically get the comment form for use in scripts???)  **/add_action('wp_ajax_teckel_replace', 'teckel_replace_callback');add_action('wp_ajax_nopriv_teckel_replace', 'teckel_replace_callback');function teckel_replace_callback() {	if (wp_verify_nonce($_POST["security"], 'teckel-nonce')) {		if (isset($_POST["whichpost"])) {			$thepostid = $_POST["whichpost"];		}		$the_query = new WP_Query(array('p' => $thepostid));		while ($the_query->have_posts()) : // the Loop			$the_query->the_post();			$theform = ob_start(comment_form());		endwhile;		wp_reset_query(); // reset query and postdata		wp_reset_postdata();		}	else { 				echo "Falscher Nonce"; 			}	die(); // always die}/** action template_redirect  * if page is singular (is_single(), is_page() or is_attachment() is true), replace comment form with #teckeledcomments div  **/  add_action('template_redirect', 'teckel_sub_comment_field');

function teckel_sub_comment_field() {
	if (is_feed() || is_trackback()) {
		return;
	}	
	if (is_singular())
		ob_start('teckel_replace');	
}

function teckel_replace($data) {	global $post;
	if (empty($data))
		return;
	if (strpos($data, "wp-comments-post.php") && !strpos($data, "teckel_marker")) { // only if comment form exists && no markers found yet		$replace_div = '<div id="teckeledcomments" show="embed" data-wpid="' . $post->ID . '"></div>';
		$data = preg_replace("/<!--teckel_marker-->.*<!--teckel_marker-->/imsU", $replace_div, $data);	
	}
	return $data;
}