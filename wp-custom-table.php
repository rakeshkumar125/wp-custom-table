<?php
/*
Plugin Name: WP custom easy table
Description:The plugin used for listing of the mysql table. You can generate the lsting in backed without coding. It help for the middle level developer. 
Version: 1.0.0
Author: Postnidea
*/

include(plugin_dir_path( __FILE__ )."/helper/ssp.class.php");
function register_custom_menu_page() {
    add_menu_page('Wp Custom Table', 'Wp Custom Table', 'manage_options', 'custompage', '_custom_menu_page', null, 6); 
	add_submenu_page( 'custompage', 'List', 'List', 'manage_options', 'subpage_test', 'subpage_test' ); 
}

add_action('admin_menu', 'register_custom_menu_page');

function subpage_test(){
 echo do_shortcode('[wp-custom-table table="test"]');  
}

function _custom_menu_page(){
   echo do_shortcode('[wpc id=110]');  
}

function wpc_data($atts) {
    
    include( plugin_dir_path( __FILE__ ) . 'wp-custom-table-template.php');
  
}

add_shortcode('wpc', 'wpc_data');


function wp_custom_table($atts) {
    
    include( plugin_dir_path( __FILE__ ) . 'wp-custom-table-template.php');
  
}

add_shortcode('wp-custom-table', 'wp_custom_table');
add_action('init', 'wp_custom_table_register');

function wp_custom_table_register() {

    $labels = array(
        'name' => _x('WPC Table', 'post type general name'),
        'all_items' => _x('All Shortcode', 'post type general name'),
        'singular_name' => _x('News Shortcode', 'post type singular name'),
        'add_new' => _x('Nieuw Shortcode', 'Shortcode'),
        'add_new_item' => __('Add New Shortcode'),
        'edit_item' => __('Edit Shortcode'),
        'new_item' => __('New Shortcode'),
        'view_item' => __('View Shortcode Item'),
        'search_items' => __('Search Shortcode'),
        'not_found' =>  __('Nothing found'),
        'not_found_in_trash' => __('Nothing found in Trash'),
        'parent_item_colon' => ''
    );
 
    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title'),
        'has_archive'=> true
      ); 
 
    register_post_type('wp_custom_table' , $args);
}


add_action( 'admin_enqueue_scripts','wp_custom_table_assests');

function wp_custom_table_assests(){
    
    wp_register_script('wpc-script', plugin_dir_url( __FILE__ ).'js/wpc_custom_table.js');
    $wpc_localize_variable = array('wpc_ajax_path' =>admin_url( 'admin-ajax.php' ));
    wp_localize_script( 'wpc-script', 'wpc_localize_data', $wpc_localize_variable );
    wp_enqueue_script('wpc-script',array('jquery'));


    wp_enqueue_style( 'datatable-base-style', plugin_dir_url( __FILE__ ).'css/jquery.dataTables.css');
    wp_enqueue_script( 'datatable-script', plugin_dir_url( __FILE__ ).'js/jquery.dataTables.min.js', array('jquery'));
    wp_enqueue_script( 'wpc-script', plugin_dir_url( __FILE__ ).'js/wpc_custom_table.js', array('jquery'));
    
}
	

add_action("wp_ajax_get_wp_custom_table", "get_wp_custom_table"); //for public usage
//add_action("wp_ajax_nopriv_get_template_data", "get_template_data"); // for restrict usage
function get_wp_custom_table() {

/*	echo "<pre>";
	print_r($_REQUEST);
	echo "</pre>";*/
    //$table = 'markers';
    $table = $_REQUEST['table_name'];
	$post_id = $_REQUEST['post_id'];
    $raw_columns = get_post_meta($post_id, 'wpc_columns_name', true ); 
	/*print_r($columns);
    die;*/
    // Table's primary key
	//$primaryKey = 'id';
		
	/*$columns = array(
		array( 'db' => 'id', 'dt' => 0 ,'formatter'=>function($id, $row){ return "<input type='checkbox' name='delete_id[]' value='".$id."'>";}),
		array( 'db' => 'name', 'dt' => 1 ),
		array( 'db' => 'address','dt' => 2 ),
		array( 'db' => 'lat','dt' => 3 ),
		array( 'db' => 'lng','dt' => 4 ),
		array('db'=> 'id','dt'=> 5,'formatter' => function( $id, $row ) {
				return '<a href="edit.php?id='.$id.'">Edit</a>';
				//return $id;
			}),
		array('db'=> 'id','dt'=> 6,'formatter' => function( $id, $row ) {
				return '<a href="edit.php?id='.$id.'">View</a>';
				//return $id;
			})

		);*/

        $columns  =array();
        $i=0;
        $raw_columns = array_keys($raw_columns);
        foreach($raw_columns as $c){
            $columns[] = array( 'db' => $c, 'dt' =>$i);
            $i++;
        }

//print_r( $raw_columns);
    //print_r($columns);

	// SQL server connection information
global $wpdb;
	$sql_details = array(
		'user' => $wpdb->dbuser,
		'pass' => $wpdb->dbpassword,
		'db'   => $wpdb->dbname,
		'host' => $wpdb->dbhost
	);

	echo json_encode(
		SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
	);

	die;
}


function wpc_shortcode_add_meta_boxes( $post ){
    add_meta_box( 'wpc_meta_box_1', __( 'Shortcode', 'wpc' ), 'wpc_build_meta_box', 'wp_custom_table', 'side', 'low' );
    add_meta_box( 'wpc_meta_box_2', __( 'Details', 'wpc' ), 'wpc_details', 'wp_custom_table', 'normal', 'low' );
}
add_action( 'add_meta_boxes_wp_custom_table', 'wpc_shortcode_add_meta_boxes' );

function wpc_build_meta_box( $post ){
    echo  "<input type='text' value='[wpc id=".$post->ID." ]' style='width:100%; height:30px;' readonly>";

}

function wpc_details(){
?>
<?php 
global $wpdb;
global $post;
$cols_sql = "show tables";
$all_objects = $wpdb->get_col( $cols_sql );
?>
<style type="text/css">
        #columns_data{
        max-height: 300px;
    overflow-y: auto;
    }
</style>
<input type="hidden" value="<?php echo $post->ID; ?>" id="wpc_shortcode_id">
<table width="100%">
    <tr><td width="25%"><label>Name</label></td><td width="25%">
         <?php 

        $existting_table = get_post_meta( get_the_ID(), 'wpc_table_name', true );

         ?>
         <input type="hidden" id="wpc_table_name" name="wpc_table_name" value="<?php echo $existting_table; ?>">
        <select id="wpc_tables" name="wpc_tables">


        <?php foreach ($all_objects as $objects) { ?>
        <option value="<?php echo $objects; ?>" <?php if($existting_table==$objects){ echo "selected"; } ?>><?php echo $objects; ?></option>
        <?php } ?>
        </select>
        </td>

    <td width="30%">Choose primary key</td>
<td>
    <select>
        <option value="">Select</option>
        <option>Test 2</option>
        <option>Test 3</option>
    </select>
</td>

        </tr>
        <tr>
            <td>Select Column </td>
            <td colspan="2">
                <div id="columns_data">
                
                </div>
            </td>
            <td> </td>
        </tr>
        <tr id="more_option">
            <td colspan="2">
                <input type="checkbox" name="">
                Are you want more info popup
            </td>
            <td colspan="2">
                <input type="checkbox" name="">
                Are you want delete button
            </td>
        </tr>
        <tr>
           <td colspan="4">this is the test info</td>     
        </tr>
</table>
<?php
}

add_action("wp_ajax_get_keys", "get_keys");
function get_keys(){
    global $wpdb;
    $table_name = $_REQUEST['table_name'];
    $post_id = $_REQUEST['post_id'];
    $return_array = array();
    $table_keys_list = $wpdb->get_results("describe ".$table_name);
    $existting_table = get_post_meta($post_id, 'wpc_table_name', true );
    if($table_name!=$existting_table){
       foreach ($table_keys_list as $columns) { $return_array[]=array($columns->Field,0,""); }

    } else {
        $wpc_columns_name = get_post_meta($post_id, 'wpc_columns_name', true );   
        
        $existing_columns_names = array_keys($wpc_columns_name);
        $existing_columns_values = array_values($wpc_columns_name);
        
        $n=0;
        foreach ($table_keys_list as $columns) {
            if(in_array($columns->Field,$existing_columns_names)){

                $return_array[]=array($columns->Field,1,$wpc_columns_name[$columns->Field]);
            } else{
                $return_array[]=array($columns->Field,0,"");
            }
            $n++;
        }
    }
    
    echo json_encode($return_array);
    die;
}

add_action("wp_ajax_get_primary_keys", "get_primary_keys");
function get_primary_keys(){
    global $wpdb;
    $table_name = $_REQUEST['table_name'];
    $table_keys_list = $wpdb->get_results("describe ".$table_name);
    $primary_keys_list = array();
    foreach ($table_keys_list as $table_keys) {
       if($table_keys->Key=="PRI"){
        $primary_keys_list[] = $table_keys->Field;
       }
    }
    echo json_encode($primary_keys_list);
    die();
}

add_action( 'save_post', 'wpc_save_post_meta_data', 10, 3 );
function wpc_save_post_meta_data( $post_ID, $post, $update ) {
  if($post->post_status=="publish" && get_post_type($post_ID)=="wp_custom_table"){
   $columns = array();
   foreach ($_POST['columns_name'] as $columns_key => $columns_value) {
       $columns[$columns_value[0]] =$_POST['column_title'][$columns_key][0];  
   }

    update_post_meta($post_ID, 'wpc_table_name', $_POST['wpc_tables']);
    update_post_meta($post_ID, 'wpc_columns_name', $columns);
  }
}

