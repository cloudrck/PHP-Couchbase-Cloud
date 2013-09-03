<?php    
if(isset($_GET['file_id']))
{
    include 'couch/users.php';
    download_file();
}
?>