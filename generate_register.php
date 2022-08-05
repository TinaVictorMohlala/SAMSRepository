<?php

session_start();

if (!isset($_SESSION["signedin"]) || $_SESSION["signedin"] !== true) {
    header("location: index.php");
    exit();
}

require('../fpdf182/fpdf.php');

class PDF extends FPDF
{

function FancyTable($header, $data)
{
	// Colors, line width and bold font
	$this->SetFillColor(221, 221, 221);
	$this->SetTextColor(119, 119, 119);
	$this->SetDrawColor(255, 255, 255);
	$this->SetLineWidth(.3);
	$this->SetFont('Arial','B',9);
	// Header
	$w = array(10, 45, 45, 45, 45);
	for($i=0;$i<count($header);$i++)
		$this->Cell($w[$i],7,$header[$i],1,0,'L',true);
	$this->Ln();
	// Color and font restoration
	$this->SetFillColor(221, 221, 221);
	$this->SetTextColor(119, 119, 119);
	$this->SetFont('Arial','',10);
	// Data
	$fill = false;
	foreach($data as $row)
	{
		$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
		$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
		$this->Cell($w[2],6,$row[2],'LR',0,'L',$fill);
		$this->Cell($w[3],6,$row[3],'LR',0,'L',$fill);
		$this->Cell($w[4],6,$row[4],'LR',0,'L',$fill);
		$this->Ln();
		$fill = !$fill;
	}
	// Closing line
	$this->Cell(array_sum($w),0,'','T');
}
}

$data = array();
//call the FPDF library

require_once("../config/connect.php");

$gen_code = $_GET['gen_code'];
$gen_date = $_GET['gen_date'];
$total_upsent = 0;
$total_present = 0;
$total = 0;

$sql="SELECT * FROM attendance_register WHERE register_code=? AND register_date=?";

if($stmt = $conn->prepare($sql)){

    $stmt->bind_param("ss", $gen_code, $gen_date);

    if($stmt->execute()){

       $result = $stmt->get_result();
   
       if($result->num_rows > 0){
            
           
       	    $status_key = 0;
            $status_value = "";

       	  	while ( $row = $result->fetch_assoc()) {

       	  		$status_key =  $row['register_status'];
       	  		 $total = $total + 1;

                if($status_key==0)
                {
                    $status_value = "Upsent";
                    $total_upsent = $total_upsent + 1;
                }else{
                	$status_value = "Present";
                }
				$data[] = array($total, $row['register_student_no'], $row['register_firstname'], $row['register_lastname'], $status_value);

			   
			
			}

			
			
		}
	}
}


				

$pdf = new PDF();
$pdf->AddPage();
//set font to arial, bold, 14pt
$pdf->SetFont('Arial','B',16);
$pdf->SetTextColor(119, 119, 119);
$pdf->Cell(130 ,5,'',0,0);
$pdf->Cell(100 ,5,'Attendance Register',0,0,'L');//end of line
//set font to arial, regular, 12pt
$pdf->Cell(59 ,5,'',0,1);//end of line
$pdf->Cell(130 ,5,'',0,0);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(25 ,5,'Subject',0,0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(34 ,5,$gen_code,0,1);//end of line
$pdf->Cell(130 ,5,'',0,0);
$pdf->SetFont('Arial','B',9);
$pdf->Cell(25 ,5,'Date',0,0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(34 ,5,$gen_date,0,1);//end of line
$pdf->Cell(130 ,5,'',0,1);

// Column headings
$header = array('#', 'Student No', 'First Name', 'Last Name', 'Attendance');
// Data loading


$pdf->FancyTable($header,$data);
$pdf->Cell(130 ,5,'',0,1);
$pdf->SetFont('Arial','B',10);
$pdf->SetTextColor(119, 119, 119);
$pdf->Cell(10 ,5,'Attendance Register Sammary',0,0,'L');//end of line
$pdf->Cell(130 ,5,'',0,1);
$pdf->SetFont('Arial','',8);
$pdf->Cell(25 ,5,'Students',0,0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(34 ,5,$total,0,1);//end of line
$pdf->SetFont('Arial','',8);
$pdf->Cell(25 ,5,'Presents',0,0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(34 ,5,$total_present = $total - $total_upsent,0,1);//end of line
$pdf->SetFont('Arial','',8);
$pdf->Cell(25 ,5,'Upsents',0,0);
$pdf->SetFont('Arial','',8);
$pdf->Cell(34 ,5,$total_upsent,0,0);//end of line
$pdf->Output();
?>
