<?php session_start();
if(isset($_POST['logout'])) {
    session_destroy();
    }
/*
Plugin Name: مدیریت ارتباط با مشتریان یارامین
Plugin URI: https://yaramin.ir
Description: این پلاگین مختص سایت فروشگاهی yaramin.ir می باشد.
Version: 1.0
Author: لیلا بیداد
*/

 function authh($userid_1,$pst_pass){
	 
	 $user_info_1 = get_userdata($userid_1);

$username_1 = $user_info_1->user_login;
$password_1 = $user_info_1->user_pass;
$test_creds_1 = wp_authenticate($username_1, $pst_pass) ;
$class_1 = get_class($test_creds_1);
$auth_1 = ( $class_1 == 'WP_User' ) ? TRUE : FALSE ;
return($auth_1);
 }
	//////////////////save edited
	if(isset($_POST['edit'])){
	global $wpdb;
	global $msg_CRM;
$table_crm = $wpdb->prefix . "yaramin_crm";
	$wpdb->update( $table_crm,

  array(

    'fullname'       => $_POST['fullname'],
    'mobile'       => $_POST['mobile'],
    'product_cat'       => $_POST['product_cat'],
    'pazirande'       => $_POST['pazirande'],
    'first_follow'       => $_POST['first_follow'],
    'second_follow'       => $_POST['second_follow'],
    'type'       => $_POST['type'],
    'bank'       => $_POST['bank'],
    'status' => $_POST['status']

  ),

  array( 'id' => $_GET['post'] ),

  array(

    '%s',
    '%s',
    '%s',
    '%s',
    '%s',
    '%s'
   

  ),

  array( '%d' )

);

header("Location:".home_url()."/wp-admin/admin.php?page=wp_list_table_class_CRM");
global $msg_CRM;
$msg_CRM='سفارش با موفقیت ذخیره شد!';
	}
	/////////////////////////save edited post	
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class CRMs_List extends WP_List_Table {
	/** Class constructor */
	public function __construct() {
		parent::__construct( [
			'singular' => __( 'Customer', 'sp' ), //singular name of the listed records
			'plural'   => __( 'Customers', 'sp' ), //plural name of the listed records
			'ajax'     => false //does this table support ajax?
		] );
	}
	/**
	 * Retrieve customers data from the database
	 *
	 * @param int $per_page
	 * @param int $page_number
	 *
	 * @return mixed
	 */
	 
	 

	 
	public static function get_yaramin_crm( $per_page = 25, $page_number = 1 ) {
		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->prefix}yaramin_crm";
		
		if(($_GET['from']) &&  ($_GET['to']) ) {
		
		$y_f=explode('/',$_GET['from'])[0];
		$m_f=explode('/',$_GET['from'])[1];
		$d_f=explode('/',$_GET['from'])[2];
		$y_t=explode('/',$_GET['to'])[0];
		$m_t=explode('/',$_GET['to'])[1];
		$d_t=explode('/',$_GET['to'])[2];
		$from = jalali_to_gregorian($y_f,$m_f,$d_f)[0]."-".jalali_to_gregorian($y_f,$m_f,$d_f)[1]."-".jalali_to_gregorian($y_f,$m_f,$d_f)[2];
		$to = jalali_to_gregorian($y_t,$m_t,$d_t)[0]."-".jalali_to_gregorian($y_t,$m_t,$d_t)[1]."-".jalali_to_gregorian($y_t,$m_t,$d_t)[2];


      		
		$sql .=  "  WHERE date >= '".$from."' AND date <= '".$to."'";
	
		}
		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		}
		else
			$sql .= ' ORDER BY product_cat DESC';

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
		$result = $wpdb->get_results( $sql, 'ARRAY_A' );
		return $result;
	}
	/**
	 * Delete a customer record.
	 *
	 * @param int $id customer ID
	 */
	public static function delete_customer( $id ) {
		global $wpdb;
		$wpdb->delete(
			"{$wpdb->prefix}yaramin_crm",
			[ 'id' => $id ],
			[ '%d' ]
		);
	}
	/**
	 * Returns the count of records in the database.
	 *
	 * @return null|string
	 */
	public static function record_count() {
		global $wpdb;
		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}yaramin_crm";
		return $wpdb->get_var( $sql );
	}
	/** Text displayed when no customer data is available */
	public function no_items() {
		_e( 'No yaramin_crm avaliable.', 'sp' );
	}
	/**
	 * Render a column when no column specific method exist.
	 *
	 * @param array $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
		
			case 'fullname':
			case 'mobile':
		
				return $item[ $column_name ];
							case 'product_cat':
								  $argst = array();
								  $termst = get_terms( 'product_cat', $argst );
									foreach ( $termst as $termt ){ if ($termt->term_id==$item[ 'product_cat' ])	return $termt->name;}
			case 'pazirande':
			case 'first_follow':
			case 'second_follow':
			case 'type':
			case 'bank':
			case 'status':
			return $item[ $column_name ];
			case 'author':
			$user_info = get_userdata($item[ $column_name ]);
            return $user_info->first_name." ".$user_info->last_name;
			case 'date':
				return yaramin_jdate_dashed($item[ $column_name ]);
            case 'time':
				return explode(" ",$item[ 'date' ])[1];

			case 'id':
				return '<a href="'.home_url().'/wp-admin/admin.php?page=wp_list_table_class_CRM&post='.$item[ 'id' ].'">ویرایش</a>';


			default:
				return print_r( $item, true ); //Show the whole array for troubleshooting purposes
		}
	}
	/**
	 * Render the bulk edit checkbox
	 *
	 * @param array $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['reserved_id']
		);
	}
	/**
	 * Method for name column
	 *
	 * @param array $item an array of DB data
	 *
	 * @return string
	 */
	function column_name( $item ) {
		$delete_nonce = wp_create_nonce( 'sp_delete_customer' );
		$title = '<strong>' . $item['fullname'] . '</strong>';
		$actions = [
			'delete' => sprintf( '<a href="?page=%s&action=%s&customer=%s&_wpnonce=%s">Delete</a>', esc_attr( $_REQUEST['page'] ), 'delete', absint( $item['id'] ), $delete_nonce )
		];
		return $title . $this->row_actions( $actions );
	}
	/**
	 *  Associative array of columns
	 *
	 * @return array
	 */
	function get_columns() {
		$columns = [
			'cb'      => '<input type="checkbox" />',
			'fullname'    => __( 'نام و نام خانوادگی', 'sp' ),
			'mobile' => __( 'موبایل', 'sp' ),
			'product_cat' => __( 'گروه کالا', 'sp' ),
			'pazirande' => __( 'پذیرنده', 'sp' ),
			'first_follow' => __( 'پیگیری 1', 'sp' ),
			'second_follow' => __( 'پیگیری 2', 'sp' ),
			'type' => __( 'نوع', 'sp' ),
			'bank' => __( 'بانک', 'sp' ),
			'status' => __( 'وضعیت', 'sp' ),
			'author' => __( 'ثبت کننده', 'sp' ),
			'date'    => __( 'تاریخ', 'sp' ),
			'time'    => __( 'ساعت', 'sp' ),
			'id'    => __( 'ویرایش', 'sp' )
		];
		return $columns;
	}
	/**
	 * Columns to make sortable.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'fullname' => array( 'fullname', true ),
			'product_cat' => array( 'product_cat', true ),
			'mobile' => array( 'mobile', false ),
			'qty' => array( 'qty', true ),
			'date' => array( 'date', true ),
			'time' => array( 'time', false )
		);
		return $sortable_columns;
	}
	/**
	 * Returns an associative array containing the bulk action
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => 'حذف',
			'remove' => 'Remove',
			'delete' => 'Delete'
		];
		
		
	//	 $actions = array();
 
    if ( is_multisite() ) {
        if ( current_user_can( 'remove_users' ) ) {
            $actions['remove'] = __( 'Remove' );
        }
    } else {
        if ( current_user_can( 'delete_users' ) ) {
            $actions['delete'] = __( 'Delete' );
        }
    }
 
		
		return $actions;
	}
	/**
	 * Handles data query and filter, sorting, and pagination.
	 */
	public function prepare_items() {
		$this->_column_headers = $this->get_column_info();
		/** Process bulk action */
		$this->process_bulk_action();
		$per_page     = $this->get_items_per_page( 'yaramin_crm_per_page', 25 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();
		$this->set_pagination_args( [
			'total_items' => $total_items, //WE have to calculate the total number of items
			'per_page'    => $per_page //WE have to determine how many items to show on a page
		] );
		$this->items = self::get_yaramin_crm( $per_page, $current_page );
	}
	public function process_bulk_action() {
		//Detect when a bulk action is being triggered...
		if ( 'delete' === $this->current_action() ) {
			// In our file that handles the request, verify the nonce.
			$nonce = esc_attr( $_REQUEST['_wpnonce'] );
			if ( ! wp_verify_nonce( $nonce, 'sp_delete_customer' ) ) {
				die( 'یک کاربر حذف گردیده است!' );
			}
			else {
				self::delete_customer( absint( $_GET['customer'] ) );
		                // esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		                // add_query_arg() return the current url
		                wp_redirect( esc_url_raw(add_query_arg()) );
				exit;
			}
		}
		// If the delete bulk action is triggered
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {
			$delete_ids = esc_sql( $_POST['bulk-delete'] );
			// loop over the array of record IDs and delete them
			foreach ( $delete_ids as $id ) {
				self::delete_customer( $id );
			}
			// esc_url_raw() is used to prevent converting ampersand in url to "#038;"
		        // add_query_arg() return the current url
		        wp_redirect( esc_url_raw(add_query_arg()) );
			exit;
		}
	}
}
class SP_CRM_Plugin {
	// class instance
	static $instance;
	// customer WP_List_Table object
	public $yaramin_crm_obj;
	// class constructor
	public function __construct() {
		add_filter( 'set-screen-option', [ __CLASS__, 'set_screen' ], 10, 3 );
		add_action( 'admin_menu', [ $this, 'plugin_menu' ] );
	}
	public static function set_screen( $status, $option, $value ) {
		return $value;
	}
	public function plugin_menu() {
		$hook = add_menu_page(
			'مدیریت ارتباط با مشتریان یارامین',
			'مدیریت ارتباط با مشتریان یارامین',
			'manage_options',
			'wp_list_table_class_CRM',
			[ $this, 'plugin_settings_page' ]
		);
		add_action( "load-$hook", [ $this, 'screen_option' ] );
	}
	/**
	 * Plugin settings page
	 */
	public function plugin_settings_page() {
		?>
		<style>
		.wp-list-table widefat fixed striped customers{font-family:'sans';}
		</style>
		<div>
			<h2>CRM</h2>

<?PHP 
 echo $msg_CRM; ?>
			<div id="poststuff" style="font-family:'sans';">
				<div id="post-body" class="metabox-holder columns-2">
					<div id="post-body-content">
						<div class="meta-box-sortables ui-sortable">
							
								<?php
								/////////////////////////////////////ساخت فرم ویرایش
								 if($_GET['post']) {
									 ?>
									 <form method="post">
								 <?php
									 $post_id=$_GET['post'];
									 global $wpdb;
									 $table_crm = $wpdb->prefix . "yaramin_crm";
									 $res_2 = $wpdb->get_results( 
										"
										SELECT * 
										FROM $table_crm
										WHERE id='$post_id'
										"
									    );
									foreach ( $res_2 as $res2 ){
									 
									 echo '<form method="post" ><table style="text-align:right;">';
									 echo '<tr class="user-last-name-wrap"><th class="col-md-4">نام و نام خانوادگی: </th><td class="regular-text"><input type="text"  name="fullname" value="'.$res2->fullname.'" /> </td>';
									 echo '<tr class="user-last-name-wrap"><th class="col-md-4">موبایل: </th><td class="regular-text"><input type="text" name="mobile" value="'.$res2->mobile.'"  /> </td>';
									 echo '<tr class="user-last-name-wrap"><th class="col-md-4">پیگیری 1: </th><td class="regular-text"><textarea rows="3"  name="first_follow" value="'.$res2->first_follow.'"  >'.$res2->first_follow.' </textarea></td>';
									 echo '<tr class="user-last-name-wrap"><th class="col-md-4">پیگیری 2: </th><td class="regular-text"><textarea rows="3"  name="second_follow" value="'.$res2->second_follow.'"  >'.$res2->second_follow.'</textarea> </td>';
									 echo '<tr class="user-last-name-wrap"><th class="col-md-4">پذیرنده: </th><td class="regular-text"><input type="text" name="pazirande" value="'.$res2->pazirande.'"  /> </td>';
									
									$selected1='';
									$selected2='';
									$selected3='';
									if($res2->type=='مراجعه به پذیرنده') $selected1='selected';
									if($res2->type=='مراجعه حضوری') $selected2='selected';
									if($res2->type=='تماس تلفنی') $selected3='selected';

									
									 echo '<tr class="user-last-name-wrap"><th class="col-md-4">نوع: </th><td class="regular-text">';
									 echo '<select class="input-text form-control" name="type" id="bank" >';
									 echo '<option value="مراجعه به پذیرنده"  '.$selected1.' >مراجعه به پذیرنده</option>';
									 echo '<option value="مراجعه حضوری"  '.$selected2.' >مراجعه حضوری</option>';
									 echo '<option value="تماس تلفنی"  '.$selected3.' >تماس تلفنی</option>';
									 echo '</select>';
									 echo '</td></tr>';


                                    $selected1='';
									$selected2='';
									$selected4='';
									$selected3='';
									if($res2->bank=='کوثر') $selected1='selected';
									if($res2->bank=='سپه') $selected2='selected';
									if($res2->bank=='قوامین') $selected3='selected';
									if($res2->bank=='انصار') $selected4='selected';


                                     echo '<tr class="user-last-name-wrap"><th class="col-md-4">بانک: </th><td class="regular-text">';
									 echo '<select class="input-text form-control" name="bank" id="bank" >';
									 echo '<option value="کوثر"  '.$selected1.' >کوثر</option>';
									 echo '<option value="سپه"  '.$selected2.' >سپه</option>';
									 echo '<option value="قوامین"  '.$selected3.' >قوامین</option>';
								     echo '<option value="انصار"  '.$selected4.' >انصار</option>';
									 echo '</select>';
									 echo '</td></tr>';
									 
									 
					

									 echo '<tr class="user-last-name-wrap"><th class="col-md-4">گروه کالا: </th><td class="regular-text">';
									 $args = array();
									  
									  $terms = get_terms( 'product_cat', $args );
									  if ( $terms ) {
											echo '<select width="100%" class="input-text form-control" name="product_cat" id="product_cat" >';
											foreach ( $terms as $term ) {
												$selected='';
												

												if($res2->product_cat==$term->term_id) $selected='selected';
												if( ( in_array( $term->term_id, top_level_cat()) ) || ( in_array( $term->term_id, second_level_cat()) ))
												echo '<option value="'.$term->term_id.'" '.$selected.' >'.$term->name.'</option>';
													}
											echo '</select>';
									  }

									echo '</td>';

									$selected1='';
									$selected2='';
									$selected4='';
									$selected3='';
									if($res2->status=='کنسل') $selected1='selected';
									if($res2->status=='قطعی') $selected2='selected';
									if($res2->status=='در انتظار') $selected3='selected';
									if($res2->status=='نیاز به پیگیری') $selected4='selected';


									echo '<tr class="user-last-name-wrap"><th class="col-md-4">وضعیت: </th><td class="regular-text">';
									 									 
									echo '<select class="input-text form-control" name="status" id="status" >';
									echo '<option value="کنسل"  '.$selected1.' >کنسل</option>';
									echo '<option value="قطعی"  '.$selected2.' >قطعی</option>';
									echo '<option value="در انتظار"  '.$selected3.' >در انتظار</option>';
									echo '<option value="نیاز به پیگیری"  '.$selected4.' >نیاز به پیگیری</option>';
									echo '</select>'; 
									 
									 
									 echo '</td></tr>';
									 echo '<tr class="user-last-name-wrap"><th class="col-md-4"> </th><td class="regular-text"><input class="button action" type="submit" value="ذخیره"  name="edit" /></td>';
									 echo '</table></form>';
									}

								}
								//////////////////انتهای ساخت فرم ویرایش

								 else{
									?>
											                               <input class="dokan-btn dokan-btn-theme"   type="button" onclick="tableToExcel('tblExport', 'W3C Example Table')" value="ارسال به اکسل">

									<form method="get" action="?page=wp_list_table_class_CRM"> <input type="hidden" value="wp_list_table_class_CRM" name="page" /><p>گزارش از تاریخ<input type="text" id="datepicker1" name="from" value="<?php echo $_GET['from']; ?>" /> تا تاریخ <input type="text" id="datepicker" name="to" value="<?php echo $_GET['to']; ?>" /> <input class="button action" type="submit" value="بگرد"  name="filter" /></p></form>
										
									 <form   method="post" >
									<?php	
									
								$this->yaramin_crm_obj->prepare_items();
								
echo '<div id="tblExport">';
								
								$this->yaramin_crm_obj->display();
	echo '</div>';
 }								?>
							</form>
							<script type="text/javascript">
    var tableToExcel = (function() {
          var uri = 'data:application/vnd.ms-excel;base64,'
            , template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>'
            , base64 = function(s) { return window.btoa(unescape(encodeURIComponent(s))) }
            , format = function(s, c) { return s.replace(/{(\w+)}/g, function(m, p) { return c[p]; }) }
          return function(table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {worksheet: name || 'Worksheet', table: table.innerHTML}
            window.location.href = uri + base64(format(template, ctx))
          }
        })()
</script> 
						</div>
					</div>
				</div>
				<br class="clear">
			</div>
		</div>
	<?php
	}
	/**
	 * Screen options
	 */
	public function screen_option() {
		$option = 'per_page';
		$args   = [
			'label'   => 'yaramin_crm',
			'default' => 25,
			'option'  => 'yaramin_crm_per_page'
		];
		add_screen_option( $option, $args );
		$this->yaramin_crm_obj = new CRMs_List();
	}
	/** Singleton instance */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}
add_action( 'plugins_loaded', function () {
	SP_CRM_Plugin::get_instance();
} );

///////////short code
 function CRM_function() {
global $wpdb;
$table_crm = $wpdb->prefix . "yaramin_crm";
///////////////////////ذخیره همه
if(isset($_POST['save_all'])){
	$msg.="ویرایش با موفقیت انجام شد!";
	$arr_all_ids=explode(',',$_POST['all_ids']);
	foreach ($arr_all_ids as &$value) {
  
$wpdb->update( $table_crm,

  array(

  //  'fullname'       => $_POST[$value.'_fl'],
    'product_cat'       => $_POST[$value.'_prc'],
    'pazirande'       => $_POST[$value.'_pz'],
    'first_follow'       => $_POST[$value.'_ff'],
    'second_follow'       => $_POST[$value.'_sf'],
    'status' => $_POST[$value.'_st']

  ),

  array( 'id' => $value ),

  array(

  //  '%s',
    '%s',
    '%s',
    '%s',
    '%s',
    '%s'
   

  ),

  array( '%d' )

);



}

}
//////////////پایان ذخیره همه
if(isset($_POST['sabte_moshtari']))
{  
	$the_user = get_user_by('login', $_SESSION['username']); //////////////get user 
	$the_user_id = $the_user->ID;

	$table_crm = $wpdb->prefix."yaramin_crm";

    $tr=1;
	if($_POST['fullname']=='') { $tr=0; $msg.= '<div id="message" class="error"><p>لطفا نام و نام خانوادگی خود را بنویسید!</p></div>';}
	if($_POST['mobile']=='') { $tr=0; $msg.= '<div id="message" class="error"><p>لطفا شماره تلفن همراه خود را بنویسید!</p></div>';}
	if($_POST['pazirande']=='') { $tr=0; $msg.= '<div id="message" class="error"><p>لطفا نام پذیرنده را وارد کنید!</p></div>';}
	if($_POST['product_cat']==0) { $tr=0; $msg.= '<div id="message" class="error"><p>لطفا گروه کالا را وارد کنید!</p></div>';}
	if($tr) {  
                	$msg= '<div class="cart-hint"><span>کالا با موفقیت رزرو شد!</span></div>';	
					$wpdb->insert($table_crm, array(
					'fullname' => $_POST['fullname'],
					'mobile' => $_POST['mobile'],
					'pazirande' => $_POST['pazirande'], // ... and so on
					'product_cat' => $_POST['product_cat'], // ... and so on
					'type' => $_POST['type'], // ... and so on
					'bank' => $_POST['bank'], // ... and so on
					'author' => $the_user_id,  // ... and so on
					'status' => $_POST['status'],  // ... and so on
					'first_follow' => $_POST['first_follow']  // ... and so on
					
				));
	
			}
}
// simple login form
$html='<style>.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12{padding-left:5px;padding-right:5px;}.nav-pills>li>a{border-radius:0px;}.form-control{font-size:12px;padding:2px;}</style><div class="container-fluid main-warp">
		<main id="main" class="site-main" role="main">	<article>';
$html.='<div>';

$userid_1 = 10;
$userid_2 = 657;
$userid_3 = 1052;
$userid_4 = 1053;
$userid_5 = 122;
$userid_6 = 1054;
////////////////////



if(!isset($_SESSION['username'])) {
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
        if(authh($userid_1,$_POST['password']) || authh($userid_2,$_POST['password']) || authh($userid_3,$_POST['password']) || authh($userid_4,$_POST['password']) || authh($userid_5,$_POST['password']) || authh($userid_6,$_POST['password']) ) 
            $_SESSION['username'] = $_POST['username'];
        }
        else {
        }
    }
	?>
	<style>.crm{overflow-x:hidden !important;font-family:'sans';} </style>
	<?php
if(!isset($_SESSION['username'])) {
$custom_logo_id = get_theme_mod( 'custom_logo' );
$image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
                $html.= '<div class="col-md-12"><div class="col-md-4"></div><div class="col-md-4" style="text-align:center;"><img width="50%" style="margin:10px;" src="'.home_url().'/wp-content/uploads/2018/11/logow-1.png"  /></div><div class="col-md-4"></div></div>
<table class="crm" ><tr><td class="col-md-4"></td>
						<td class="col-md-4" style="border:1px solid #e2e2e2;padding:30px;">
						                        <form name="input"  method="post">
						<div class="col-md-4" style="margin-bottom:3px;">نام کاربری: </div><div class="col-md-12" style="margin-bottom:3px;"><input class="input-text form-control" type="text" name="username" /></div>
						<div class="col-md-4" style="margin-bottom:3px;">کلمه عبور: </div><div class="col-md-12" style="margin-bottom:3px;"><input class="input-text form-control" type="password" name="password" /></div>
						<div class="col-md-12"> <input style="background-color:rgb(67 ,110 ,79);max-height:40px;border:1px solid rgb(67 ,110 ,79);width:100%;" class="dokan-btn dokan-btn-theme" type="submit" value="ورود" /></div>

                           
                        </form>
						</td>
						
						<td class="col-md-4"></td><tr></table>';
            }
            else {
				
                    $html.='<div class="crm">'.$msg.'</br>';
					$html.='<div class="row">

  <!-- Navigation Buttons -->
  <div class="col-md-2">
    <ul class="nav nav-pills nav-stacked" id="myTabs">
      <li><a style="text-align:center;" href="#jari" data-toggle="pill"><img width="50%" src="'.home_url().'/wp-content/uploads/2019/05/avatar.png" />
	  <p  style="font-family:sans;font-size:18px;font-weight:bold;">'.$_SESSION['username'].'</p>
</a></li>
      <li  class="active"><a href="#home" data-toggle="pill">جستجو</a></li>
      <li><a href="#profile" data-toggle="pill">ثبت درخواست</a></li>
      <li><a href="#report" data-toggle="pill">گزارش پیگیری</a></li>
      <li><a href="#messages" data-toggle="pill">خروج</a></li>
    </ul>
  </div>

  <!-- Content -->
  <div class="col-md-10">
    <div class="tab-content">
      <div class="tab-pane" id="jari">'.$_SESSION['username'].'</div>
      <div class="tab-pane active" id="home">'.html_search().'</div>
      <div class="tab-pane" id="profile">'.html_request_reg().'</div>
      <div class="tab-pane" id="report">'.html_report().'</div>
      <div class="tab-pane" id="messages">'.html_3().'</div>
    </div>
  </div>

</div>';
					
	
            } 

////////////////////////////
$html.='</div>
</div></div></div></article></main></div>';

  return $html;
}
 add_shortcode('yaramin_CRM', 'CRM_function');
 
 


function html_search(){
	
	$the_user = get_user_by('login', $_SESSION['username']); //////////////get user 
	$the_user_id = $the_user->ID;

	$table_crm = $wpdb->prefix."yaramin_crm";
	
	
		$ar_id=array();
	global $wpdb; 
		$table_crm = $wpdb->prefix . "yaramin_crm";

							 $mobile_sch=$_POST['mobile_sch'];
					 
		$sql="SELECT * FROM {$wpdb->prefix}yaramin_crm WHERE mobile='$mobile_sch'	AND author='$the_user_id'";


if(($_POST['from']) &&  ($_POST['to']) ) {
		
		$y_f=explode('/',$_POST['from'])[0];
		$m_f=explode('/',$_POST['from'])[1];
		$d_f=explode('/',$_POST['from'])[2];
		$y_t=explode('/',$_POST['to'])[0];
		$m_t=explode('/',$_POST['to'])[1];
		$d_t=explode('/',$_POST['to'])[2];
		$from = jalali_to_gregorian($y_f,$m_f,$d_f)[0]."-".jalali_to_gregorian($y_f,$m_f,$d_f)[1]."-".jalali_to_gregorian($y_f,$m_f,$d_f)[2];
		$to = jalali_to_gregorian($y_t,$m_t,$d_t)[0]."-".jalali_to_gregorian($y_t,$m_t,$d_t)[1]."-".jalali_to_gregorian($y_t,$m_t,$d_t)[2];
      		
		$sql .=  "  AND date >= '".$from."' AND date <= '".$to."'";
	
		}


						$res_1 = $wpdb->get_results($sql);
					$i=0;
	
	
	$html_search='<style>.input-text form-control{}</style><div class="col-md-12" style="">';
	$html_search.='<form  method="post" class="register"><div class="dokan-dahsboard-product-listing-wrapper" >
					<div class="col-md-12" >
							<div  class="col-md-6"><input type="text" class="input-text form-control" name="mobile_sch" id="mobile_sch"  value="'.$_POST['mobile_sch'].'"   placeholder="لطفا شماره موبایل مشتری را وارد نمایید..."></div>
							<div  class="col-md-2"><input type="text" class="input-text form-control" name="from" id="datepicker1"  value="'.$_POST['from'].'"   placeholder="از..."></div>
							<div  class="col-md-2"><input type="text" class="input-text form-control" name="to" id="datepicker"  value="'.$_POST['to'].'"   placeholder="تا..."></div>
							<div class="col-md-2"><input type="submit" style="background-color:rgb(67 ,110 ,79);border:1px solid rgb(67 ,110 ,79);" class="dokan-btn dokan-btn-theme" name="search_moshtari" value="جستجو" /> </div>
					</div></div></form>';
					
					if(isset($_POST['search_moshtari'])) { ///////////////نمایش پس از جستجو
					$html_search.='<div class="col-md-12" style="margin:10px 0;background-color:#eee;padding:2px;">';
$html_search.='<div class="col-md-2" >نام و نام خانوادگی</div>';
$html_search.='<div class="col-md-1" >پذیرنده</div>';
$html_search.='<div class="col-md-3" >پیگیری 1</div>';
$html_search.='<div class="col-md-3" >پیگیری 2</div>';
$html_search.='<div class="col-md-1" >گروه کالا</div>';
$html_search.='<div class="col-md-1" >وضعیت</div>';
$html_search.='<div class="col-md-1" >تاریخ</div>';

$html_search.='</div>';
					
					$html_search.= '<form method="post" >';
					foreach ( $res_1 as $res1 ) 
					{
						array_push($ar_id,$res1->id);
						$i++;
											$html_search.= '<div class="col-md-12" style="margin-top:6px;margin-bottom:6px;border-bottom:1px solid #ddd;"><div class="col-md-2"><input type="hidden"  name="'.$res1->id.'_id" value="'.$res1->id.'"  />'.$res1->fullname.'</div>';
											$html_search.= '<div class="col-md-1"><input id="'.$res1->id.'_pz" name="'.$res1->id.'_pz" class="input-text form-control" value="'.$res1->pazirande.'"  /></div>';

						$html_search.= '<div class="col-md-3"><textarea rows="3" id="'.$res1->id.'_ff" name="'.$res1->id.'_ff" class="input-text form-control" value="'.$res1->first_follow.'"  >'.$res1->first_follow.'</textarea></div>';
						$html_search.= '<div class="col-md-3"><textarea rows="3" id="'.$res1->id.'_sf" name="'.$res1->id.'_sf" class="input-text form-control" value="'.$res1->second_follow.'"  >'.$res1->second_follow.'</textarea></div>';
						
												$html_search.= '<div class="col-md-1">';
						

						  $args = array();
						  $terms = get_terms( 'product_cat', $args );
						  if ( $terms ) {
								$html_search.= '<select id="'.$res1->id.'_prc" class="input-text form-control" name="'.$res1->id.'_prc" id="product_cat"  >';
								foreach ( $terms as $term ) {
									$selected="";
									if($term->term_id==$res1->product_cat) $selected="selected";
										if( ( in_array( $term->term_id, top_level_cat()) ) || ( in_array( $term->term_id, second_level_cat()) ))
											$html_search.= '<option value="'.$term->term_id.'"  '.$selected.' >'.$term->name.'</option>';
										}
								$html_search.= '</select>';
						  }


						$html_search.= '</div>';
						$html_search.= '<div class="col-md-1">';
						$selected1='';
						$selected2='';
						$selected4='';
						$selected3='';
						     if($res1->status=='کنسل') $selected1='selected';
						     if($res1->status=='قطعی') $selected2='selected';
						     if($res1->status=='در انتظار') $selected3='selected';
						     if($res1->status=='نیاز به پیگیری') $selected4='selected';
							$html_search.= '<select id="'.$res1->id.'_st" name="'.$res1->id.'_st"  class="input-text form-control" name="status" id="status" >';
							$html_search.= '<option value="کنسل"  '.$selected1.' >کنسل</option>';
							$html_search.= '<option value="قطعی"  '.$selected2.' >قطعی</option>';
							$html_search.= '<option value="در انتظار"  '.$selected3.' >در انتظار</option>';
							$html_search.= '<option value="نیاز به پیگیری"  '.$selected4.' >نیاز به پیگیری</option>';
							$html_search.= '</select>';
						
							$html_search.= '</div>';

						
						$html_search.= '<div class="col-md-1">'.yaramin_jdate_dashed($res1->date).'</div></div>';	
						
						
					}
					$html_search.= '<div style="margin-top:5px;"  class="col-md-12"><input type="hidden" name="all_ids" value="'.implode(",",$ar_id).'" />
						<input class="dokan-btn dokan-btn-theme" style="width:100%;"  type="submit" name="save_all" value="ذخیره همه" /></div>';
						
						
						
						
						$html_search.= '</form>';
					$html_search.="</br> تعداد رکورد های یافت شده ".$i." عدد می باشد ";

					}
	
	$html_search.='</div>';
	
	 return $html_search;
 }
 function html_request_reg(){
 
		  $html_request_reg.='<div class="col-md-12" style="font-size:13px;">
						<form  id="sabte_moshtari" method="post" class="register">
						<div class="col-md-2 " style="margin-bottom:30px;font-weight:bold;">نام و نام خانوادگی:</div><div style="margin-bottom:30px;" class="col-md-4 "><input type="text" class="input-text form-control" name="fullname" id="last-name"  value="'.$_POST['fullname'].'" ></div>
						<div class="col-md-2 " style="margin-bottom:30px;font-weight:bold;">شماره موبایل:</div><div style="margin-bottom:30px;" class="col-md-4 "><input type="text" class="input-text form-control" name="mobile" id="shop-mobile"  value="'.$_POST['mobile'].'" ></div>
						<div class="col-md-2 " style="margin-bottom:30px;font-weight:bold;">نام پذیرنده:</div><div style="margin-bottom:30px;" class="col-md-4 "><input type="text" class="input-text form-control" name="pazirande" id="pazirande"  value="'.$_POST['pazirande'].'"  ></div>
						<div class="col-md-2 " style="margin-bottom:30px;font-weight:bold;">گروه کالا:</div><div style="margin-bottom:30px;" class="col-md-4 ">';
							 

				 $args = array();
				  
				  $terms = get_terms( 'product_cat', $args );
				  if ( $terms ) {
						$html_request_reg.= '<select class="input-text form-control" name="product_cat" id="product_cat" >';
						foreach ( $terms as $term ) {
							if( ( in_array( $term->term_id, top_level_cat()) ) || ( in_array( $term->term_id, second_level_cat()) ))
							$html_request_reg.= '<option value="'.$term->term_id.'" >'.$term->name.'</option>';
								}
						$html_request_reg.= '</select>';
				  }

				$html_request_reg.='</div>';
				$html_request_reg.='<div class="col-md-2 " style="margin-bottom:30px;font-weight:bold;">نوع:</div><div style="margin-bottom:30px;" class="col-md-4 "> ';
							$html_request_reg.= '<select class="input-text form-control" name="type" id="type" >';
							$html_request_reg.= '<option value="مراجعه به پذیرنده" >مراجعه به پذیرنده</option>';
							$html_request_reg.= '<option value="مراجعه حضوری" >مراجعه حضوری</option>';
							$html_request_reg.= '<option value="تماس تلفنی" >تماس تلفنی</option>';
							$html_request_reg.= '</select>';
				$html_request_reg.='</div>';
				
				$html_request_reg.='<div class="col-md-2 " style="margin-bottom:30px;font-weight:bold;">بانک:</div><div style="margin-bottom:30px;" class="col-md-4 "> ';
							$html_request_reg.= '<select class="input-text form-control" name="bank" id="bank" >';
							$html_request_reg.= '<option value="کوثر"  '.$selected.' >کوثر</option>';
							$html_request_reg.= '<option value="سپه"  '.$selected.' >سپه</option>';
							$html_request_reg.= '<option value="قوامین"  '.$selected.' >قوامین</option>';
							$html_request_reg.= '<option value="انصار"  '.$selected.' >انصار</option>';
							$html_request_reg.= '</select>';
				$html_request_reg.='</div>';
				
				
				$html_request_reg.='<div class="col-md-2 " style="margin-bottom:30px;font-weight:bold;">وضعیت:</div><div style="margin-bottom:30px;" class="col-md-4 "> ';
							$html_request_reg.= '<select class="input-text form-control" name="status" id="status" >';
							$html_request_reg.= '<option value="کنسل"  '.$selected.' >کنسل</option>';
							$html_request_reg.= '<option value="قطعی"  '.$selected.' >قطعی</option>';
							$html_request_reg.= '<option value="در انتظار"  '.$selected.' >در انتظار</option>';
							$html_request_reg.= '<option value="نیاز به پیگیری"  '.$selected.' >نیاز به پیگیری</option>';
							$html_request_reg.= '</select>';
				$html_request_reg.='</div>';
				
				$html_request_reg.='<div class="col-md-2 " style="margin-bottom:30px;font-weight:bold;">پیگیری1:</div><div style="margin-bottom:30px;" class="col-md-4 "> ';
							$html_request_reg.= '<textarea rows="3" class="input-text form-control" name="first_follow" id="first_follow" >';
							$html_request_reg.= '</textarea>';
				$html_request_reg.='</div>';
				
				
				
				
				$html_request_reg.='<div style="left:-999em; position:absolute;"><label for="trap">ضد اسپم</label><input type="text" name="email_2" id="trap" tabindex="-1"></div>

				<div class="col-md-6  su" style="text-align:center;"> 

				<input type="submit" style="background-color:rgb(67 ,110 ,79);max-height:40px;border:1px solid rgb(67 ,110 ,79);" class="dokan-btn dokan-btn-theme" name="sabte_moshtari" value="ثبت حضوری کالا"></div>


				</form>
				</div>'; 
	 
 
	 	 return $html_request_reg;

 }
 function html_3(){
	$html_3='<div "col-md-9" >';
	$html_3.='آیا از خروج خود مطمئن هستید؟<form style="margin:5px 0;" name="logout"  method="post"><input class="dokan-btn dokan-btn-theme" type="submit" value="خروج" name="logout"/> </form>'; 
	$html_3.='</div>';
    return $html_3;

	 
 }
 
 
///////////////////////تابع زیر بالاترین سطح طبقه بندی کالا را ارائه می دهد
function top_level_cat()
{
	$arr_parrent = array();
	$argst = array();
	$termst = get_terms( 'product_cat', $argst );

	foreach ($termst as $categoria) {
	if($categoria->parent == 0){
	   array_push($arr_parrent,$categoria->term_id);
	}
	}
    return($arr_parrent);
}
/////////////////////////تابع زیر سطح دوم طبقه بندی کالاها را ارائه می دهد
function second_level_cat(){
	$argst = array();
	$arr_parrent_2 = array();
	$arr_parrent=top_level_cat();
	$termst = get_terms( 'product_cat', $argst );
	foreach ($termst as $categoria) {
			foreach ($arr_parrent as $catp) {

				if($categoria->parent == $catp){
				   array_push($arr_parrent_2,$categoria->term_id);
				}
			}
		}
    return($arr_parrent_2);
}
//////////////////نمایش گزارش
function html_report(){
		global $wpdb; 

	$the_user = get_user_by('login', $_SESSION['username']); //////////////get user 
	$the_user_id = $the_user->ID;

	$table_crm = $wpdb->prefix."yaramin_crm";
	
	
		$ar_id=array();
		$sql="SELECT * 	FROM {$wpdb->prefix}yaramin_crm	WHERE  author='$the_user_id'";
	if(($_POST['from']) &&  ($_POST['to']) ) {
		
		$y_f=explode('/',$_POST['from'])[0];
		$m_f=explode('/',$_POST['from'])[1];
		$d_f=explode('/',$_POST['from'])[2];
		$y_t=explode('/',$_POST['to'])[0];
		$m_t=explode('/',$_POST['to'])[1];
		$d_t=explode('/',$_POST['to'])[2];
		$from = jalali_to_gregorian($y_f,$m_f,$d_f)[0]."-".jalali_to_gregorian($y_f,$m_f,$d_f)[1]."-".jalali_to_gregorian($y_f,$m_f,$d_f)[2];
		$to = jalali_to_gregorian($y_t,$m_t,$d_t)[0]."-".jalali_to_gregorian($y_t,$m_t,$d_t)[1]."-".jalali_to_gregorian($y_t,$m_t,$d_t)[2];
      		
		$sql .=  "  AND date >= '".$from."' AND date <= '".$to."'";
	
		}	
		$sql .="  ORDER BY id DESC";
		

							 $mobile_sch=$_POST['mobile_sch'];

						$res_1 = $wpdb->get_results($sql);
					$i=0;
	
	
	$html_report='<style>.input-text form-control{}</style><div class="col-md-12" style="">';
	$html_report.='<form  method="post" class="register"><div class="dokan-dahsboard-product-listing-wrapper" >
					<div class="col-md-12" >
							<div  class="col-md-6"><input type="text" class="input-text form-control" name="mobile_sch" id="mobile_sch"  value="'.$_POST['mobile_sch'].'"   placeholder="لطفا شماره موبایل مشتری را وارد نمایید..."></div>
							<div  class="col-md-2"><input type="text" class="input-text form-control" name="from" id="datepicker1"  value="'.$_POST['from'].'"   placeholder="از..."></div>
							<div  class="col-md-2"><input type="text" class="input-text form-control" name="to" id="datepicker"  value="'.$_POST['to'].'"   placeholder="تا..."></div>
							<div class="col-md-2"><input type="submit" style="background-color:rgb(67 ,110 ,79);border:1px solid rgb(67 ,110 ,79);" class="dokan-btn dokan-btn-theme" name="search_moshtari" value="جستجو" /> </div>
					</div></div></form>';
					
					 //////////////نمایش کامل گزارش
					$html_report.='<div class="col-md-12" style="margin:10px 0;background-color:#eee;padding:2px;">';
$html_report.='<div class="col-md-1" >نام و نام خانوادگی</div>';
$html_report.='<div class="col-md-1" >موبایل</div>';
$html_report.='<div class="col-md-1" >پذیرنده</div>';
$html_report.='<div class="col-md-3" >پیگیری 1</div>';
$html_report.='<div class="col-md-3" >پیگیری 2</div>';
$html_report.='<div class="col-md-1" >گروه کالا</div>';
$html_report.='<div class="col-md-1" >وضعیت</div>';
$html_report.='<div class="col-md-1" >تاریخ</div>';

$html_report.='</div>';
					
					$html_report.= '<form method="post" >';
					foreach ( $res_1 as $res1 ) 
					{
						array_push($ar_id,$res1->id);
						$i++;
											$html_report.= '<div class="col-md-12" style="margin-top:6px;margin-bottom:6px;border-bottom:1px solid #ddd;"><div class="col-md-1"><input type="hidden"  name="'.$res1->id.'_id" value="'.$res1->id.'"  />'.$res1->fullname.'</div>';
											$html_report.= '<div class="col-md-1">'.$res1->mobile.'</div>';
											$html_report.= '<div class="col-md-1"><input id="'.$res1->id.'_pz" name="'.$res1->id.'_pz" class="input-text form-control" value="'.$res1->pazirande.'"  /></div>';

						$html_report.= '<div class="col-md-3"><textarea rows="3" id="'.$res1->id.'_ff" name="'.$res1->id.'_ff" class="input-text form-control" value="'.$res1->first_follow.'"  >'.$res1->first_follow.'</textarea></div>';
						$html_report.= '<div class="col-md-3"><textarea rows="3" id="'.$res1->id.'_sf" name="'.$res1->id.'_sf" class="input-text form-control" value="'.$res1->second_follow.'"  >'.$res1->second_follow.'</textarea></div>';
						
												$html_report.= '<div class="col-md-1">';
						

						  $args = array();
						  $terms = get_terms( 'product_cat', $args );
						  if ( $terms ) {
								$html_report.= '<select id="'.$res1->id.'_prc" class="input-text form-control" name="'.$res1->id.'_prc" id="product_cat"  >';
								foreach ( $terms as $term ) {
									$selected="";
									if($term->term_id==$res1->product_cat) $selected="selected";
										if( ( in_array( $term->term_id, top_level_cat()) ) || ( in_array( $term->term_id, second_level_cat()) ))
											$html_report.= '<option value="'.$term->term_id.'"  '.$selected.' >'.$term->name.'</option>';
										}
								$html_report.= '</select>';
						  }


						$html_report.= '</div>';
						$html_report.= '<div class="col-md-1">';
						$selected1='';
						$selected2='';
						$selected4='';
						$selected3='';
						     if($res1->status=='کنسل') $selected1='selected';
						     if($res1->status=='قطعی') $selected2='selected';
						     if($res1->status=='در انتظار') $selected3='selected';
						     if($res1->status=='نیاز به پیگیری') $selected4='selected';
							$html_report.= '<select id="'.$res1->id.'_st" name="'.$res1->id.'_st"  class="input-text form-control" name="status" id="status" >';
							$html_report.= '<option value="کنسل"  '.$selected1.' >کنسل</option>';
							$html_report.= '<option value="قطعی"  '.$selected2.' >قطعی</option>';
							$html_report.= '<option value="در انتظار"  '.$selected3.' >در انتظار</option>';
							$html_report.= '<option value="نیاز به پیگیری"  '.$selected4.' >نیاز به پیگیری</option>';
							$html_report.= '</select>';
						
							$html_report.= '</div>';

						
						$html_report.= '<div class="col-md-1">'.yaramin_jdate_dashed($res1->date).'</div></div>';	
						
						
					}
					$html_report.= '<div style="margin-top:5px;"  class="col-md-12"><input type="hidden" name="all_ids" value="'.implode(",",$ar_id).'" />
						<input class="dokan-btn dokan-btn-theme" style="width:100%;"  type="submit" name="save_all" value="ذخیره همه" /></div>';
						
						
						
						
						$html_report.= '</form>';
					$html_report.="</br> تعداد رکورد های یافت شده ".$i." عدد می باشد ";

		
	
	$html_report.='</div>';
	
	 return $html_report;
 }
////////////انتهای نمایش گزارش 
 ?>
