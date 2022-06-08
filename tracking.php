/*****************************************************************************
 *
 * Copyright(c) 2022, Yusuf Bülbül. All rights reserved.
 *           http://www.yusufbulbul.com  mailto: mail@yusufbulbul.com
 * --------------------------------------------------------------------------
 * This file is part of log-entry plugin, which is free software. You may redistribute
 * and/or modify it under the terms of the GNU General Public License,
 * version 3 or later, as published by the Free Software Foundation.
 *      log-entry is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY, not even the implied warranty of MERCHANTABILITY.
 * See the GNU General Public License for specific details.
 *      By using log-entry, you warrant that you have read, understood and
 * agreed to these terms and conditions, and that you possess the legal
 * right and ability to enter into this agreement and to use log-entry
 * in accordance with it.
 *      Your log-entry.zip distribution file should contain the file COPYING,
 * an ascii text copy of the GNU General Public License, version 3.
 * If not, point your browser to  http://www.gnu.org/licenses/
 * or write to the Free Software Foundation, Inc.,
 * 59 Temple Place, Suite 330,  Boston, MA 02111-1307 USA.
 * --------------------------------------------------------------------------
 * etc 
 */
 
<?php

header('Content-Type: application/json');

require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

writeToDatabase($_POST['Type'], $_POST['Ip'],  $_POST['Name'],  $_POST['Duration'], $_POST['Country'], $_POST['City'], $_POST['Device']);


function writeToFile($type, $ip, $name, $duration, $country, $city)
{
	$date = date("Y/m/d");
	$time = date("h:i:sa");
	
	$file = fopen("ips.txt", "a");      
	fwrite($file, $type);
	fwrite($file, "\t");
	fwrite($file, $duration);
	fwrite($file, "\t");
	fwrite($file, $name);
	fwrite($file, "\t");
	fwrite($file, $ip);
    fwrite($file, "\t");
	fwrite($file, $country);
	fwrite($file, "\t");
	fwrite($file, $city);
	fwrite($file, "\t");
	fwrite($file, $date);
	fwrite($file, "\t");
	fwrite($file, $time);
	
	fwrite($file, "\n");
	

	fclose($file);

}



function writeToDatabase($type, $ip, $name, $duration, $country, $city, $device)
{
	$date = date("Y/m/d");
	$time = date("h:i:sa");
	
    global $wpdb;
    $table_name = $wpdb->prefix. "entrylog";
	global $charset_collate;
	$charset_collate = $wpdb->get_charset_collate();
	global $db_version;


	$wpdb->insert($table_name, array(
		'id' => NULL,
		'type' => $type,
		'duration' => $duration,
		'page' => utf8_encode($name),
		'ip' => utf8_encode($ip),
		'device' => utf8_encode($device),
		'country' => utf8_encode($country),
		'city' => utf8_encode($city),
		'date' => utf8_encode($date),
		'time' => utf8_encode($time),
	));
}



?>
