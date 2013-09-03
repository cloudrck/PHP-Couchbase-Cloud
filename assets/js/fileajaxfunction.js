//javascript jquery functions....filemanager system

//upload file function...
function ajaxFileUpload()
{
      var folder_id = $("#folder_file_id").val();
      var filetags = $("#filetags").val();
      $("#imageloader").show();
      $.ajaxFileUpload({
              url: "couch/users.php",
              secureuri:false,
              fileElementId:'filename',
              dataType:'json',
              data:{
                     "action" : "create_file",
                     "folder_id" : folder_id,
                     "filetags" : filetags
               },
               success: function(response)
               {
                   $("#imageloader").hide();
                   if(response.status)
                   {
                       window.location = document.URL;
                        
                   }
                   else
                   {
                       alert("Error in file Uploading");
          
                   }
                        
//                        //document.getElementById('event_flyer_thumb').src= base_url + "public/assets/event_images/" + reponse.photo;
//                        console.log(response);
//                
//                        var image_id = "image_" + index;
//                        if(response.status)
//                        {
//                            document.getElementById(image_id).src= "images/media/" + response.photo;
//                            media[index].photo = response.photo;
//                            $('#image_upload_actions_'+index).show();
//                        }else
//                        {
//                            console.log("error");
//                            alert(response.error)
//                        }
                    },
                    error: function (response, status, e)
                    {
                        console.log(response);
                        alert(e);
                    }
                }
            );
            return false;
}

// jquery checkboxes function....
        function checkAll()
		{
			if(document.form1.showicon.length > 1)
			{
				for(var i in document.form1.showicon)
				{
					document.form1.showicon[i].checked = true;
				}
		
			}else
			{
				document.form1.showicon.checked = true;
			}
			return true;
		}
		
		function uncheckAll()
		{
			if(document.form1.showicon.length > 1)
			{
				for(var i in document.form1.showicon)
				{
					document.form1.showicon[i].checked = false;
				}
				
			}else
			{
				document.form1.showicon.checked = false;
			}
			return true;
		}
                
		$(document).ready(function(){
                  
		$(".showicon").change(function(){
			
                        if($(this).is(":checked"))
			{
                            $(this).parent().parent().css("background-color","red");
                            
                            
			}
			else
			{
				$(this).parent().parent().css("background-color","white");
			}
		
			if($('input[type=checkbox]:checked').length > 0)
			{
				$(".fileoptions").show();
				
			}else
			{
				$(".fileoptions").hide();
		
			}
			});
			
			$("#checkall").click(function(){
			
				if(this.checked){
				
					$(".fileoptions").show();
					checkAll();
				}
				else{
				$(".fileoptions").hide();
					uncheckAll();
				}
				$(".showicon").change();
			});
                      
                        // rename folder function
                        $("#renamefolder").click(function(){
                        
                        var url = "couch/users.php";
                        var form = $("#folRename").serialize();
                        $.post(url,form,function(response){
                                
                                if(response.status)
                                {
                                  
                                    window.location = document.URL;
                                }
                                else
                                {
                                    alert("Folder cannot created");
                                }
                            
                        }, 'json');
                        });
                        
                //drag and drop function
                $('#drag').tableDnD({
                    onDrop: function(table, row) {
                    
                    var folder_id = $("#folder_id").val();
                    var file_id = $("#file_id").val();
                    alert(file_id);
                    },
                    dragHandle: ".dragHandle"
                });
                
                
});

function delfile()
{
         var folder_id = $("#folder_id").val();
         var file_id = document.getElementById("file_id_del").value;
         var url = "couch/users.php";
         $.ajax({
                    type : "post",
                    url: url,
                    dataType: 'json',
                    data: {'folder_id' : folder_id, 'file_id': file_id, 'action': "delete_file" },
                    success: function(response){
                          if(response.status)
                          {
                                 window.location = document.URL;
                          }
                          else
                          {
                                 alert("Error in deleting file")
                          }
                   }
             });
                
}
 function getfile(id)
 {
         var permission_id = id;
         var url = "couch/users.php";
         $.ajax({
                     type : "post",
                     url: url,
                     dataType: 'json',
                     data: {"permission_id": permission_id, "action": "permission_file"},
                     success: function(response){

                     document.getElementById("drag").innerHTML = "";

                     for(var i=0; i< response.length; i++)
                     {
                                var tr = document.createElement("tr");
                                var td1 = document.createElement("td");
                                td1.style.width = "200px";
                                var td2 = document.createElement("td");
                                td2.style.width = "150px";
                                var td3 = document.createElement("td");
                                td3.style.width = "100px";
                                tr.appendChild(td1);
                                tr.appendChild(td2);
                                tr.appendChild(td3);
                                td1.innerHTML = response[i].email;
                                td2.innerHTML = response[i].permission;
                                
                                var link = document.createElement('input'); 
                                link.setAttribute('type', 'image');
                                link.setAttribute('id', 'resendrequest');
                                link.setAttribute('src', 'assets/images/delete.png');
                                link.setAttribute('height', '10px');
                                link.setAttribute('width', '10px');
                                //link.onclick = get_temp(response[i].email, response[i].permission);
                                
                                bindClick(link, response[i].email, response[i].permission,id);
                                
//                                link.onclick = function(){
//                                    
//                                    var data =  {"email" : response[i].email, "permission_id": response[i].permission,"action":"userdel_share"};
//                                    
//                                    alert(JSON.stringify(data));
//                                    
//                                    var email = emailid;
//                                    alert(email);
//                                    $.ajax({
//                                        
//                                        type : "post",
//                                        url: url,
//                                        dataType: 'json',
//                                        data: {"email" : response[i].email, "permission_id": response[i].permission,"action":"userdel_share"},
//                                        success: function(response){
//                                            
//                                        }
//                                        
//                                    })
//                                 };
                                
                                
                                td3.appendChild(link);
                                document.getElementById("drag").appendChild(tr);
                       }

                    }
                   });
                   
                                
}


function bindClick(link, email, permission,id)
{
    
    link.onclick = function(){
    
    var url = "couch/users.php";
    $.ajax({

        type : "post",
        url: url,
        dataType: 'json',
        data: {"email" : email, "permission": permission,"action":"userdel_share","file_id":id},
        success: function(response){
            
            if(response.status)
            {
                alert("file has been deleted");
                window.location = document.URL;
            }
            else
            {
                alert("file cant be delete");
            }
        }

    });
 };
}

function CreateFolderValidation()
{
  
    var foldername = document.forms["folderform"]["foldername"].value;
    var errors = "";
    
    if (foldername==null || foldername=="")
    {
        errors = "Folder name must be filled out";
        
    }
  
    if(errors != "")
    {
    
        alert(errors);
    }
    else
    {
       $(document).ready(function(){  
         var url = "couch/users.php";
         var form = $("#folderform").serialize();
         $.post(url,form,function(response){
                                
            if(response.status)
            {
                
                 window.location = document.URL;
            }
            else
            {
                 alert("Folder cannot created");
            }
                            
         }, 'json');
        });
    }
    
    return false;
}

function FileUploadValidation()
{
    var filename = document.getElementById("filename").value;
    var filetags = document.forms["fileform"]["filetags"].value;
 
    
    var errors = "";
    
    if (filename==null || filename=="")
    {
        errors = "File must be Select out";
        
    }
    
    if (filetags==null || filetags=="")
    {
        errors = "\nFile tags must be filled out";
        
    }
  
    if(errors != "")
    {
    
        alert(errors);
    }
    else
    {
       ajaxFileUpload();
    }
    
    return false;
    
}

function deletefolder()
{
     var folder_id = document.getElementById("folder_id_del").value;
     var url = "couch/users.php";
     $.ajax({
              type : "post",
              url: url,
              dataType: 'json',
              data: {'folder_id' : folder_id, 'action': "delete_folder" },
              success: function(response){
                  if(response.status)
                  {
                    
                        window.location = document.URL;
                        
                  }
                  else
                  {
                      
                        alert("Folder cant be delete");
                        window.location = document.URL;
                  }
          }
          
     });
}

function publicsharefile(id)
{
      var file_id = id;
      var url = "couch/users.php";
      $.ajax({
               type : "post",
               url: url,
               dataType: 'json',
               data: {'file_id': file_id,'action': "share_file"},
               success: function(response){
                   if(response.status)
                   {
                        alert("file has been shared")
                   }
                   else
                   {
                       alert("file has not been shared")
                   }
              }
        });
               
}


function usersharedValidation()
{
    var username = document.forms["usershareform"]["user"].value;
    var errors = "";
    
    if (username==null || username=="")
    {
        errors = "Email must be filled out";
        
    }
  
    if(errors != "")
    {
    
        alert(errors);
    }
    else
    {
        
       var form = $("#usershareform").serialize();
       var url = "couch/users.php";
       $.ajax({
                 type : "post",
                 url: url,
                 dataType: 'json',
                 data: form,
                 success: function(response){
                 if(response.status)
                 {
         
                     window.location = document.URL;
                 }
                 else
                 {
                     alert("file has not been shared")
                 }
                 
             }
        });
               
    }
    
    return false;
    
}

function renamedfile(fileid, filename)
{
    var file_id = fileid;
    var file_name = filename;
    var getname = file_name.split(".");
    document.getElementById("file_rename").value = getname[0];
    document.getElementById("extension").innerHTML = "." + getname[1];
    document.getElementById("id_file").value = file_id;
}
function filerenamed()
{
    var fileid = document.getElementById("id_file").value;
    var filename = document.getElementById("file_rename").value;
   
    var url = "couch/users.php";
    $.ajax({
        
        type : "post",
        url: url,
        dataType: 'json',
        data: {"file_id" : fileid, "file_rename": filename,"action":"file_rename"},
        success: function(response){
            
            if(response.status)
            {
                window.location = document.URL;
            }
            else
            {
                alert("file cant be Rename");
            }
        }

    });   
}
