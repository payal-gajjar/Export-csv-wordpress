<?php

/* Add coumn on admin side post*/
function add_post_columns ( $columns ) {
   return array_merge ( $columns, array ( 
     'designation' => __ ( 'Designation' ),
     'featured_image' => 'Image'
   ) );
 }
 add_filter ( 'manage_post_posts_columns', 'add_post_columns' );

 /* Add columns to  post  */
 function post_custom_column ( $column, $post_id ) {
   switch ( $column ) {
     case 'designation':
       echo get_post_meta ( $post_id, 'designation', true );
       break;
     case 'featured_image':
        the_post_thumbnail( 'thumbnail' );
       break;
   }
 }
 add_action ( 'manage_post_posts_custom_column', 'post_custom_column', 10, 2 );


/* Add custom post meta options */
add_action("admin_init", "admin_initi");
    add_action('save_post', 'save_designation');
 
    function admin_initi(){
        add_meta_box("prodInfo-meta", "Designation Options", "meta_options", "post", "side", "default");
}
     
     
function meta_option(){
    global $post;
        $custom = get_post_custom($post->ID);
        $designation = $custom["designation"][0];
            ?>
     <label>Designation:</label><input name="designation" value="<?php echo $designation; ?>" />
     <?php
    }

/*Add Export Button in post */
add_action( 'restrict_manage_posts', 'add_export_button' );
function add_export_button() {
    $screen = get_current_screen();
 
    if (isset($screen->parent_file) && ('edit.php' == $screen->parent_file)) {
        ?>
        <input type="submit" name="export_all_posts" id="export_all_posts" class="button button-primary" value="Export">
        <script type="text/javascript">
            jQuery(function($) {
                $('#export_all_posts').insertAfter('#post-query-submit');
            });
        </script>
        <?php
    }
}

 /* export csv file*/
add_action( 'init', 'func_export_all_posts' );
function func_export_all_posts() {
    if(isset($_GET['export_all_posts'])) {
        $arg = array(
                'post_type' => 'post',
                'post_status' => 'publish',
                'posts_per_page' => -1,
            );
 
        global $post;
        $arr_post = get_posts($arg);
        if ($arr_post) {
 
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="post.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');
 
            $file = fopen('php://output', 'w');
 
            fputcsv($file, array('Post Title', 'URL', 'Designation' , 'Image'));
 
            foreach ($arr_post as $post) {
                setup_postdata($post);
                fputcsv($file, array(get_the_title(), get_the_permalink(), get_field('designation'),get_the_post_thumbnail_url()));
            }
            exit();
        }
    }
}