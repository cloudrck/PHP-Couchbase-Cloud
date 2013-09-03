<?php

    include 'includes.php';
    
    $txt = array();
    $txt[0] = "You have Signed_Up";
    $txt[1] = "Please Try Again";
    $txt[2] = "folder has been created";
    $txt[3] = "folder could not created";
    $txt[4] = "File has been uploaded";
    $txt[5] = "Please Try Again";
    $txt[6] = "File has been deleted";
    $txt[7] = "File could not deleted";
    $txt[8] = "File has been shared";
    $txt[9] = "Either fileid or user error";
    
    function sessionstart()
    {
        if(!isset($_SESSION))
        {
            session_start();
        }
    }
    
    function create_doc()
    {
        global $txt;
        
        $users = new Users();
    
        $user_data = array();
        $user_data['fname'] = $_POST['fname'];
        $user_data['lname'] = $_POST['lname'];
        $user_data['email'] = $_POST['email'];
        $user_data['password'] = $_POST['password'];
        $user_data['confirmpass'] = $_POST['repeatpass'];
    
        if($users->create_doc($user_data))
        {
            $response = array("status" => 1, "message" => $txt[0]);
            echo json_encode($response);
        }
        else
        {
            $response = array("status" => 0, "message" => $txt[1]);
            echo json_encode($response);
        }
    }
    
    function login()
    {
        session_start();
        global $txt;
        
        $users = new Users();
        
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $user_id = $users->user_login($email, $password);
        
        if($user_id !== FALSE)
        {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;
            $response = array("status" => 1, "message" => $txt[0]);
            echo json_encode($response);
        }
        else
        {
            $response = array("status" => 0, "message" => $txt[1]);
            echo json_encode($response);
        }
    }   
    
    function create_folder()
    {
        session_start();
        global $txt;
        
        $users = new Users();
        
        $folder_name = $_POST['foldername'];
        $parent_id = $_POST['parent'];
        $user_id = $_SESSION['user_id']; 
        
        //$users->createfolder($user_id,$parent_id,$folder_name);
        
        if($users->createfolder($user_id, $parent_id, $folder_name))
        {
            $response = array("status" => 1, "message" => $txt[2]);
            echo json_encode($response);
        }
        else
        {
            $response = array("status" => 0, "message" => $txt[3]);
            echo json_encode($response);
        }
                
    }
    
    function get_folders()
    {
        if(!isset($_SESSION))
            session_start();
        $user_id = $_SESSION['user_id'];
        
        $parent = 1;
        
        if(isset($_GET['folder']))
            $parent = $_GET['folder'];
        
        $sort = "date";
        
        if(isset($_GET['sort']))
            $sort = $_GET['sort'];
        
        $users = new Users();
        
        $folders = $users->get_folders($user_id, $parent, $sort);
        
        return $folders;
    }
    
    function get_files()
    {
        if(!isset($_SESSION))
            session_start();
        $user_id = $_SESSION['user_id'];
        
        $parent = 1;
        
        if(isset($_GET['folder']))
            $parent = $_GET['folder'];
        
        $sort = "date";
        
        if(isset($_GET['sort']))
            $sort = $_GET['sort'];
        
        $users = new Users();
        
        $files = $users->get_files($user_id, $parent, $sort);
        
        return $files;
    }
    
    function upload_file()
    {
        session_start();
        
        global $txt;
        $users = new Users();
        
        $file_data = array();
       
        $user_id = $_SESSION['user_id'];
        $folder_id = $_POST['folder_id'];
       
        $file_data['name'] = $_FILES["file"]["name"];
        $file_data['file_extension'] = get_extension($file_data['name']);
        
        $file_data['size'] = $_FILES["file"]["size"];
        $file_data['tags'] = $_POST['filetags'];
       
        
        $file_data['source'] = "/home/testdev/stored_files/".time().rand(100, 999).".".$file_data['file_extension'];
        
        move_uploaded_file($_FILES["file"]["tmp_name"], $file_data['source']);
        
        if($users->create_file($user_id, $folder_id, $file_data))
        {
            $response = array("status" => 1, "message" => $txt[4]);
            echo json_encode($response);
        }
        else
        {
            $response = array("status" => 0, "message" => $txt[5]);
            echo json_encode($response);
        }
     }
    
    function get_extension($name)
    {
        $name_array = explode(".", $name);
        
        $index = count($name_array) - 1;
        $ext  = $name_array[$index];
        return $ext;
    }
    
    function download_file()
    {
        session_start();
        
        $users = new Users();
        
        $user_id = $_SESSION['user_id'];
        
        $file_id = $_GET['file_id'];
        
        $file = $users->get_file($file_id, $user_id);
        
        if($file)
        {
           if (file_exists($file->source)) 
           {
               force_download($file->source, $file->name);
           }
        }else
        {
            header("HTTP/1.0 404 Not Found");
        }
    }
    
    function sharefile_file()
    {
       
        global  $txt;
        
        $users = new Users();
        
        $file_id = $_POST['file_id'];
       
        if($users->share_file($file_id))
        {
            $response = array("status" => 1, "message" => $txt[3]);
            echo json_encode($response); 
           
        }else
        {
            $response = array("status" => 0, "message" => $txt[3]);
            echo json_encode($response);  
        }
        
    }
    
    function force_download($file, $name)
    {
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$name);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
            exit;
    }
    
    function delete_folder()
    {
        session_start();
        global  $txt;
        
        $users = new Users();
        
        $user_id = $_SESSION['user_id'];
        $folder_id = $_POST['folder_id'];
        
        if($users->delete_folder($user_id, $folder_id))
        {
            $response = array("status" => 1, "message" => $txt[2]);
            echo json_encode($response);
        }
        else
        {
           $response = array("status" => 0, "message" => $txt[3]);
           echo json_encode($response);  
        }
        
        
    }
    
    function delete_file()
    {
        session_start();
        global  $txt;
        
        $users = new Users();
        
        $user_id = $_SESSION['user_id'];
        $folder_id = $_POST['folder_id'];
        $file_id = $_POST['file_id'];
        
        
        if($users->delete_file($user_id, $folder_id, $file_id))
        {
            $response = array("status" => 1, "message" => $txt[6]);
            echo json_encode($response);
        }
        else
        {
           $response = array("status" => 0, "message" => $txt[7]);
           echo json_encode($response);  
        }
    }
    
    function rename_folder()
    {
        session_start();
        global  $txt;
        
        $users = new Users();
        
        $user_id = $_SESSION['user_id'];
        $folder_id = $_POST['folder_id'];
        $folder_rename = $_POST['folder_rename'];
        
        if($users->newname_folder($user_id, $folder_id, $folder_rename))
        {
            $response = array("status" => 1, "message" => $txt[4]);
            echo json_encode($response);
        }
        else
        {
           $response = array("status" => 0, "message" => $txt[5]);
           echo json_encode($response);  
        }
        
        
    }
    
    function rename_file()
    {
        session_start();
        global  $txt;
        
        $users = new Users();
        
        $user_id = $_SESSION['user_id'];
        $file_id = $_POST['file_id'];
        $file_name = $_POST['file_rename'];
        
        if($users->rename_file($file_id, $file_name,$user_id))
        {
            $response = array("status" => 1, "message" => $txt[6]);
            echo json_encode($response);
        }
        else
        {
           $response = array("status" => 0, "message" => $txt[7]);
           echo json_encode($response);  
        }
        
    }
    
    function filedrag_drop()
    {
        global  $txt;
        
        $users = new Users();
        
        $folderdrop_id = $_POST['folderdrop_id'];
        $filedrag_id = $_POST['filedrag_id'];
        
        if($users->file_drag($folderdrop_id,$filedrag_id))
        {
            $response = array("status" => 1, "message" => $txt[6]);
            echo json_encode($response);
        }
        else
        {
           $response = array("status" => 0, "message" => $txt[7]);
           echo json_encode($response);  
        }
        
    }
    
    function user_share()
    {
        global  $txt;
        
        $users = new Users();
        
        $username = $_POST['user'];
        $permission = $_POST['permission'];
        $file_id = $_POST['fileshareid'];
      
        if($users->user_permission($username,$permission,$file_id))
        {
            $response = array("status" => 1, "message" => $txt[8]);
            echo json_encode($response);
        }
        else
        {
           $response = array("status" => 0, "message" => $txt[9]);
           echo json_encode($response);  
        }
        
    }
    
    function delete_sharefile()
    {
        global  $txt;
        $username = $_POST['email'];
        $permission = $_POST['permission'];
        $file_id = $_POST['file_id'];
        
        $users = new Users();
        if($users->remove_sharedfiles($username,$permission,$file_id))
        {
            $response = array("status" => 1, "message" => $txt[8]);
            echo json_encode($response);
        }
        else
        {
            $response = array("status" => 0, "message" => $txt[9]);
            echo json_encode($response);
        }
        
    }
    
    function user_files()
    {

        $users = new Users();
        
        $user_id = $_SESSION['user_id'];
        
        $sort = "date";
        if(isset($_GET['sort']))
        $sort = $_GET['sort'];
        
        $editfiles= $users->get_shared_files($user_id,$sort);
       
        return $editfiles;
        
        
    }
    
    function permission_files()
    {

        $users = new Users();
        
        $permission_id = $_POST['permission_id'];
            
        $files= $users->manage_files($permission_id);
        
        return json_encode($files);
        
        
    }
    
    function user_name()
    {
        $users = new Users();
        
        $user_id = $_SESSION['user_id'];
        
        $username= $users->get_username($user_id);
        
        return $username;
        
    }
    
    
    
    if (isset($_POST['action'], $_POST['fname'],$_POST['lname'], $_POST['email'],$_POST['password'],$_POST['repeatpass'] ) && $_POST['action'] == "signup_users")
    {
        echo create_doc();
        
    }
    
    if (isset($_POST['action'],$_POST['email'],$_POST['password']) && $_POST['action'] == "login_users")
    {
        echo login();
    }
    
    if (isset($_POST['action'],$_POST['foldername']) && $_POST['action'] == "folder_create")
    {
        echo create_folder();
    }
    
   
    if (isset($_POST['action'],$_FILES['file'], $_POST['folder_id'],$_POST['filetags']) && $_POST['action'] == "create_file")
    {
        echo upload_file();
    }
    
    if(isset($_POST['action'],$_POST['folder_id']) && $_POST['action'] == "delete_folder")
    {
        echo delete_folder();
        
    }
    
    if (isset($_POST['action'],$_POST['folder_id'],$_POST['file_id']) && $_POST['action'] == "delete_file")
    {
        echo delete_file();
    }
    
    if (isset($_POST['action'],$_POST['folder_id'],$_POST['folder_rename']) && $_POST['action'] == "folder_rename")
    {
        echo rename_folder();
    }
    
    if (isset($_POST['action'],$_POST['file_id'], $_POST['file_rename']) && $_POST['action'] == "file_rename")
    {
        echo rename_file();
    }
    
    if (isset($_POST['action'],$_POST['folderdrop_id'], $_POST['filedrag_id']) && $_POST['action'] == "file_drop")
    {
        echo filedrag_drop();
    }
    
    if (isset($_POST['action'],$_POST['file_id']) && $_POST['action'] == "share_file")
    {
        echo sharefile_file();
    }
    
    if(isset($_POST['action'],$_POST['user'],$_POST['permission'],$_POST['fileshareid']) && $_POST['action'] == "user_share")
    {
        echo user_share();
    }
    
    if (isset($_GET['action'],$_GET['file_id']) && $_GET['action'] == "usershare_file")
    {
        echo download_usershare();
    }
    
    if (isset($_POST['action'],$_POST['permission_id']) && $_POST['action'] == "permission_file")
    {
        echo permission_files();
       
    }
    
    if(isset($_POST['action'],$_POST['email'],$_POST['permission'],$_POST['file_id']) && $_POST['action'] == "userdel_share")
    {
        echo delete_sharefile();
    }
    
    
?>
