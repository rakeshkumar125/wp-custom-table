<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = 'markers';

// Table's primary key
$primaryKey = 'id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
/*array(
		'db'        => 'start_date',
		'dt'        => 4,
		'formatter' => function( $d, $row ) {
			return date( 'jS M y', strtotime($d));
		}
	),
	array(
		'db'        => 'salary',
		'dt'        => 5,
		'formatter' => function( $d, $row ) {
			return '$'.number_format($d);
		}
	)*/
/*,'formatter'=>function($id, $row){ return "<input type='checkbox' name='delete_id[]' value='".$id."'>";}*/
	
$columns = array(
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

	);

// SQL server connection information
$sql_details = array(
	'user' => 'root',
	'pass' => '',
	'db'   => 'wordpress',
	'host' => 'localhost'
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);


