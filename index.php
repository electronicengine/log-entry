<?php

/**
 * Plugin Name: Log Entry
 * Description: Loging the user ip info instantly
 * Author: Yusuf Bulbul
 * Version: 0.1
 * 
 */

add_action('template_redirect', 'pageLoad');
function pageLoad(){

    $title = get_the_title();
    $home = is_home();

    if($home == "1")
        $title = "main";

    logUserEntryInfo($title);

}

function addMenu()
{
    add_menu_page("Log Entry", "Log Entry", 4 ,"log-entry", "logEntryMenu");
}
add_action("admin_menu", "addMenu");


function log_plugin_activation() {

    createTables();
}
register_activation_hook(__FILE__, 'log_plugin_activation');


 function logEntryMenu()
 {
    require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
    global $wpdb;
    $table_name = $wpdb->prefix. "entrylog";
    $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY id DESC LIMIT 3000;" );

    ?>
    <form action="../wp-content/plugins/log-entry/logoptions.php" method="post">
        <input id='search' name='search' type='text' value='search prefix' />
        <input id='delete' name='delete' type='text' value='delete prefix' />
        <input name="submit" class="button button-primary" type="submit" value="Get Result" />
    </form>
    <table BORDER=”2″ CELLPADDING=”10″>
        <tr>
            <th>type</th>
            <th>duration</th>
            <th>page</th>
            <th>ip</th>
            <th>country</th>
            <th>city</th>
            <th>time</th>
            <th>date</th>
        </tr>
       
    <?php

    if(!empty($results))                        // Checking if $results have some values or not
    {    
        foreach($results as $row){  
            ?>
            <tr>
                <td><?php echo $row->type; ?></td>
                <td><?php echo $row->duration; ?></td>
                <td><?php echo $row->page; ?></td>
                <td><?php echo $row->ip; ?></td>
                <td><?php echo $row->country; ?></td>
                <td><?php echo $row->city; ?></td>
                <td><?php echo $row->time; ?></td>
                <td><?php echo $row->date; ?></td>
            </tr>
            <?php
        }
   
    }

    ?></table>
    

    <?php




 }

 

 function createTables()
 {
    require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');

    global $wpdb;
    $table_name = $wpdb->prefix. "entrylog";
    global $charset_collate;
    $charset_collate = $wpdb->get_charset_collate();
    global $db_version;

    if( $wpdb->get_var("SHOW TABLES LIKE '" . $table_name . "'") !=  $table_name){   
        $create_sql = "CREATE TABLE " . $table_name . " (
            id INT(11) NOT NULL auto_increment,
            type INT(4) NOT NULL ,
            duration INT(10) NOT NULL,
            page VARCHAR(40) NOT NULL,
            ip VARCHAR(15) NOT NULL,
            country VARCHAR(15) NOT NULL ,
            city VARCHAR(15) NOT NULL,
            date VARCHAR(15) NOT NULL,
            time VARCHAR(15) NOT NULL default '0',
            PRIMARY KEY (id))$charset_collate;";
    }

    require_once($_SERVER['DOCUMENT_ROOT'].'/wp-admin/includes/upgrade.php' );
    dbDelta( $create_sql );

    //register the new table with the wpdb object
    if (!isset($wpdb->entrylog))
    {
        $wpdb->entrylog = $table_name;
        //add the shortcut so you can use $wpdb->stats
        $wpdb->tables[] = str_replace($wpdb->prefix, '', $table_name);
    }
 

 }



function logUserEntryInfo($title) {


	if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		//check ip from share internet
		$ip = $_SERVER['HTTP_CLIENT_IP'];
	} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		//to check ip is pass from proxy
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}

	$filtered = apply_filters( 'wpb_get_ip', $ip );
	logTheInfo($filtered, $title);
		
}


function logTheInfo($ip, $name)
{
    $type = "close";
    $duration = 123;
	$country =  ipLocInfo("Visitor", "Country");
	$city = ipLocInfo("Visitor", "City"); 
	$date = date("Y/m/d");
	$time = date("h:i:sa");
    $unixTime = time();
    
    ?><script>

        var type = "<?php echo $type; ?>";
        var duration = 0;
        var ip = "<?php echo $ip; ?>";
        var name = "<?php echo $name; ?>";
        var country = "<?php echo $country ?>";
        var city = "<?php echo $city ?>";
        var date = "<?php echo $date ?>";
        var time = "<?php echo $time ?>";
        var unixtime = <?php echo $unixTime; ?>;
        var url_post = "wp-content/themes/vilva/tracking.php";
        var log_saved = false;

        if(name == "main"){
            url_post = "wp-content/plugins/log-entry/tracking.php";
        } else{
            url_post = "../../../../wp-content/plugins/log-entry/tracking.php";
        }

        var t = setTimeout(timerFunc, 1000)

        window.onbeforeunload = function () {
            type = "close";
            sendPostReq();
        };

        function timerFunc()
        {
            duration++;
            setTimeout(timerFunc, 1000);

            if(duration > 2 && log_saved == false){
                type = "start";
                sendPostReq();
                log_saved = true;
            }

        }


        function sendPostReq()
        {

            jQuery.ajax({
                type: "POST",
                url: url_post,
                dataType: 'json',
                data: {Type: type, Duration: duration, Ip: ip, Name: name, Country: country, City: city, Data: date, Time: time, UnixTime: unixtime },

                success: function (obj, textstatus) {
                  if( !('error' in obj) ) {
                  }
                  else {
                      console.log(obj.error);
                  }
                }
            });

        }
        
    </script><?php
}

function ipLocInfo($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}



