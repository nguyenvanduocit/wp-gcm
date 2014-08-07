<?php
class GCMCustomDashboard{
	public static $instance = null;
	public static function instance()
	{
		if(self::$instance == null)
		{;
			self::$instance = new GCMCustomDashboard();
		}
		return self::$instance;
	}
	public function Register()
	{
		add_action('wp_dashboard_setup', array( __CLASS__,'admin_message_dashboard_widgets'),10,2 );
	}
	
	function admin_message_dashboard_widgets() {
    	global $wp_meta_boxes;

		wp_add_dashboard_widget('admin_message_widget', 'Sảm phẩm của bé', array( __CLASS__, 'admin_dashboard_pendingProduct' ));
		wp_add_dashboard_widget('admin_news_widget', 'Thông báo', array( __CLASS__, 'admin_dashboard_news' ));
		wp_add_dashboard_widget('admin_draft_material_widget', 'Nguyên vật liệu cần thêm', array( __CLASS__, 'admin_dashboard_draft_material' ));

		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
	    unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
	    // Get the regular dashboard widgets array 
	    // (which has our new widget already but at the end)
	    $normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];
	    // Backup and delete our new dashboard widget from the end of the array
	    $example_widget_backup = array( 'admin_message_widget' => $normal_dashboard['admin_message_widget'] );
	    unset( $normal_dashboard['admin_message_widget'] );

	    // Merge the two arrays together so our widget is at the beginning
	    $sorted_dashboard = array_merge( $example_widget_backup, $normal_dashboard );
	    // Save the sorted array back into the original metaboxes 
	    $wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
	}
	
	function admin_dashboard_pendingProduct() {
		global $post;
        $backup = $post; //backup current object  
        $post = query_posts('post_type=product&post_status=pending&posts_per_page=10');
        if (have_posts()) :?>
			<div id="activity-product-pending-widget">
				<div id="pending-products" class="activity-block">
					<h4><strong>Sản phẩm đợi duyệt</strong></h4>
					<ul>
			            <?php while (have_posts()) : the_post(); ?>
							<li class="published alt multi">
								<span class="aspst"><?php echo get_the_date() ?></span> : <a href="<?php echo get_edit_post_link() ?>"><?php echo get_the_title() ?></a> bởi <?php the_author_posts_link(); ?>
							</li>
			            <?php endwhile; ?>
					</ul>
				</div>
			</div>
        <?php endif;

        $post = query_posts('post_type=product&post_status=publish&posts_per_page=10');
        if (have_posts()) :?>
			<div id="activity-product-publish-widget">
				<div id="publish-products" class="activity-block">
					<h4><strong>Các sản phẩm mới</strong></h4>
					<ul>
			            <?php while (have_posts()) : the_post(); ?>
							<li class="published alt multi">
								<span class="aspst"><?php echo get_the_date() ?></span> : <a href="<?php echo get_edit_post_link() ?>"><?php echo get_the_title() ?></a> bởi <?php the_author_posts_link(); ?>
							</li>
			            <?php endwhile; ?>
					</ul>
					<ul class="subsubsub">
						<li class="all"><a href="edit.php?post_type=product">All</a> |</li>
						<li class="moderated"><a href="edit.php?post_status=pending&post_type=product">Pending</a> |</li>
						<li class="approved"><a href="edit.php?post_status=approved&post_type=product">Approved</a> |</li>
						<li class="spam"><a href="edit.php?post_status=spam&post_type=product">Spam</a> |</li>
						<li class="trash"><a href="edit.php?post_status=trash&post_type=product">Trash</a></li>
					</ul>
				</div>
			</div>
        <?php else:
            echo "Không có sản phẩm nào của các bé.";
        endif;
        $post = $backup;
        wp_reset_query();
	}

	function admin_dashboard_news() {
		global $post;
        $backup = $post; //backup current object
        $current = $post->ID; //current page ID        
        $post = query_posts('post_type=post&post_status=pending&posts_per_page=10&exclude='.$current);
        if (have_posts()) :?>
			<div id="activity-product-widget">
				<div id="published-products" class="activity-block">
					<h4>Tin mới</h4>
					<ul>
			            <?php while (have_posts()) : the_post(); ?>
							<li class="published alt multi">
								<span class="aspst"><?php echo get_the_date() ?></span> : <a href="<?php echo get_edit_post_link() ?>"><?php echo get_the_title() ?></a> bởi <?php the_author_posts_link(); ?>
							</li>
			            <?php endwhile; ?>
					</ul>
					<ul class="subsubsub">
						<li class="all"><a href="edit.php?post_type=post">All</a> |</li>
						<li class="moderated"><a href="edit.php?post_status=pending&post_type=post">Pending</a> |</li>
						<li class="approved"><a href="edit.php?post_status=approved&post_type=post">Approved</a> |</li>
						<li class="spam"><a href="edit.php?post_status=spam&post_type=post">Spam</a> |</li>
						<li class="trash"><a href="edit.php?post_status=trash&post_type=post">Trash</a></li>
					</ul>
				</div>
			</div>
        <?php else: ?>
        	Không có tin mới nào
        <?php endif;
        $post = $backup;
        wp_reset_query();
	}

	function admin_dashboard_draft_material() {
		global $post;
        $backup = $post; //backup current object
        $current = $post->ID; //current page ID        
        $post = query_posts('post_type=material&post_status=draft&posts_per_page=10&exclude='.$current);
        if (have_posts()) :?>
			<div id="activity-product-widget">
				<div id="published-products" class="activity-block">
					<h4>Material cần thêm</h4>
					<ul>
			            <?php while (have_posts()) : the_post(); ?>
							<li class="published alt multi">
								<span class="aspst"><?php echo get_the_date() ?></span> : <a href="<?php echo get_edit_post_link() ?>"><?php echo get_the_title() ?></a> bởi <?php the_author_posts_link(); ?>
							</li>
			            <?php endwhile; ?>
					</ul>
					<ul class="subsubsub">
						<li class="all"><a href="edit.php?post_type=material">All</a> |</li>
						<li class="moderated"><a href="edit.php?post_status=pending&post_type=material">Pending</a> |</li>
						<li class="approved"><a href="edit.php?post_status=approved&post_type=material">Approved</a> |</li>
						<li class="spam"><a href="edit.php?post_status=spam&post_type=material">Spam</a> |</li>
						<li class="trash"><a href="edit.php?post_status=trash&post_type=material">Trash</a></li>
					</ul>
				</div>
			</div>
        <?php else: ?>
        	Không có tin mới nào
        <?php endif;
        $post = $backup;
        wp_reset_query();
	}
}