<?php

error_reporting(E_ALL);
//enable the all error

ini_set("display_errors", 1);
date_default_timezone_set('America/Los_Angeles');

//display error and time zone

define('KEY_DELIM',"_");

$hosts = "dev1.cloudrck.net";


define('COUCHBASE_USER','admin');
define('COUCHBASE_PASS','TheMan!)');
define('COUCHBASE_BUCKET','default');


/**
* Singleton Couchbase class
* This keeps a single global copy of the Couchbase
* Couchbase connection.
*/

class CouchbaseSingleton{
    private static $instance;
    private static $cb_obj;

    /**
    * Construct the object
    */
    private function __construct() {
    }

    /**
    * Initialize this class after construction
    */
    private function initialize(){
        
        global $hosts;
        self::$cb_obj = new Couchbase($hosts, COUCHBASE_USER, COUCHBASE_PASS, COUCHBASE_BUCKET, FALSE) or die("error in connecting");
    }

    /**
    * Return the singleton instance, constructing and
    * and initializing it if it doesn't already exist
    */
    public static function getInstance() {
        if(!self::$instance) {
            self::$instance = new self();
            self::$instance->initialize();
        }
        return self::$instance;
    }

    /**
    * Return the Couchbase object held by the singleton
    */
    public static function getCouchbase(){
        return(self::$cb_obj);
    }
}
/*End of Couchbase of connection class*/

class Users
{
    public function __construct()
    {
        $this->cb = CouchbaseSingleton::getInstance() -> getCouchbase();
    }
    
    public function get_doc($user_id)
    {
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        return $doc;
    }
    
    public function create_doc($user_data)
    {
        
        $user_id = (int)$this->cb->get('users_last_id') + 1;
        
    $doc_userid = <<<EOF
            {	
                "object_type": "",
                "object_id": "",
                "firstname": "",
                "lastname": "",
                "email": "",
                "password": "",
                "confirm_pass": "",
                "active": "",
                "allocated_space": "",
                "used_space": "",
                "free_space": "",
                "create_date": "",
                "update_time": "",
                "folders" : [
                    {
                        "folder_id": "1",
                        "folder_parent": "",
                        "folder_name": "root",
                        "files": []
                    }
                ],
                "shared_files": [],
                "last_folder_id": ""
			
             }
EOF;
   
        $doc_sign = json_decode($doc_userid);
        
        $doc_sign->object_type = "users";
        $doc_sign->object_id = $user_id;
        $doc_sign->firstname = $user_data['fname'];
        $doc_sign->lastname =  $user_data['lname'];
        $doc_sign->email =  $user_data['email'];
        $doc_sign->password =  $user_data['password'];
        $doc_sign->confirm_pass =  $user_data['confirmpass'];
        $doc_sign->active = "active";
        $doc_sign->allocated_space = "2GB";
        $doc_sign->used_space = '0';
        $doc_sign->free_space = '2GB';
        $doc_sign->last_folder_id = "1";
        $doc_sign->create_time = date("c");
        $doc_sign->update_time = date("c");
        
        //get uersall data from couchbase
        $doc_usersall = json_decode($this->cb->get('users_all'));
        
        //$alluser = array("user_id"=>$user_id, "email"=>$user_data['email'], "password"=>$user_data['password']);
        $usersobj  = new stdClass();
        
        $usersobj->user_id = $user_id;
        $usersobj->email = $user_data['email'];
        $usersobj->password = $user_data['password'];
        //array_push($doc_usersall->users, $alluser);
        
        //print_r($usersobj);
        //get array of all users from couchbase
        $user = &$doc_usersall->users;
        
        $index = FALSE;
        
        foreach ($user as $key => $value)
        {
            if($value->email == $user_data['email'])
            {
               $index = $key;
               break;
            }
        }
        
        if($index === FALSE)
        {
            array_push($doc_usersall->users, $usersobj);
            $doc_key = $this->get_doc_key($user_id);
            $this->cb->set('users_last_id', $user_id);
            $this->cb->set($doc_key, json_encode($doc_sign));
            $this->cb->set('users_all', json_encode($doc_usersall));
            
            return TRUE;
        }
        
        return FALSE;
    }
    
    public function user_login($email,$password)
    {
        $doc_usersall = json_decode($this->cb->get('users_all'));
        $user = &$doc_usersall->users;
        
        $user_id = FALSE;
        
        foreach($user as $key => $value) 
        {
            if($value->email == $email && $value->password == $password)
            {
                $user_id = $doc_usersall->users[$key]->user_id;
                return $user_id;
            }
        }
        
        return FALSE;
    }
    
    public function createfolder($user_id,$parent_id,$folder_name)
    {
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        $last_folder_id = $doc->last_folder_id;
        $folders = $doc->folders;
        
        $last_folder_id = (int)$last_folder_id + 1;
        
        $folder = new stdClass();
        
        $folder->folder_id = $last_folder_id;
        $folder->folder_parent = $parent_id;
        $folder->folder_name = $folder_name;
        $folder->create_time = date("c");
        $folder->files = [];
        
        $doc->last_folder_id = $last_folder_id;
        
        
        $exists = FALSE;
        
        foreach ($folders as $key => $value) 
        {
            
            if($value->folder_name == $folder_name)
            {
                $exists = TRUE;
                break;
            }
            
        }
        
        if($exists === FALSE)
        {
            array_push($doc->folders,$folder);
            return $this->cb->set($doc_key,json_encode($doc));
        }else
        {
            return FALSE;
        }
     }
     
     public function get_file($file_id, $user_id)
     {
         // get user_id relevant document
        $doc_key = $this->get_file_key($file_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        if($doc->from->id == $user_id)
        {
            return $doc;
        }else if(in_array($user_id, $doc->viewers) || in_array($user_id, $doc->editors))
        {
            return $doc;
        }else  if($doc->shared == 1)
        {
            return $doc;
        }else
        {
            return FALSE;
        }
        
     }


     public function create_file($user_id,$folder_id,$file_data)
    {
        $file_id = md5(rand(111, 999).time());
        
        $doc_file = <<<EOF
        {
            "id": "",
            "object_type": "file",
            "from": {
                "id": "",
                "name": ""
            },
            "shared" : "",
            "name": "",
            "format": "",
            "size": "",
            "source": "",
            "tags": "",
            "created_time": "",
            "update_time": "",
            "admins": [],
            "editors" : [],
            "viewers" : []
         }
EOF;
        
        
        $doc_userfile = json_decode($doc_file);
        
        $doc_userfile->id = $file_id;
        $doc_userfile->object_type = "userfiles";
        $doc_userfile->from->id = $user_id;
        $doc_userfile->from->name = "anthony";
        $doc_userfile->name =  $file_data['name'];
        $doc_userfile->shared =  0;
        $doc_userfile->format = $file_data['file_extension'];
        $doc_userfile->size = $file_data['size'];
        $doc_userfile->tags =  $file_data['tags'];
        $doc_userfile->source =  $file_data['source'];
        $doc_userfile->admins = [];
        $doc_userfile->editor = [];
        $doc_userfile->viewers = [];
        $doc_userfile->created_time = date("c");
        $doc_userfile->update_time = date("c");
        
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        $folders = &$doc->folders;
        
        $files = new stdClass();
        
        $files->file_id = $file_id;
        $files->permission = "admin";
        
        
        foreach($folders as $key => $value) 
        {
            if($value->folder_id == $folder_id)
            {
                $index = $key;
                array_push($doc->folders[$index]->files, $files);
                $this->cb->set($doc_key,json_encode($doc));
                
                $docfile_key = $this->get_file_key($file_id);
                $this->cb->set('file_last_id',$file_id);
                return $this->cb->set($docfile_key, json_encode($doc_userfile));
             
            }
        }
        
        return FALSE;
     }
     
     public function delete_file($user_id,$folder_id,$file_id)
     {
        // get user_id relevant document
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        //get all folder array from users_id document
        $folders = &$doc->folders;
       
        $folder_index = FALSE;
        
        foreach($folders as $key => $value) 
        {
            if($value->folder_id == $folder_id)
            {
                $folder_index = $key;
                break;
            }
        }
        
        $file_index = FALSE;
        
        if($folder_index !== FALSE)
        {
            $files = &$folders[$folder_index]->files;
                
            foreach ($files as $key => $value)
            {
                if($value->file_id == $file_id)
                {
                    $file_index = $key;
                    break;
                }
            }
        }
        
        
        if($file_index !== FALSE)
        {
            
            $docfile_key = $this->get_file_key($file_id);
            $doc_file = json_decode($this->cb->get($docfile_key));
            
            if($doc_file->from->id == $user_id)
            {
                $source = $doc_file->source;
                unlink($source);
                $this->cb->delete($docfile_key);
            }
            
            unset($files[$file_index]);
            $files = array_merge($files);
            
            return $this->cb->set($doc_key,json_encode($doc));
        }
        
        return FALSE;
     }
     
     public function delete_folder($user_id, $folder_id)
     {
         // get user_id relevant document
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        //get all folder array from users_id document
        $folders = &$doc->folders;
        
        $folder_index = FALSE;
        
        foreach($folders as $key => $value) 
        {
            if($value->folder_id == $folder_id)
            {
                $folder_index = $key;
                break;
            }
        }
        
       
        if($folder_index !== FALSE)
        {
            $folderid = $folders[$folder_index]->folder_id;
            $folderfiles = $folders[$folder_index]->files;
            
            $numoffiles = count($folderfiles);
            
            foreach ($folders as $key => $value)
            {
                if($value->folder_parent != $folderid && $numoffiles ==0)
                {
                    unset($folders[$folder_index]);
                    $folders = array_merge($folders);
                    return $this->cb->set($doc_key,json_encode($doc));
                }
            }
        }
        
        return FALSE;
    }
    
    public function newname_folder($user_id, $folder_id , $folder_rename)
    {
         // get user_id relevant document
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        //get all folder array from users_id document
        $folders = &$doc->folders;
       
        foreach($folders as $key => $value) 
        {
            if($value->folder_id == $folder_id)
            {
                $index = $key;
                $doc->folders[$index]->folder_name = $folder_rename;
                return $this->cb->set($doc_key,json_encode($doc));
            }
        }
        
        return FALSE;
    }
    
    public function rename_file($file_id, $file_name,$user_id)
    {
        $docfile_key = $this->get_file_key($file_id);
        $doc = json_decode($this->cb->get($docfile_key));
        
        if($doc->from->id == $user_id)
        {
            $name = $doc->name;
            $extension = get_extension($name);
            $doc->name = $file_name.".".$extension;
            return $this->cb->set($docfile_key,json_encode($doc));
        }else if(in_array($user_id, $doc->viewers) || in_array($user_id, $doc->editors))
        {
            $name = $doc->name;
            $extension = get_extension($name);
            $doc->name = $file_name.".".$extension;
            return $this->cb->set($docfile_key,json_encode($doc));
        }else
        {
            return FALSE;
        }
     }
    
    public function get_folders($user_id, $parent, $sort)
    {
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        $folders_all = $doc->folders;
        
        $folders = array();
        $titles = array();
        $dates = array();
        
        foreach ($folders_all as $folder)
        {
            if($folder->folder_parent == $parent)
            {
                $titles[] = $folder->folder_name;
                $dates[] = $folder->create_time;
                array_push($folders, $folder);
            }
        }
        
        if($sort == "title")
        {
             array_multisort($titles, $folders);
                
         }else
         {
             array_multisort($dates, $folders);
         }
        
        return $folders;
        
    }
    
    public function get_files($user_id, $parent, $sort)
    {
        //echo "user_id= ".$user_id."parent = ".$parent;
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        $index = FALSE;
        $folders = $doc->folders;
        
        foreach($folders as $key => $value) 
        {
            if($value->folder_id == $parent)
            {
                $index = $key;
                break;
            }
        }
        
        if($index !== FALSE)
        {
            $files = &$folders[$index]->files;
            $file_keys = array();
            
            foreach($files as $file)
            {
                array_push($file_keys, $this->get_file_key($file->file_id));
            }
           
            $files_all  = $this->cb->getMulti($file_keys);
            
            $doc_files = array();
            $titles = array();
            $dates = array();
            
            foreach ($files_all as $key => $value) 
            {
                $value = json_decode($value);
                
                $titles[] = $value->name;
                $dates[] = $value->update_time;
                
                array_push($doc_files, $value);
            }
            

            if($sort == "title")
            {
                array_multisort($titles, $doc_files);
                
            }else
            {
                array_multisort($dates, $doc_files);
            }
            
            
            return $doc_files;
        }
        
        return FALSE;
    }
    
    public function file_drag($user_id, $folderdrop_id, $folderdrag_id, $filedrag_id)
    {
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        $folders = &$doc->folders;
        
        $index = FALSE;
        
        foreach ($folders as $key => $value) 
        {
            if($value->folder_id == $folderdrag_id)
            {
                $index = $key;
                break;
            }
        }
        
        $file_index = FALSE;
        
        if($index !== FALSE)
        {
            $files = &$folders[$index]->files;
            foreach ($files as $key => $value)
            {
                if($value->file_id == $filedrag_id)
                {
                    $file_index = $key;
                    break;
                }
            }
        }
        //find folder_id and file_id for deleting purpose
        
        $folderdrop_index = FALSE;
        
        if($file_index !== FALSE)
        {
            //get reqiured file which want to drop into other folder
            $requiredfile = &$files[$file_index];
            foreach ($folders as $key => $value){
            if($value->folder_id == $folderdrop_id)
            {
                $folderdrop_index = $key;
                break;
            }
            }
        }
         
        if($folderdrop_index !== FALSE)
        {
            $dropfiles = &$folders[$folderdrop_index]->files;
            //push the requiredfile into folderdrop
            array_push($dropfiles, $requiredfile);
 
            
            unset($files[$file_index]);
            $files= array_merge($files);
            return $this->cb->set($doc_key,json_encode($doc));
        }
        
        return FALSE;
        
   }
   
   public function share_file($file_id)
   {
       $docfile_key = $this->get_file_key($file_id);
       $doc_file = json_decode($this->cb->get($docfile_key));
       
       $doc_file->shared = 1;
       
       return $this->cb->set($docfile_key,json_encode($doc_file));
   }
   
   public function downalodfile_share($file_id)
   {
       $docfile_key = $this->get_file_key($file_id);
       $doc_file = json_decode($this->cb->get($docfile_key));
       
       if($doc_file && $doc_file->shared == 1)
       {
           return $doc_file;
       }
       
       return FALSE;
   }
   public function user_permission($username,$permission,$file_id)
   {
       $users_all = json_decode($this->cb->get('users_all'));
       $users = &$users_all->users;
       
       $user_index = false;
       foreach ($users as $key => $value) 
       {
           if($value->email == $username)
           {
               $user_index = $key;
               break;
           }
       }
       
       $docfile_key = $this->get_file_key($file_id);
       $doc_file = json_decode($this->cb->get($docfile_key));
       $doceditor = &$doc_file->editors;
       $docview = &$doc_file->viewers;
       
       $user_editor_id = FALSE;
       $user_inedx_view = FALSE;
       
       if($user_index !== FALSE)
       {
           $reqiuredid = $users[$user_index]->user_id;
           
           foreach ($doceditor as $key => $value) 
           {
                if($value == $reqiuredid)
                {
                    $user_editor_id = $value;
                    break;
                }
            }
            
            foreach ($docview as $key => $value) 
            {
                if($value == $reqiuredid)
                {
                    $user_inedx_view = $value;
                    break;
                }
            }
       }
       else 
       {
           return FALSE;
       }
       
       if($user_editor_id === FALSE && $permission == "edit" && $user_inedx_view === FALSE)
       {
                array_push($doc_file->editors, $reqiuredid );
                $this->cb->set($docfile_key,json_encode($doc_file));
                $doc_key = $this->get_doc_key($reqiuredid);
                $doc = json_decode($this->cb->get($doc_key));
                $objfileedit = new stdClass();
                $objfileedit->file_id = $file_id;
                $objfileedit->permission = $permission;
                array_push($doc->shared_files, $objfileedit );
                return $this->cb->set($doc_key,json_encode($doc));
       }
       
       if($user_inedx_view === FALSE && $permission == "view" && $user_editor_id === FALSE)
       {
                array_push($doc_file->viewers, $reqiuredid );
                $this->cb->set($docfile_key,json_encode($doc_file));
                $doc_key = $this->get_doc_key($reqiuredid);
                $doc = json_decode($this->cb->get($doc_key));
                $objfileview = new stdClass();
                $objfileview->file_id = $file_id;
                $objfileview->permission = $permission;
                array_push($doc->shared_files, $objfileview );
                return $this->cb->set($doc_key,json_encode($doc));
            
       }
     
       
     
    }
    
    public function remove_sharedfiles($username,$permission,$file_id)
    {
       $users_all = json_decode($this->cb->get('users_all'));
       $users = &$users_all->users;
       
       
       $user_index = false;
       foreach ($users as $key => $value) 
       {
           if($value->email == $username)
           {
               $user_index = $key;
               break;
           }
       }
       
       $docfile_key = $this->get_file_key($file_id);
       $doc_file = json_decode($this->cb->get($docfile_key));
       $doceditor = &$doc_file->editors;
       
       $user_editor_id = FALSE;
       
       if($user_index !== FALSE)
       {
           $reqiuredid = $users[$user_index]->user_id;
           
           foreach ($doceditor as $key => $value) 
           {
                if($value == $reqiuredid)
                {
                    $user_editor_id = $key;
                    break;
                }
            }
       }
       
       $docview = &$doc_file->viewers;
       $user_inedx_view = FALSE;
      
        if($user_index !== FALSE)
        {
            foreach ($docview as $key => $value) 
            {
                if($value == $reqiuredid)
                {
                    $user_inedx_view = $key;
                    break;
                }
            }
        }
        
       if($user_editor_id !== FALSE && $permission == "edit")
       {
                unset($doceditor[$user_editor_id]);
                $doceditor = array_merge($doceditor);
                $this->cb->set($docfile_key,json_encode($doc_file));
                
                $doc_key = $this->get_doc_key($reqiuredid);
                $doc = json_decode($this->cb->get($doc_key));
                $sharedfile = &$doc->shared_files;
                $delete_index = FALSE;
                foreach ($sharedfile as $key => $value) 
                {
                    if($value->file_id == $file_id && $value->permission == $permission)
                    {
                        $delete_index = $key;
                        break;
                    }
                }
                
                if($delete_index !== FALSE)
                {
                    unset($sharedfile[$delete_index]);
                    $sharedfile= array_merge($sharedfile);
                    return $this->cb->set($doc_key,json_encode($doc));
                }
                
       }
       
       if($user_inedx_view !== FALSE && $permission == "view" )
       {
                unset($docview[$user_inedx_view]);
                $docview = array_merge($docview);
                $this->cb->set($docfile_key,json_encode($doc_file));
                
                $doc_key = $this->get_doc_key($reqiuredid);
                $doc = json_decode($this->cb->get($doc_key));
                $sharedfile = &$doc->shared_files;
                $delete_index = FALSE;
                foreach ($sharedfile as $key => $value) 
                {
                    if($value->file_id == $file_id && $value->permission == $permission)
                    {
                        $delete_index = $key;
                        break;
                    }
                }
                
                if($delete_index !== FALSE)
                {
                    unset($sharedfile[$delete_index]);
                    $sharedfile= array_merge($sharedfile);
                    return $this->cb->set($doc_key,json_encode($doc));
                }
            
       }
     
       return FALSE;
     
    }
    public  function get_shared_files($user_id,$sort)
    {
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        $shared_files = &$doc->shared_files;
        
        $file_keys = array();
        
        foreach ($shared_files as $file)
        {
            array_push($file_keys, $this->get_file_key($file->file_id));
        }
        
        $files_all  = $this->cb->getMulti($file_keys);
        
        $doc_files = array();
        $titles = array();
        $dates = array();
        
        $count = 0;
        foreach ($files_all as $index => $file)
        {
            $file = json_decode($file);
            
            $file->permission = $shared_files[$count]; 
            $titles[] = $file->name;
            $dates[] = $file->update_time;
            $doc_files[] = $file;
            $count++;
            
           
           
        }
        
        if($sort == "title")
        {
            array_multisort($titles, $doc_files);
                
        }
        else
        {
            array_multisort($dates, $doc_files);
        }
            
        return $doc_files;
    }
        
    public function getuser_file($file_id)
    {
        $docfile_key = $this->get_file_key($file_id);
        $doc_file = json_decode($this->cb->get($docfile_key));
        
        return $doc_file;
    }
    
    public function manage_files($permission_id)
    {
       $docfile_key = $this->get_file_key($permission_id);
       $doc_file = json_decode($this->cb->get($docfile_key));
       $editor= &$doc_file->editors;
       $viewer= &$doc_file->viewers;
       
       $useredit_doc = array();
       $userview_doc = array();
       
       foreach($editor as $value)
       {
           array_push($useredit_doc, $this->get_doc_key($value));
       }
      
       foreach($viewer as $value)
       {
           array_push($userview_doc, $this->get_doc_key($value));
       }
       
       $useredit_all  = $this->cb->getMulti($useredit_doc);
       $userview_all  = $this->cb->getMulti($userview_doc);
     
       $get_editedemail = array();
       foreach ($useredit_all as $value) 
       {
            $value = json_decode($value);
            $objuser = new stdClass();
            $objuser->email = $value->email;
            $objuser->permission = "edit";
            array_push($get_editedemail, $objuser);
       }
      
       $get_viewemail = array();
       foreach ($userview_all as $value) 
       {
            $value = json_decode($value);
            $objuser = new stdClass();
            $objuser->email = $value->email;
            $objuser->permission = "view";
            array_push($get_viewemail, $objuser);
       }
       
       
       $result = array_merge($get_viewemail,$get_editedemail);
       
       return $result;
    }
    
    public function get_username($user_id)
    {
        $doc_key = $this->get_doc_key($user_id);
        $doc = json_decode($this->cb->get($doc_key));
        
        $firstname = $doc->firstname;
        
        return $firstname;
    }
         
    public function get_doc_key($user_id)
    {
        return "user".KEY_DELIM.$user_id;
        
    }
    public function get_file_key($file_id)
    {
        return "file".KEY_DELIM.$file_id;
    }
 
}
