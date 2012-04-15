<?php
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
        "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <title>Crud Example</title>
    <style type="text/css">
    table{
        width: 100%;
        border: 1px solid blue;
    }
    tr {
        border: 1px solid green;
    }
    th {
        background-color: #CCC;
        text-align: center;
    }
    td {
        background-color: white;
        text-align: center;
        padding: 5px;
    }
    #message {
        background-color: orange;
        border: 1px solid red;
        padding: 15px;
    }
    </style>
</head>
<body>
    <h1>Crud Example</h1>
    <?php
    if (!empty($message)){
        echo '<div id="message"><p>'.$message.'</p></div>';
    }
    ?>
    <ul id="options">
        <li><a href="<?php echo base_url(); ?>index.php/example/create_table">Make Database Table</a>: Requires you to setup the database paramaters in "/system/application/config/database.php"</li>
        <li><a href="<?php echo base_url(); ?>index.php/example/create">Create New Entry</a>: This will attempt to generate a new entry, but will check for duplicates.  So if it comes back and says a duplicate is found. Click it agian.</li>
        <li><a href="<?php echo base_url(); ?>">Just show the table</a></li>
    </ul>
    <?php
    if (empty($table_list))
    {
        echo '<h2>The database table is empty.  Please click on Create Individual record.</h2>';
    } else {
        echo '
        <table>
        <tr>
            <th>options</th>
            <th>blog_id</th>
            <th>date</th>
            <th>title</th>
            <th>content</th>
        </tr>';
        foreach($table_list as $value)
        {
            echo '
        <tr>
            <td><a href="'. base_url() .'index.php/example/delete/'.$value['blog_id'].'">delete</a> | <a href="'. base_url() .'index.php/example/update/'.$value['blog_id'].'">edit</a> | <a href="'. base_url() .'index.php/example/retrieve/'.$value['blog_id'].'">view</a></td>
            <td>'.$value['blog_id'].'</td>
            <td>' . date("F j, g:i a", $value['date']) . '</td>
            <td>' . $value['title'] . '</td>
            <td>' . $value['content'] . '</td>
        </tr>';
        }
        echo '</table>';
    }
    echo '<p>The total number of entries in the database is: '. $total .'</p>';
    ?>
    
<hr />
<h3>How to use this example:</h3>
<ol id="howto">
    <li>Setup your website with the base code igniter install.</li>
    <li>Setup your database and create a database with no tables.</li>
    <li>Configure your code igniter install to work with your database. (via '/system/application/config/database.php')</li>
    <li>To access this help, goto "http://{base_url}/example/" .  Replacing {base_url} with your base url used in the Codeigniter config file.</li>
    <li>Click on the Make Database Table Link at the top of the page.  This will fill in the structure of your table.</li>
    <li>Click on the Create new entry link at the top of the page and this will start to put generic data into your table. (the more you click on it the more entries it will create..)</li>
    <li>Thenk you can edit/delete/view your entries.</li>
    <li>You can look at the code in examples.php ('/system/application/controllers/example.php') This should show you the basics of how to use the crud library.</li>
</ol>
</body>
</html>