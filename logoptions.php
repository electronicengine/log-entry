

    <?php                       
        $prefix = explode(":", $_POST["search"]);
        $delete_prefix = explode(":", $_POST["delete"]);

        if(count($prefix) == 2){
            makeSearchForm();
            makeSearchQuery($prefix);

        } else if($prefix[0] !== "") {
            makeSearchForm();
            if($prefix[0] == "1"){
                showLastRecords();
            } else{
                displayVariety($prefix[0]);
            }
        }


        if(count($delete_prefix) == 2)
            makeDeleteQuery($delete_prefix);


        function makeSearchForm(){
            ?>

                <form action="logoptions.php" method="post">
                <label for="fname">Search Prefix:</label>
                <input id='search' name='search' type='text' value='' />
                <label for="fname">Delete Prefix:</label>
                <input id='delete' name='delete' type='text' value='' />
                <input name="submit" class="button button-primary" type="submit" value="Get Result" />
            </form>
            <br>

            <?php
        }


        function displayVariety($prefix){
            
            $queryStr = "SELECT $prefix FROM `wp_entrylog` WHERE 1 ORDER BY date DESC LIMIT 3000;";
            $results = makeSqlOp($queryStr);
            $ar = array();
           
            foreach($results as $row)
            {
                array_push($ar, $row->$prefix);
            }
            
            makeArrayTable(array_unique($ar), $prefix);

        }

        function showLastRecords(){

            $queryStr = "SELECT * FROM `wp_entrylog` WHERE 1 ORDER BY id DESC LIMIT 3000;";
            $results = makeSqlOp($queryStr);
            makeWpResultTable($results);

        }


        function makeSearchQuery($prefix){

            global $tableColumns;

            $interwal_prefix = explode("-", $prefix[1]);

            if(count($interwal_prefix) == 2){
                $queryStr = "SELECT * FROM `wp_entrylog` WHERE $prefix[0] between '$interwal_prefix[0]' and '$interwal_prefix[1]';";
            }else{
                $queryStr = "SELECT * FROM `wp_entrylog` WHERE $prefix[0]='$prefix[1]' LIMIT 3000;";
            }
    
            $results = makeSqlOp($queryStr);
            makeWpResultTable($results);

        }


        function makeDeleteQuery($prefix){

            $interwal_prefix = explode("-", $prefix[1]);

            if(count($interwal_prefix) == 2){
                $queryStr = "DELETE FROM `wp_entrylog` WHERE $prefix[0] between '$interwal_prefix[0]' and '$interwal_prefix[1]';";
            }else{
                $queryStr = "DELETE FROM `wp_entrylog` WHERE $prefix[0]='$prefix[1]';";
            }

            makeSqlOp($queryStr);
            echo "Deletion is successful";
            makeSearchForm();
            showLastRecords();

        }


        function makeSqlOp($queryStr) {

            require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
            global $wpdb;
            $table_name = $wpdb->prefix. "entrylog";
            $results = $wpdb->get_results($queryStr);

            return $results;

        }


        function makeWpResultTable($Params)
        {
            ?>
            <table BORDER=”2″ CELLPADDING=”10″>
                <tr>
                    <th>type</th>
                    <th>duration</th>
                    <th>page</th>
                    <th>ip</th>
                    <th>device</th>
                    <th>country</th>
                    <th>city</th>
                    <th>time</th>
                    <th>date</th>
                </tr>
       
            <?php

            if(!empty($Params))                        // Checking if $results have some values or not
            {    
                foreach($Params as $row){  
                    ?>
                    <tr>
                        <td><?php echo $row->type; ?></td>
                        <td><?php echo $row->duration; ?></td>
                        <td><?php echo $row->page; ?></td>
                        <td><?php echo $row->ip; ?></td>
                        <td><?php echo $row->device; ?></td>
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


        function makeArrayTable($Params, $Column)
        {
            ?>
            <table BORDER=”2″ CELLPADDING=”10″>
                <tr>
                    <?php
                        echo '<th>' .$Column. '</th>';
                    ?>
                </tr>
            
            <?php

            if(!empty($Params))                        // Checking if $results have some values or not
            {    
                foreach($Params as $row){  
                    ?>
                    <tr>
                        <td><?php echo $row; ?></td>
                    </tr>
                    <?php
                }
        
            }

            ?></table>

            <?php
        }


        
        
        
    ?>

