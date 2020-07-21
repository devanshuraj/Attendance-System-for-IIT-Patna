<?php
    $conn=mysqli_connect('localhost','root','');
    mysqli_select_db($conn,'iitpatna');


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

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
    $nx=10;

    for($i=0;$i<sizeof($Date_List);$i++){
        for($p=0;$p<2;$p++){
            for($j=0;$j<sizeof($Room_List);$j++){
                for($k=0;$k<sizeof($Course_List);$k++){
                    $qry="SELECT * FROM room_allocation WHERE roomno='$Room_List[$j]' AND coursecode='$Course_List[$k]' AND date='$Date_List[$i]' AND shift='$Shift_List[$p]'";
                    $temp= $conn->query($qry);
                    if($temp->num_rows>0){
                        while($row=$temp->fetch_assoc()){
                            $string=$row["rollnolist"];
                            $str_arr = explode (",", $string); 
                            for($x=0;$x<sizeof($str_arr);$x++){
                                $check=$str_arr[$x];
                                if(strlen($check)){
                                    $getname="SELECT * FROM studentinfo WHERE Roll_No='$check'";
                                    $get_me=$conn->query($getname);
                                    $f_name="error";//here is the name
                                    if($get_me->num_rows>0){
                                        while($nrow=$get_me->fetch_assoc()){
                                            $f_name=$nrow["Name"];
                                        }
                                    }
                                    $check=strtolower($check);
                                    $getname="SELECT * FROM studentinfo WHERE Roll_No='$check'";
                                    $get_me=$conn->query($getname);
                                    if($get_me->num_rows>0){
                                        while($nrow=$get_me->fetch_assoc()){
                                            $f_name=$nrow["Name"];
                                        }
                                    }
                                    if(file_exists('photos/'.$check.'.jpg')){
                                        $pic_name=$check;
                                    }
                                    else{
                                        if($f_name=="error"){
                                            $No_name_list[]=$str_arr[$x];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    ;
    $No_name_list=array_unique($No_name_list);
    // $myfile = fopen("newfile.txt", "w") or die("Unable to open file!");
    // foreach($No_name_list as $val){
    //     $txt = $val."\n";
    //     fwrite($myfile, $txt);
    // }
    // fclose($myfile);

    $file = "test.txt";
    $txt = fopen($file, "w") or die("Unable to open file!");

    foreach($No_name_list as $val){
        $txtg = $val."\n";
        fwrite($txt, $txtg);
    }
    fclose($txt);
    
    header('Content-Description: File Transfer');
    header('Content-Disposition: attachment; filename='.basename($file));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    header("Content-Type: text/plain");
    readfile($file);
    
?>