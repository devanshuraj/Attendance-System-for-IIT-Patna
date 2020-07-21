<?php
    session_start();
    $conn=mysqli_connect('localhost','root','');
    mysqli_select_db($conn,'iitpatna');
?>
<!DOCTYPE html>
<html>
<head>
  <title>sheet</title>
  <meta name="viewport" content="width=device-width, initial-scale=3.0">
</head>
<body class="body">

<p style="text-decoration:underline;">
<?php echo "Attendance Sheet list for different shifts\n"; ?>
</p>
<?php
    $Room_List=array();
    $Course_List=array();
    $Date_List=array();
    $tRoom_List=array();
    $tCourse_List=array();
    $tDate_List=array();
    $No_name_list=array(); 
    $sql = "SELECT date, day, shift, coursecode, roomno  FROM room_allocation";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            // echo "id: " . $row["id"]. " - hello: " . $row["shift"]. " " . $row["cour    secode"]. "<br>";
            $tRoom_List[]=$row["roomno"];
            $tCourse_List[]=$row["coursecode"];
            $tDate_List[]=$row["date"];
        }
    } else {
        // echo "0 results";
    }
    $tRoom_List=array_unique($tRoom_List);
    $tCourse_List=array_unique($tCourse_List);
    $tDate_List=array_unique($tDate_List);
    sort($tDate_List);
    // print_r($Room_List);
    foreach($tCourse_List as $val){
        $Course_List[]=$val;
    }
    foreach($tRoom_List as $val){
        $Room_List[]=$val;
    }
    foreach($tDate_List as $val){
        $Date_List[]=$val;
    }
    $Shift_List=array("Morning","Evening");
    $anonymous=array();
    
    $Button_names=array();

     for($i=0;$i<sizeof($Date_List);$i++){
        for($p=0;$p<2;$p++){
            for($j=0;$j<sizeof($Room_List);$j++){
                $is_present=0;
                for($k=0;$k<sizeof($Course_List);$k++){
                    $qry="SELECT * FROM room_allocation WHERE roomno='$Room_List[$j]' AND coursecode='$Course_List[$k]' AND date='$Date_List[$i]' AND shift='$Shift_List[$p]'";
                    $temp= $conn->query($qry);
                    if($temp->num_rows>0){
                        $is_present=1;
                    }
                }
                if($is_present){
                    echo "<form method='post'>
                    <input type='hidden' name='id' value='{$i}'/>
                    <input type='submit' name='action' value='$Date_List[$i]_$Shift_List[$p]_$Room_List[$j]'/>
                    </form>";
                    if(ISSET($_POST['id']) && ctype_digit($_POST['id']) && ISSET($_POST['action'])){
                        $id = $_POST['id'];
                        $action = $_POST['action'];
                        $str_arr = explode ("_", $action);                             
                        $_SESSION["search_date"] =$str_arr[0] ;
                        $_SESSION["search_shift"] =$str_arr[1] ;
                        $_SESSION["search_room"] =$str_arr[2] ;
                        $_SESSION["merged"]=0;
                        if($str_arr[2]=="merged")$_SESSION["merged"]=1;
                        header("Location: check_it.php"); 
                    }
                }
            }
            echo "<form method='post'>
            <input type='hidden' name='id' value='{$i}'/>
            <input type='submit' name='action' value='$Date_List[$i]_$Shift_List[$p]_merged'/>
            </form>";
            if(ISSET($_POST['id']) && ctype_digit($_POST['id']) && ISSET($_POST['action'])){
                $id = $_POST['id'];
                $action = $_POST['action'];
                $str_arr = explode ("_", $action);                             
                $_SESSION["search_date"] =$str_arr[0] ;
                $_SESSION["search_shift"] =$str_arr[1] ;
                $_SESSION["search_room"] =$str_arr[2] ;
                $_SESSION["merged"]=0;
                if($str_arr[2]=="merged")$_SESSION["merged"]=1;
                header("Location: check_it.php"); 
            }
        }
    }     
?>
</body>
</html> 
