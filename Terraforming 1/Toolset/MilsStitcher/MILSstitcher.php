<?php

	// LDR Part spec:  1 <colour> x y z a b c d e f g h i <file>

	
	echo("Please enter path to the directory containing MILS tiles: ");
	
	$handle = fopen ("php://stdin","r");
	$line = fgets($handle);
	
	$path_parts = pathinfo($line);
		
	if ( is_dir($path_parts['dirname']) ) {
		
    	echo("\nThe path ".$path_parts['dirname']."/ exists\n\n");
    	
    	// Get a List of all Tiles A1 to Z99
    	$map = get_tile_list( $path_parts['dirname'] );
    	
    	print_r($map);
	
		// Calculate a shift-Matrix upon Tile-List
    	$shift_matrix = calculate_shift($map); 
    	
    	// Create MPD-File accourding MILS-Tilelist & Shifting Matrix    	
    	create_milsboard( $path_parts['dirname'], $map, $shift_matrix );
    	
	} else {
    	echo "The path ".$path_parts['dirname']." does not exist\n";
	}
	
	fclose($handle);
	



/**
*
*
*	Major Functions
*
*
*/	
	
/**
 * get_tile_list function.
 * 
 * @access public
 * @param string $mils_dir
 * @return void
 */
function get_tile_list ( string $mils_dir ) {
	
	// extracts unsorted list of MILS-Tiles from $mils_dir
	$ldr_files = path_actions( $mils_dir );
	
	if ( $ldr_files) {
		
		// Splices unsorted list of MILS-Tiles into arrays containing Filedname and Number
		$ldr_files = splice_tilelist( $ldr_files );
		
		//$ldr_files = array_map('preformat', $ldr_files);
		
		// Sort list of MILS-Tiles
		$ldr_files = presort($ldr_files);
		
		
		// Get the x & y dimentions of the MILS-Board
		$field_size = get_dimentions ( $ldr_files );
		
		if (!empty($ldr_files)) {
	
			echo( "Your area consists of: ".$field_size['x']."x".$field_size['y']." MILS Modules!\n" );
		
		} else {
			echo ( "This directory does not contain MILS Modules!\n" );
		}
	
	    echo("\nTile-Map extracted... \n");
    	print_r($ldr_files);
	
		return $ldr_files;
	}
}

/**
 * calculate_shift function.
 *
 * Calculates pivot-element of a MILS-board and the relative positions of all tiles arround it.
 *
 * @access public
 * @param mixed $target_map
 * @return void
 */
function calculate_shift ( $target_map ) {
	
	$matrix_size = get_dimentions ( $target_map );
	
	echo("\nInitial Matrix: \n");
	display_matrix ( $target_map, $matrix_size );
	
	$pivot_field = find_pivot($target_map);
		
	foreach ( $target_map as &$map_field) {
			
		echo( "Old Field Value: [".$map_field['x'].", ".$map_field['y']."]\n");
		
		$map_field['x'] = pivot_step( $map_field['x'], $pivot_field['x']);
		$map_field['y'] = pivot_step( $map_field['y'], $pivot_field['y']);
		
		echo( "New Field Value: [".$map_field['x'].", ".$map_field['y']."]\n\n");
		
	}
	
	echo("\nPivot Multiplyer Matrix: \n");
	display_matrix ( $target_map, $matrix_size );

	return $target_map;
}

/**
 * create_milsboard function.
 *
 * Creates .mpd-File of all tiles with shifted position and .ldr-Data
 * 
 * @access public
 * @param mixed &$targetpath
 * @param mixed &$matrix
 * @return void
 */
function create_milsboard( &$targetpath, &$map, &$matrix) {
	
	$shift_list = recombine($map);
	
//	echo("\n\nModule-List: \n");
//	print_r( $shift_list );
//	
//	echo("\n\nMatrix-List: \n");
//	print_r( $matrix );
	
	$milsboard = basename( $targetpath);
	$newfile = $targetpath."/".$milsboard.".mpd";
	
	echo("\nNew File - Path: ".$newfile."\n");
		
	$mpd_file_handle = fopen($newfile, 'w') or die('Cannot create file:  '.$milsboard); //implicitly creates file
	
	$header_lines = "0 FILE ".$milsboard.".ldr\n".
					"0 ".$milsboard." Complete\n".
					"0 Name: ".$milsboard.".ldr\n".
					"0 Author: MILSgen & MILSStitcher\n\n";
					
	fwrite($mpd_file_handle, $header_lines);
		
	foreach ( $shift_list as $key => $fieldname ) {
		
		position_n_shift( $mpd_file_handle, $targetpath, $fieldname, $matrix[$key] );
			
	}
	
	fwrite($mpd_file_handle, "\n");
	
	foreach ( $shift_list as $key => $fieldname ) {
		
		// echo("\n\n".$fieldname." - Field No.".$key." Operation: [".$matrix[$key]['x'].",".$matrix[$key]['y']."]\n");
			
		open_n_append( $mpd_file_handle, $targetpath, $fieldname, $matrix[$key] );
		
	}
	
	fclose($mpd_file_handle);
	
	echo("\nStitching complete!\n\n");
	
};
	




/**
*
*
*	Helper Functions
*
*
*/	

/**
 *
 * path_actions function.
 *
 * Extracts unsorted Array-List of MILS-Tiles from $mils_dir
 * 
 * @access public
 * @param mixed &$workingpath
 * @return array - unsorted Array with all MILS-Modules in $workingpath
 *
 */
function path_actions ( &$workingpath ) {
	
	$objects_in_dir = array_slice(scandir($workingpath), 2);
	
	$ldr_files = glob($workingpath.'/*.ldr');
	
	array_walk($ldr_files, 'strip_path');
	
	if ( empty($ldr_files) ) {
		echo "The directory does not contain LDR-files!\n";
		
		return false;
		
	} else {
		$ldr_files = array_values(array_filter($ldr_files, 'choose_mils_files'));
		
		echo("\nModule-List found! \n");
		
		return $ldr_files;
	}
	
}

/**
 * strip_path function.
 * 
 * Strips the path of a File and returns Filename
 *
 * @access public
 * @param mixed &$targetpath
 * @return void
 */
function strip_path ( &$targetpath ) {
	return $targetpath = pathinfo($targetpath, PATHINFO_FILENAME);
}
	
/**
 * choose_mils function.
 * 
 * Returns a List containing only Names matching a Pattern from A1 to Z99
 *
 * @access public
 * @param string $candidate
 * @return void
 *
 */
function choose_mils_files (string $candidate ) {

	if ( preg_match('/\b[A-Z]{1}[0-9]{1,2}\b/i', $candidate) ) {			
		return $candidate;	
	}
}

/**
 * splice_tilelist function.
 * 
 * Splices a array of strings into an array of arrays of 1-character strings and n-character strings - [A,12]
 *
 * @access public
 * @param mixed &$tilelist
 * @return void
 */
function splice_tilelist ( $tilelist ) {
	
	$alphabet = range('A', 'Z');
	
	foreach ( $tilelist as $key => $tilestring ) {
		$tilelist[$key] = array( "x" => substr($tilestring, 0, 1), "y" => intval(substr($tilestring, 1)) );
	}
	
	echo("\nTilelist spliced! \n");

	return $tilelist;
	
}

/**
 * presort function.
 *
 * Sorts a two-dimentional array in the form of array('x' => 'somevalue', 'y' => 'somevalue)
 * 
 * @access public
 * @param mixed &$data
 * @return void
 *
 */
function presort ( &$data ) {

//	echo("UNSORTED:\n");
//	print_r($data);

	$xvalue  = array_column($data, 'x');
	$yvalue = array_column($data, 'y');
	
	usort($data, make_comparer('x','y'));
	
//	echo("\nSORTED:\n\n");
//	print_r($data);
	
	return $data;
	
}

/**
 * get_dimentions function.
 *
 * Returns the Dimentions af a List of MILS-Boardtiles by lookong at the highest boardtile
 * 
 * @access public
 * @param array $data
 * @return void
 */
function get_dimentions ( array $data ) {
	
	$size = end($data);
	
	if ( !is_numeric($size['x']) ) {
		
		$alphabet = range('A', 'Z');
		$size['x'] = array_search($size['x'], $alphabet);
	}
	
	$size['x'] = $size['x'] + 1;
	
	return $size;
	
}

/**
 * make_comparer function.
 *
 * returns comparing function for usort()
 * 
 * @access public
 * @return void
 */
function make_comparer () {
    // Normalize criteria up front so that the comparer finds everything tidy
    $criteria = func_get_args();
    foreach ($criteria as $index => $criterion) {
        $criteria[$index] = is_array($criterion)
            ? array_pad($criterion, 3, null)
            : array($criterion, SORT_ASC, null);
    }

    return function($first, $second) use (&$criteria) {
        foreach ($criteria as $criterion) {
            // How will we compare this round?
            list($column, $sortOrder, $projection) = $criterion;
            $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;

            // If a projection was defined project the values now
            if ($projection) {
                $lhs = call_user_func($projection, $first[$column]);
                $rhs = call_user_func($projection, $second[$column]);
            }
            else {
                $lhs = $first[$column];
                $rhs = $second[$column];
            }

            // Do the actual comparison; do not return if equal
            if ($lhs < $rhs) {
                return -1 * $sortOrder;
            }
            else if ($lhs > $rhs) {
                return 1 * $sortOrder;
            }
        }

        return 0; // tiebreakers exhausted, so $first == $second
    };
}

/**
 * find_pivot function.
 *
 * Finds Pivot in board-array
 * 
 * @access public
 * @param mixed $pivot_array
 * @return void
 *
 */
function find_pivot ( $pivot_array) {
	
	$pivot_element = get_dimentions ( $pivot_array );
	
	$pivot_element['x'] = floor( $pivot_element['x']/2 ) - 1;
	$pivot_element['y'] = floor( $pivot_element['y']/2 );
	
	$alphabet = range('A', 'Z');
	
	echo ( "\nFound Pivot Field at: [".$alphabet[$pivot_element['x']].", ".$pivot_element['y']."]\n\n" );
	
	return $pivot_element;
	
}

/**
 * pivot_step function.
 *
 * Returns relative position of $field_pos to $pivot_pos
 * 
 * @access public
 * @param mixed $field
 * @param mixed $pivot
 * @return void
 */
function pivot_step ( $field_pos, $pivot_pos ) {

	if (!is_numeric($field_pos)) {
			$alphabet = range('A', 'Z');
			$field_pos = array_search($field_pos, $alphabet);
	}
	
	$step = 0;
	
	if ( $field_pos == $pivot_pos ) {
		$step = 0;
	} else {
		$step = $field_pos - $pivot_pos;
	}
	return $step;
}

/**
 * position_n_shift function.
 * 
 * @access public
 * @param mixed $file
 * @param mixed $op_path
 * @param mixed $op_field
 * @param mixed $op_matrix
 * @return void
 */
function position_n_shift ( $file, $op_path, $op_field, $op_matrix ) {
	
	$op_file_path = $op_path."/".$op_field.".ldr";
	
	$temp_file = fopen($op_file_path, 'r') or die("Cannot open file:  ".$op_file_path."\n");
	
	$op_data = file($op_file_path, FILE_IGNORE_NEW_LINES);
	
	$headline_count = count_headerlines($op_data);

	$op_data = array_slice($op_data, $headline_count);
	
	// LDR Part spec:  1 0 x y z a b c d e f g h i <file>
	
	$list_item = "1 0 ".(640 * intval($op_matrix['x']))." 0 ".(-640 * intval($op_matrix['y']))." 1 0 0 0 1 0 0 0 1 ".$op_field.".ldr\n";
	
	echo("Listing ".$op_field." with Matrix-Shift: [".$op_matrix['x'].",".$op_matrix['y']."] ==> ".$list_item);
	
	fwrite($file, $list_item);
	
}

/**
 * count_headerlines function.
 * 
 * @access public
 * @param mixed $file_array
 * @return void
 */
function count_headerlines ( $file_array ) {
	
	$headlines = 0;
	
	foreach ( $file_array as $key => $payload ) {
		
//		echo( "Payload in Line ".$key." : ".$payload."\n" );
//		echo( "First Substring: ".gettype($payload)." - ".intval($payload[0])."\n" );
		
		
		if ( intval($payload[0]) == 0 && strlen(substr($payload, 1)) > 0 ) {
			
//			echo( "Payload in Line ".$key." : ".$payload[0]." with ".strlen(substr($payload, 1))." characters in string\n" );
			
			$headlines++;
//		} else {
			
//			echo( "No payload in Line ".$key."\n" );
			
		}
		
	}
	
	return $headlines;
	
}

/**
 * open_n_append function.
 * 
 * @access public
 * @param mixed $file
 * @param mixed $op_path
 * @param mixed $op_field
 * @param mixed $op_matrix
 * @return void
 */
function open_n_append ( $file, $op_path, $op_field, $op_matrix ) {
		
 	// echo("Opening Tile ".$op_field." in ".$op_path."/ and appending.\n");
	
	$op_file_path = $op_path."/".$op_field.".ldr";
	
	$temp_file = fopen($op_file_path, 'r') or die("Cannot open file:  ".$op_file_path."\n");
	
	$op_data = file($op_file_path, FILE_IGNORE_NEW_LINES);
	
	$headline_count = count_headerlines($op_data);
	
//	echo( "There are: ".$headline_count." header lines.\n" );

	$op_data = array_slice($op_data, $headline_count);
	
	$header_lines = array("0 FILE ".$op_field.".ldr",
					"0 ".$op_field,
					"0 Name: ".$op_field.".ldr",
					"0 Author: MILSgen & MILSStitcher\n");
	
	$op_data = array_merge($header_lines, $op_data);
	
	$op_data = implode("\n", $op_data)."\n\n";
	
	fwrite($file, $op_data);

	
}

/**
 * recombine function.
 * 
 * @access public
 * @param mixed $tilelist
 * @return void
 */
function recombine ( $tilelist ) {
	
	$recombined_tiles = array();
	
	foreach ($tilelist as $key => $tile ) {
		
		array_push($recombined_tiles, $tile['x'].$tile['y']);
		
	}
	
	return $recombined_tiles;
	
}



/**
*
*
*	Display Functions
*
*
*/	

/**
 * display_matrix function.
 * 
 * @access public
 * @param mixed $display_array
 * @param mixed $display_size
 * @return void
 */
function display_matrix ( $display_array, $display_size) {
	
//	echo("Size of array to shift: ".count($display_array)." elements in a ".$display_size['x']."x".$display_size['y']." Grid\n\n");
			
	$count = 0;
		
	for ( $row = 0; $row < $display_size['x']; $row++ ) {
		
		for ( $col = 0; $col < $display_size['y']; $col++ ) {
			
			echo( "[".( $display_array[$count]['x'] /*+1*/ ).",".$display_array[$count]['y']."] " ); 
			$count++;
		}
	
	echo("\n");
	
	}
	
}

?>