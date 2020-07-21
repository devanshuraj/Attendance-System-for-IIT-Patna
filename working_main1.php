<?php
    session_start();
    require('library/tcpdf.php');
    $conn=mysqli_connect('localhost','root','');
    mysqli_select_db($conn,'iitpatna');


    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    class MYPDF extends TCPDF {
        public function Header() {
            // Logo
            $image_file = 'library/iitp_logo.png';
            $this->Image($image_file, 10, 4, 28, '', 'PNG', '', 'T', false, 30, '', false, false, 0, false, false, false);
            // Set font
            $this->SetFont('helvetica', 'U', 20);
            // Title
            $this->setXY(28,14);
            $this->Cell(0, 15, ' ATTENDANCE SYSTEM FOR EXAMS ', 0, false, 'C', 0, '', 1, false, 'M', 'M');
        }
        public function Footer() {

            $this->SetY(-40);
            $this->SetX(10);
            $this->Cell(194, 7, ' Invigilators Name & Signature ', 1, false, 'C', 0, '', 1, false, 'L', 'L');
            $this->setXY(10,-33);
            $this->Cell(30, 7, 'Sl No.', 1, false, 'C', 0, '', 1, false, 'L', 'L');
            $this->setXY(40,-33);
            $this->Cell(80, 7, 'Name', 1, false, 'C', 0, '', 1, false, 'L', 'L');
            $this->setXY(120,-33);
            $this->Cell(84, 7, 'Signature', 1, false, 'C', 0, '', 1, false, 'L', 'L');
            for($i=0;$i<4;$i++){
                $this->setXY(10,-33+7*$i);
                $this->Cell(30, 7, '', 1, false, 'C', 0, '', 1, false, 'L', 'L');
                $this->setXY(40,-33+7*$i);
                $this->Cell(80, 7, '', 1, false, 'C', 0, '', 1, false, 'L', 'L');
                $this->setXY(120,-33+7*$i);
                $this->Cell(84, 7, '', 1, false, 'C', 0, '', 1, false, 'L', 'L');
            }
            // Position at 15 mm from bottom
            $this->SetY(-10);
            // Set font
            $this->SetFont('helvetica', 'I', 8);
            // Page number
            $this->Cell(0, 10, 'Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
        }
    }
    // create new PDF document
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->setCellMargins(0, 0, 3, 3);
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Devanshu Raj');
    $pdf->SetTitle('Attendance_Sheet');
    $pdf->SetSubject('TCPDF Tutorial');
    $pdf->SetKeywords('TCPDF, PDF, example, test, guide');

    $pdf->SetMargins(15, 35, 0);
    $pdf->SetHeaderMargin(40);
    $pdf->SetFooterMargin(0);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set font
    $pdf->SetFont('times', '', 12);

    // add a page
    $pdf->AddPage();

    

    // set color for background
    $pdf->SetFillColor(255, 255, 127);

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
    // $op_name=$_SESSION["search_date"]$_SESSION["search_shift"]
    $Shift_List=array("Morning","Evening");
    $anonymous=array();
    $nx=10;
    $cnt_page=1;
    $op_name="dd";
    $day_n;
    for($i=0;$i<sizeof($Date_List);$i++){
        for($p=0;$p<2;$p++){
            for($j=0;$j<sizeof($Room_List);$j++){
                if($_SESSION["merged"]==0){
                    if($Room_List[$j]!= $_SESSION["search_room"])continue;
                }
                if($Shift_List[$p]!= $_SESSION["search_shift"] )continue;
                if($Date_List[$i]!= $_SESSION["search_date"] )continue;
                for($k=0;$k<sizeof($Course_List);$k++){
                    $qry="SELECT * FROM room_allocation WHERE roomno='$Room_List[$j]' AND coursecode='$Course_List[$k]' AND date='$Date_List[$i]' AND shift='$Shift_List[$p]'";
                    $temp= $conn->query($qry);
                    if($temp->num_rows>0){
                        while($row=$temp->fetch_assoc()){
                            $seed="Subject:".$Course_List[$k]."   "."Room:".$Room_List[$j]."   "."Exam:".$row["day"].",".$Date_List[$i]."  Shift:".$Shift_List[$p];
                            $day_n=$row["day"];
                            $pdf->setXY(54,20);
                            $pdf->MultiCell(130, 5, $seed, 1, 'M', 0, 1, '', '', true);
                            $pdf->setY(35);
                            $cnt=0;
                            $string=$row["rollnolist"];
                            $str_arr = explode (",", $string); 
                            $cntr=sizeof($str_arr);
                            for($x=0;$x<sizeof($str_arr);){
                                for($tr=0;$tr<3;$tr++){
                                    $check=$str_arr[$x];
                                    if(strlen($check)){
                                        $getname="SELECT * FROM studentinfo WHERE Roll_No='$check'";
                                        $get_me=$conn->query($getname);
                                        $f_name="";//here is the name
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
                                        $pdf->SetX($nx);
                                        $pic_name="red_one";
                                        if(file_exists('photos/'.$check.'.jpg')){
                                            $pic_name=$check;
                                        }
                                        $pdf->Image('photos/'.$pic_name.'.jpg', '', '', 15, 14.8, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
                                        if(strlen($f_name)>=18){
                                            $f_name=substr($f_name,0,18);
                                        }
                                        if(!strlen($f_name)){
                                            $No_name_list[]=$str_arr[$x];
                                        }
                                        $txt="Name-".$f_name."\nRollNo-".$str_arr[$x]."\n"."Sign-";
                                        $cnt++;
                                        $pdf->SetFont('times', '', 11);
                                        if($tr<2){
                                            $pdf->MultiCell(49, 10, $txt, 1, 'L', 0, 0, '', '', true);
                                        }
                                        else{
                                            $pdf->MultiCell(49, 10, $txt, 1, 'L', 0, 1, '', '', true);
                                        }
                                    }
                                    $pdf->SetFont('times', '', 12);
                                    $x++;
                                    $nx+=65;
                                    if($x==sizeof($str_arr)){
                                        $p_name=$row["date"]."_".$row["day"]."_".$row["shift"]."_".$row["roomno"]."_".$row["coursecode"];
                                        $p_name=$p_name.'.pdf';
                                        // $pdf->Output($p_name,'I');
                                        $nx=10; 
                                        $pdf->AddPage();
                                        $cnt_page++;
                                        break;
                                    }
                                    else if($cnt%36==0){
                                        $nx=10; 
                                        $okay=0;
                                        for($to=$x+1;$to<sizeof($str_arr);$to++){
                                            if(strlen($str_arr[$to])){
                                                $okay=1;
                                                break;
                                            }
                                        }
                                        if($okay==0)break;
                                        $pdf->AddPage();
                                        $cnt_page++;
                                        $seed="Subject:".$Course_List[$k]."   "."Room:".$Room_List[$j]."   "."Exam:".$row["day"].",".$Date_List[$i]."  Shift:".$Shift_List[$p];
                                        $pdf->setXY(54,20);
                                        $pdf->MultiCell(130, 5, $seed, 1, 'M', 0, 1, '', '', true);
                                        $pdf->setY(35);
                                        break;
                                    }    
                                }
                                $nx=10;
                            }
                        } 
                    }
                }
            }
        }
    }
    ;
    if($_SESSION["merged"]!=0){
        $op_name="".$_SESSION["search_date"]."_".$day_n."_".$_SESSION["search_shift"]."_merged";
    }
    else{
        $op_name="".$_SESSION["search_date"]."_".$day_n."_".$_SESSION["search_shift"]."_".$_SESSION["search_room"];
    }
    $op_name=$op_name.'.pdf';
    $pdf->deletePage($cnt_page);
    $pdf->Output($op_name, 'I');
    // $pdf->Output('www.pdf','I');

?>