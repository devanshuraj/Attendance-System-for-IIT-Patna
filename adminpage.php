<html>
<head>
<title>Login Form Design</title>
    <link rel="stylesheet" type="text/css" href="style2.css">
<body>
    <div class="loginbox">
    <img src="avatar.png" class="avatar">
    <?php
      if(isset($_POST['button1'])) { 
        //   echo "This is Button1 that is selected"; 
        header("Location: fpage.php");
      } 
      if(isset($_POST['button2'])) { 
        header("Location: page2.php");
      } 
      if(isset($_POST['button3'])) { 
        header("Location: bpage.php");
    } 
    // if(isset($_POST['button4'])) { 
    //     echo "This is Button2 that is selected"; 
    // } 
    ?> 
    <form method="post"> 
            <input type="submit" name="button1"
                value="Generate Defaulter's List"/> 
            
            <input type="submit" name="button2"
                value="Search for specific subject"/> 
                <input type="submit" name="button3"
                value="Search Roomwise"/> 
            
            <!-- <input type="submit" name="button2"
                value="Search for specific subject"/>  -->
    </form>     
    </div>

</body>
</head>
</html>