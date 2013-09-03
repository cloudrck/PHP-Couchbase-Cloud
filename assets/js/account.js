function sigupValidation()
{
   
    var firstname = document.forms["signupform"]["fname"].value;
    var lastname = document.forms["signupform"]["lname"].value;
    var emailid = document.forms["signupform"]["email"].value;
    var password = document.forms["signupform"]["password"].value;
    var retypepass = document.forms["signupform"]["repeatpass"].value;

    var errors = "";
    if (firstname==null || firstname=="")
    {
      errors = "First name must be filled out";

    }

    if (lastname==null || lastname=="")
    {
      errors = "\nLast name must be filled out";

    }

    if (emailid==null || emailid=="")
    {
      errors = "\nEmail ID must be filled out";

    }

    if (password==null || password=="")
    {
      errors = "\nPassword must be filled out";

    }
    
    if (retypepass==null || retypepass=="")
    {
      errors = "\nRe_type Password must be filled out";
    }
    
    if(password != retypepass)
    {
        errors = "\nPassword does not match";
    }
    
    if(errors != "")
    {
        alert(errors);
        
    }
    else
    {
        $(document).ready(function(){
        var url = "couch/users.php";
        var form = $("#signupform").serialize();
        $.post(url,form,function(response){

           if(response.status)
           {
                 alert("Congradulation have been signed up");  
                 window.location = "http://dev1.cloudrck.net/login.php";
           }
           else
           {
                 alert("Email ID already exist"); 
           }
           }, 'json');
        });
    }
    
    return false;

}

function loginValidation()
{
    var emailid = document.forms["loginform"]["email"].value;
    var password = document.forms["loginform"]["password"].value;
    
    var errors = "";
    
    if (emailid==null || emailid=="")
    {
        errors = "Email ID must be filled out";
        
    }
  
    if (password==null || password=="")
    {
        errors = "\nPassword must be filled out";
        
    }
    
    if(errors != "")
    {
    
        alert(errors);
    }
    else
    {
        var url = "couch/users.php";
        var form = $("#loginform").serialize();

        $(document).ready(function(){  
       
        $.post(url,form,function(response){
           if(response.status)
           {
                window.location = "filemanager.php";
           }else
           {
                alert("Either Email or Password invalid");
           }
        }, 'json');
        });
    }
    
    return false;
}




      


