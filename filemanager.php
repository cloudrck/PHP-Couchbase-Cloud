<?php

include 'couch/users.php';

session_start();

if(isset($_SESSION['email'], $_SESSION['password']))
{
}else{
    header("Location: login.php");
}//


$first_name = user_name();
$folders = get_folders();
$files = get_files();
?>
<!DOCTYPE html>
<html lang="en">
    <head>

        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <title>File manager</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="assets/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="assets/bootstrap/js/bootstrap-fileupload.min.css" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
   	<script src="assets/js/jquery-1.9.1.min.js"></script>
        <script src="assets/jquery-ui-1.10.2.custom/js/jquery-ui-1.10.2.custom"></script>
        <script src="assets/js/dragdrop_plugin.js"></script>
        <script src="assets/js/ajaxfileupload.js"></script>
        <script src="assets/js/fileajaxfunction.js"></script>
        <script src="assets/bootstrap/js/bootstrap-fileupload.min.js"></script>
        
        
        
    <style type="text/css">
      body {
        padding-top: 60px;
        padding-bottom: 40px;
      }
      .sidebar-nav {
        padding: 9px 0;
      }

      @media (max-width: 980px) {
        /* Enable use of floated navbar text */
        .navbar-text.pull-right {
          float: none;
          padding-left: 5px;
          padding-right: 5px;
        }
      }
    </style>
  

    <link rel="shortcut icon" href="assets/images/logo.gif">
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top" style="border-bottom: 8px solid red">
      <div class="navbar-inner">
        <div class="container-fluid">
          <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div id="filemanager">
          <img src="assets/images/logo.gif" id="logo">
          <span id="file">FILE</span> manager</div>
          <div id="userdiv">
          <div id="userimage"><img src="assets/images/profile.jpg" id="profile"></div>
          <div id="username">
          <ul>
              <li>Hi!<span><?php echo $first_name ?></span></li>
              <li><a href="logout.php" style="color:#FFFFFF;">Logout</a></li>
          </ul>
          </div>
          </div>
        </div>
          <!--/.nav-collapse -->
        </div>
      </div>
    </div>

    <div class="container-fluid" style="margin-top:10px;">
      <div class="row-fluid">
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="redactive"><a href="#"><i class="icon-home"></i>Dashboard</a></li>
              <li><a href="http://dev1.cloudrck.net/filemanager.php"><i class="icon-user"></i>My Files</a></li>
              <li><a href="http://dev1.cloudrck.net/sharedfile.php"><i class="icon-share"></i>Shared with me</a></li>
              <li><a href="#"><i class="icon-home"></i>Help</a></li>
              <li><a href="#"><i class="icon-headphones"></i>Contact Us</a></li>
            </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
        <div id="iconheader" class="well">
        <ul>
            <form id="folderform" name="folderform" onsubmit="return CreateFolderValidation();" method="post">
    		<div id="example" class="modal hide fade in" style="display: none; ">  
    		<div class="modal-header">  
    			<a class="close" data-dismiss="modal">×</a>  
    			<h3>New Folder</h3>  
    		</div>  
    		
                <div class="modal-body">
                <h4><input type="text" name="foldername" id="foldername" placeholder="Enter Folder Name"></h4>
                <input type="hidden" name="parent" value="<?php echo isset($_GET['folder'])? $_GET['folder']: 1;?>" />
    		</div>  
    		
                <div class="modal-footer">  
                <input type="hidden" name="action" value="folder_create">
    		<button type="submit" class="btn primary" name="folder" id="folder" >Create Folder</button>  
    		<a href="#" class="btn" data-dismiss="modal">Cancel</a>  
                </div> 
    		
                </div> 
            </form>
               
            <form id="folRename" onsubmit="return false;">
    		<div id="rename" class="modal hide fade in" style="display: none; ">  
    		<div class="modal-header">  
    			<a class="close" data-dismiss="modal">×</a>  
    			<h3>Folder Rename</h3>  
    		</div>  
    		<div class="modal-body">
                <h4><input type="text" name="folder_rename" id="folder_rename" placeholder="Enter Folder Rename"></h4>
                    <input type="hidden" name="folder_id" value="" id="folder_id_renamed" />
                    <input type="hidden" name="action" value="folder_rename">
    		</div>  
    		<div class="modal-footer"> 
                <button type="submit" class="btn primary" id="renamefolder" name="renamefolder" >Rename Folder</button>      
    		<a href="#" class="btn" data-dismiss="modal">Cancel</a>  
               
    		</div> 
    		</div> 
             </form>
           
   
              <form onsubmit="return false;">
    		<div id="filerenamed" class="modal hide fade in" style="display: none; ">  
    		<div class="modal-header">  
    			<a class="close" data-dismiss="modal">×</a>  
    			<h3>File Rename</h3>  
    		</div>  
    		<div class="modal-body">
                <h4>Enter the new file name</h4>
                <input type="text" name="file_rename" id="file_rename" value="">
                <input type="hidden" name="id_file" id="id_file" value="">
                <div id="extension"></div>
                </div>  
    		<div class="modal-footer"> 
                <button type="submit" class="btn primary" onclick="filerenamed(); return false;" >Rename File</button>
    		<a href="#" class="btn" data-dismiss="modal">Cancel</a>  
                </div> 
    		</div> 
             </form>
            
                <form id="fileform" name="fileform" enctype="multipart/form-data" onsubmit="return FileUploadValidation()">
    		<div id="uploadfile" class="modal hide fade in" style="display: none; ">  
    		<div class="modal-header">  
    			<a class="close" data-dismiss="modal">×</a>  
    			<h3>Upload File</h3>  
    		</div>  
    		
                <div class="modal-body">  
    		<h4><input type="file" name="file" id="filename" placeholder="Select File"></h4>
                <h4><input type="text" name="filetags" id="filetags" placeholder="Enter File Tag"></h4>
                <div id="loader"><img src="assets/images/uploader.jpg" id="imageloader" style="display: none;"></div>
      
    		</div>  
    	
                <div class="modal-footer">
                <input type="hidden" name="folder_id" id="folder_file_id" value="<?php echo isset($_GET['folder'])? $_GET['folder']: 1;?>" />
    		<button type="submit" class="btn primary" name="createfile" id="createfile" >Upload</button>  
    		<a href="#" class="btn" data-dismiss="modal">Cancel</a>  
    		</div>  
    		</div>   
                </form>	
               
    		<div id="deletefolder" class="modal hide fade in" style="display: none; ">   
    		<div class="modal-body">  
    		<h4>Are You Sure You want to delete it</h4>               
    		</div>  
    		<div class="modal-footer">
                <input type="hidden" name="folder_id_del" id="folder_id_del">
                <button type="submit" class="btn primary" onclick=" return deletefolder();" >yes</button>   
    		<a href="#" class="btn" data-dismiss="modal">no</a>  
    		</div>  
    		</div>
            
                <div id="deletefile" class="modal hide fade in" style="display: none; ">   
    		<div class="modal-body">  
    		<h4>Are You Sure You want to delete it</h4>               
    		</div>  
    		<div class="modal-footer">
                <input type="hidden" name="file_id_del" id="file_id_del">
                <button type="submit" class="btn primary" onclick="return delfile();" >yes</button> 
    		<a href="#" class="btn" data-dismiss="modal">no</a>  
    		</div>  
    		</div>
   	 
              <form id="shareform" onsubmit="return false;">
    		<div id="fileshare" class="modal hide fade in" style="display: none; ">  
    		<div class="modal-header">  
    			<a class="close" data-dismiss="modal">×</a>  
    			<h3>File Share</h3>  
    		</div>  
    		<div class="modal-body">
                <h4>Copy file link</h4>
                <input type="text" name="file_link" id="downloadurl" class="input-xlarge">
    		</div>  
    		<div class="modal-footer">     
                </div> 
    		</div> 
             </form>
           
                <form id="usershareform" name="usershareform" onsubmit="return usersharedValidation();">
    		<div id="usershare" class="modal hide fade in" style="display: none; ">  
    		<div class="modal-header">  
    			<a class="close" data-dismiss="modal">×</a>  
    			<h3>User Shared</h3>  
    		</div>  
    		<div class="modal-body">  
    		<h4>Email ID<input type="text" name="user" id="user"></h4>
                <h4>Permission
                    <select name="permission">
                        <option>edit</option>
                        <option>view</option>
                    </select>
                </h4>
    		</div>
                <input type="hidden" name="fileshareid" id="fileshareid" value="">
                <input type="hidden" name="action" id="action" value="user_share">
                <div class="modal-footer"> 
                <button type="submit" class="btn primary" name="givepermisssion" id="givepermisssion" >Shared File</button>
                <a href="#" class="btn" data-dismiss="modal">Cancel</a>  
    		</div>  
    		</div>   
                </form>	
           
            
            <div class="container">
    		<div id="manage_user" class="modal hide fade in" style="display: none; ">  
    		<div class="modal-header">  
    			<a class="close" data-dismiss="modal">×</a>  
    			<h3>Permission</h3>  
    		</div>  
    		<div class="modal-body">  
                 
    		<table id="table">
                <tr>
                    <th width="200">Email ID</th>
                    <th width="150">Permission</th>
                    <th width="100">Delete</th>
                </tr>
                </table>
                <table class="table table-hover" id="drag">
                    
                </table>
    		</div>  
                <div class="modal-footer"> 
    		<a href="#" class="btn" data-dismiss="modal">Cancel</a>  
    		</div>  
    		</div>   
            </div>
            
            <li><a data-toggle="modal" href="#example" ><i class="icon-folder-close" title="Create Folder"></i>Create Folder</a></li>
            <li><a data-toggle="modal" href="#uploadfile" ><i class="icon-upload" title="Upload file"></i>Upload </a></li>
            <li class="fileoptions"><a data-toggle="modal" href="#" ><i class="icon-download" title="download file"></i>Dowload</a> </li>
            <li class="fileoptions"><a data-toggle="modal" href="#deletefile"><i class="icon-trash" title="delete file"></i>Delete</a></li>
            <div class="btn-group" id="setsort"><a href="#" data-toggle="dropdown" class="btn btn-mini dropdown-toggle list-btn-action">Sort<span class="caret"></span></a>
                 <ul style="font-size:12px;" class="dropdown-menu">
                      <li><a href="http://dev1.cloudrck.net/filemanager.php?folder=<?php echo isset($_GET['folder'])? $_GET['folder']: 1;?>&sort=date"><i class="icon-list"></i>Sort by Date</a></li>
                      <li><a href="http://dev1.cloudrck.net/filemanager.php?folder=<?php echo isset($_GET['folder'])? $_GET['folder']: 1;?>&sort=title"><i class="icon-list"></i>Sort by Title</a></li>
                 </ul>
            </div>
        </ul>
        </div>
        </div>
        
        <div class="span9" style="height:600px">
        <div id="tablehead">
        <form name="form1" >
        <table id="table">
        <tr>
        <th width="60"><input type="checkbox" id="checkall" ></th>
        <th width="350">TITLE</th>
        <th>LAST MODIFY</th>
        <th></th>
        </tr>
        </table>
            <?php if(empty($folders) && empty($files)): ?>
            <tr><td><?php echo "<div class='alert alert-info'>No folder or file found.</div>"; ?></td></tr>
            <?php else : ?>
            <table class="table table-hover" id="drag">
                
                <?php foreach ($folders as $folder):?>
                    <tr>
                        <input type="hidden" id="folder_id" name="folder_id" value="<?php echo isset($_GET['folder'])? $_GET['folder']: 1;?>">
                        <td width="60"><input type="checkbox" class="showicon" name="showicon" name="folderrename_id" id="folderrename_id" value="<?php echo $folder->folder_id;?>"></td>
                        <td width="350"><i class="icon-folder-close"></i> <a class="back" href="filemanager.php?folder=<?php echo $folder->folder_id;?>" ><?php echo $folder->folder_name;?></a></td>
                        <td><?php echo $folder->create_time;?></td>
                        <td>
                        <div class="btn-group">
                            <a href="#" data-toggle="dropdown" class="btn btn-mini dropdown-toggle list-btn-action">Actions<span class="caret"></span></a>
                            <ul style="font-size:12px;" class="dropdown-menu">
                           <li><a data-toggle="modal" href="#rename" onclick="$('#folder_id_renamed').val(<?php echo $folder->folder_id;?>); $('#folder_rename').val('<?php echo $folder->folder_name;?>');"> &gt;<i class="icon-edit"></i>Rename</a></li>
                           <input type="hidden" name="getfolderid" id="getfolderid" name="getfolderid" value="">
                           <li><a data-toggle="modal" href="#deletefolder" onclick="$('#folder_id_del').val(<?php echo $folder->folder_id;?>);"><i class="icon-trash"></i>Delete</a></li>
                           </ul>
                       </div>
                       </td>
                    </tr>
                <?php endforeach;?>
                
               <?php foreach ($files as $file):?>
                <tr>
                    <input type="hidden" id="folder_id" name="folder_id" value="<?php echo isset($_GET['folder'])? $_GET['folder']: 1;?>" >
                    <td width="60"><input type="checkbox" class="showicon" id="file_id" name="file_id" value="<?php echo $file->id;?>"></td>
                    <td width="350"><i class="icon-file"></i><a class="back" href="#"><?php echo $file->name;?></a>
                    </td>
                    <td><?php echo $file->created_time;?></td>
                    <td>
                       <div class="btn-group">
                           <a href="#" data-toggle="dropdown" class="btn btn-mini dropdown-toggle list-btn-action">Actions<span class="caret"></span></a>
                       <ul style="font-size:12px;" class="dropdown-menu">
                           <input type="hidden" id="renamefileid" name="renamefileid" value="">
                           <li><a data-toggle="modal" href="#filerenamed" onclick="renamedfile('<?php echo $file->id;?>','<?php echo $file->name; ?>'); return false;">&gt;<i class="icon-edit"></i>Rename</a></li>
                           <li><a href="download.php?file_id=<?php echo $file->id;?>"><i class="icon-download-alt"></i>Download</a></li>
                           <li><a data-toggle="modal" href="#deletefile" onclick="$('#file_id_del').val('<?php echo $file->id;?>');"><i class="icon-trash"></i>Delete</a></li>
                           <li><a data-toggle="modal" href="#fileshare" onclick="publicsharefile('<?php echo $file->id;?>'); $('#downloadurl').val('http://dev1.cloudrck.net/share.php?file_id=<?php echo $file->id;?>');"><i class="icon-share"></i>Public Share</a></li>
                           <li><a data-toggle="modal" href="#usershare" onclick="$('#fileshareid').val('<?php echo $file->id;?>');"><i class="icon-share"></i>User Share</a></li>
                           <li><a data-toggle="modal" href="#manage_user" onclick="getfile('<?php echo $file->id;?>'); return false;"><i class="icon-share"></i>Manage permission</a></li>
                       </ul>
                       </div> 
                   </td>
                </tr>
                
              <?php endforeach;?>
            </table>
            <?php endif; ?>
        </form>
        </div>
        </div>
         
        </div>
      </div>
      
      <hr>

      <footer>
        <p>© Company 2013</p>
      </footer>
      
    </div>
    <script src="assets/bootstrap/js/bootstrap.js"></script>
   
</body>
</html>
