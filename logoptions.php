<html>
<body style="background-color: black; color:white">
    <?php

        $prefix = explode(":", $_POST["search"]);
        $delete_prefix = explode(":", $_POST["delete"]);

        if(count($prefix) == 2)
            makeTable($prefix);

        if(count($delete_prefix) == 2)
            deleteTheKey($delete_prefix);


        function deleteTheKey($delete_prefix){
            
            
            require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
            global $wpdb;
            $table_name = $wpdb->prefix. "entrylog";
            $results = $wpdb->get_results( "DELETE FROM $table_name WHERE $delete_prefix[0]='$delete_prefix[1]';" );

            echo "deletion is succesful!!!";

            ?>
                <form action="logoptions.php" method="post">
                    <input id='search' name='search' type='text' value='search prefix' />
                    <input id='delete' name='delete' type='text' value='delete prefix' />
                    <input name="submit" class="button button-primary" type="submit" value="Get Result" />
                </form>
            <?php
        }

        function makeTable($prefix)
        {
            require_once($_SERVER['DOCUMENT_ROOT'].'/wp-load.php');
            global $wpdb;
            $table_name = $wpdb->prefix. "entrylog";
            $results = $wpdb->get_results( "SELECT * FROM `wp_entrylog` WHERE $prefix[0]='$prefix[1]' LIMIT 3000;" );

            ?>
                        
            <form action="logoptions.php" method="post">
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


        
        
        
    ?>
</body>
</html>


