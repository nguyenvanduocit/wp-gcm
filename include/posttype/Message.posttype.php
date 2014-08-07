<?php
class MessagePostType
{
	public static $instance = null;

	public static $_PostTypeName = "message";
	public static $_PostTypeName_Shift = "Message";

	function __construct() {

	}
	public static function instance()
	{
		if(self::$instance == null)
		{
			$classname = self::$_PostTypeName_Shift."PostType";
			self::$instance = new $classname();
		}
		return self::$instance;
	}
	function register()
	{
		add_action( 'init', array( __CLASS__, 'wpSub_custom_post' ) );
		add_filter( 'post_updated_messages', array( __CLASS__, 'wpSub_updated_messages' ) );
		add_action( 'contextual_help', array( __CLASS__, 'wpSub_contextual_help' ), 10, 3 );
		add_action( 'admin_head', array( __CLASS__, 'add_menu_icons_styles' ) );
		$this->wpSub_custom_metabox();

        //add_action("transition_post_status", array( __CLASS__, 'wpSub_on_publish_post'), 10, 3);
        //add_action("new_to_publish", array( __CLASS__, 'wpSub_on_publish_post'),10, 3);
        //add_action('draft_to_publish', array( __CLASS__, 'wpSub_on_publish_post'), 10, 3);
        //add_action('pending_to_publish', array( __CLASS__, 'wpSub_on_publish_post'), 10, 3);
        //add_action('publish_to_trash', array( __CLASS__, 'wpSub_on_publish_post'), 10, 3);
        //add_action('save_post', array( __CLASS__, 'wpSub_on_save_post'));
	}

    function wpSub_on_publish_post($ID, $post)
    {
        die("asdf");
        add_action('admin_notices' , array( __CLASS__,'wpSub_admin_notices'));
    }

    function wpSub_on_save_post($post_id)
    {
        if ( wp_is_post_revision( $post_id ) )
            return;
        add_action('admin_notices' , array( __CLASS__,'wpSub_admin_notices'));
    }
    function wpSub_admin_notices($message = "c hos")
    {
        ?>
        <div class="updated">
            <p><?php _e( "Co no", 'wp-gcm' ); ?></p>
        </div>
        <?php
    }
	function wpSub_custom_post() {
		$labels = array(
			'name'               => _x( self::$_PostTypeName.'s', 'post type general name' ),
			'singular_name'      => _x( self::$_PostTypeName, 'post type singular name' ),
			'add_new'            => _x( 'Add New', 'Add new '.self::$_PostTypeName ),
			'add_new_item'       => __( 'Add New '.self::$_PostTypeName ),
			'edit_item'          => __( 'Edit '.self::$_PostTypeName ),
			'new_item'           => __( 'New '.self::$_PostTypeName ),
			'all_items'          => __( 'All '.self::$_PostTypeName.'s' ),
			'view_item'          => __( 'View '.self::$_PostTypeName ),
			'search_items'       => __( 'Search '.self::$_PostTypeName.'s' ),
			'not_found'          => __( 'No '.self::$_PostTypeName.'s found' ),
			'not_found_in_trash' => __( 'No '.self::$_PostTypeName.'s found in the Trash' ),
			'parent_item_colon'  => '',
			'menu_name'          => self::$_PostTypeName_Shift.'s',
			'menu_icon'			 => plugins_url( 'images/product.png' , __FILE__ )
		);
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our '.self::$_PostTypeName.'s and '.self::$_PostTypeName.' specific data',
			'public'        => true,
			'menu_position' => 5,
			'supports'      => array( 'title', 'editor', 'thumbnail'),
			'has_archive'   => true,
			//'capability_type' => self::$_PostTypeName,
      		//'map_meta_cap' => true
		);

		register_post_type( self::$_PostTypeName, $args );
	}

	function add_menu_icons_styles(){
	?>

		<style>
		#adminmenu #menu-posts-<?php echo self::$_PostTypeName ?> div.wp-menu-image:before {
		  content: '\f473';
		}
		</style>

	<?php
	}

	function wpSub_updated_messages( $messages ) {
		global $post, $post_ID;
		$messages[self::$_PostTypeName] = array(
			0 => '',
			1 => sprintf( __(self::$_PostTypeName.' updated. <a href="%s">View '.self::$_PostTypeName.'</a>'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.'),
			3 => __('Custom field deleted.'),
			4 => __(self::$_PostTypeName.' updated.'),
			5 => isset($_GET['revision']) ? sprintf( __(self::$_PostTypeName.' restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __(self::$_PostTypeName.' published. <a href="%s">View '.self::$_PostTypeName.' on</a>'), esc_url( get_permalink($post_ID) ) ),
			7 => __(self::$_PostTypeName.' saved.'),
			8 => sprintf( __(self::$_PostTypeName.' submitted. <a target="_blank" href="%s">Preview '.self::$_PostTypeName.'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
			9 => sprintf( __(self::$_PostTypeName.' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview '.self::$_PostTypeName.'</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __(self::$_PostTypeName.' draft updated. <a target="_blank" href="%s">Preview '.self::$_PostTypeName.'</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
		);
		return $messages;
	}

	function wpSub_contextual_help( $contextual_help, $screen_id, $screen ) {
	    $currentScreen = $screen;
	    switch( $screen_id ) {
	        case self::$_PostTypeName :
	            $currentScreen->remove_help_tabs();

	            $currentScreen->add_help_tab( array(
	            'id'        => 'my-help-tab-title',
	            'title'     => __( 'Tên sản phẩm' ),
	            'content'   => __( '<p>Phải đặt tên sản phẩm ngắn ngọn, vừa đủ, thê hiện đúng tên của sản phẩm. Tuyệt đối không đặt tên sản phẩm trùng với mã sản phẩm.</p>' )
	            ) );

	            $currentScreen->add_help_tab( array(
	            'id'        => 'my-help-tab-image',
	            'title'     => __( 'Hình ảnh' ),
	            'content'   => __( '<p>Chọn ảnh minh họa có chất lượng cao. Tốt nhất nên chọn ảnh có kích thước tương đối vuôn.</p>' )
	            ) );

	            $currentScreen->add_help_tab( array(
	            'id'        => 'my-help-tab-productdetail',
	            'title'     => __("Chi tiết sản phẩm"),
	            'content'   => __( '<p>Đây là phần dữ liệu quan trọng, thông tin trong này sẽ được cung cấp cho website mua hàng online.<br>'.
	                                '<b>Giá</b> : Là một số nguyên, không dùng số thực, không có dấu phẩy.<br/>'.
	                                '<b>Đơn vị</b> : Đơn vị tính của một sản phẩm VD : Viên, cục, mét.<br/>'.
	                                '<b>Quy cách đóng gói</b> : Sản phẩm được đóng gói như thế nào</p>'.
	                                '<b>Số lượng</b> : Trong một đơn-vị-đóng-gói có bao nhiêu đơn-vị-sản-phẩm</p>'.
	                                '<b>Lượng hàng</b> : Số lượng hàng trong kho, điền -1 nếu số lượng không giới hạn, hoặc không xác định.</p>'.
	                                '<b>Nổi bật</b> : Chọn mục này để hiển thị sản phẩm ở slider.</p>')
	            ) );
	    }
	    $sidebar = '<p><strong>' . __( 'Thông tin trợ giúp:', 'colorbeats_textdomain' ) . '</strong></p>' .
	        '<p><a href="mailto:nguyenvanduocit@gmail.com">Gửi mail</a></p>' .
	        '<p><a href="http://fb.me/chucbengungon">facebook</a></p>' .
	        '<p>(+84) 167 297 1234</p>';

	    $screen->set_help_sidebar($sidebar);
	    return $contextual_help;
	}

	function wpSub_custom_metabox(){
		$arg = array(
			'post_type' => self::$_PostTypeName,
			'context' => 'advanced',
			'priority' => 'default'
		);
		$classname = self::$_PostTypeName_Shift."DetailMetabox";
		new $classname(self::$_PostTypeName);

	}
}
class MessageDetailMetabox extends scbPostMetabox
{
	private $formFields;
	private $_PostTypeName = '';

	function __construct($PostTypeName)
	{
		$this->_PostTypeName = $PostTypeName;
		$arg = array(
			'post_type' => $this->_PostTypeName,
			'context' => 'advanced',
			'priority' => 'default'
		);

		$this->addField(array(
			'title' => 'Message type',
			'type'	=> 'text',
			'value'	=>	'',
			'name'	=> 'message_type'
		));

		$this->addField(array(
			'title' => 'Notification key',
			'type'	=> 'text',
			'name'	=> 'notification_key'
		));

        $this->addField(array(
            'title' => 'Collapse key',
            'type'	=> 'text',
            'value'	=>	'000',
            'name'	=> 'collapse_key'
        ));

        $this->addField(array(
            'title' => 'Delay while idle',
            'type'	=> 'check',
            'name'	=> 'delay_while_idle'
        ));

        $this->addField(array(
            'title' => 'Time to live',
            'type'	=> 'number',
            'value'	=>	'604800',
            'name'	=> 'time_to_live',
            'desc'	=> 'seconds, default 1week'
        ));
		$this->addField(array(
			'title' => 'Thuộc tính',
			'type'	=> 'text',
			'name'	=> 'Attribute_field'
		));
		parent::__construct($this->_PostTypeName.'DetailMetabox',$this->_PostTypeName." detail",$arg);
	}

	private function addField($field)
	{
		$field['name'] = $this->_PostTypeName ."_". $field['name'];
		$this->formFields[] = $field;
		return $this->formFields;
	}
	public function form_fields() {
		return $this->formFields;
	}
	protected function validate_post_data( $post_data ) {
		return false;
	}
}