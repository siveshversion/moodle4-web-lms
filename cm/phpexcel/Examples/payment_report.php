<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

require('../../../config.php');

//ini_set('display_errors', TRUE);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


$vWhere = '';
if($_SESSION["sesSelectedCourseId"] != ''){
	$vWhere .= " and a.instanceid = ".$_SESSION["sesSelectedCourseId"];
}

if($_SESSION["sesBranchId"] != '' and $_SESSION["sesBranchId"] != 0){
	$vWhere .= " and c.branch = ".$_SESSION["sesBranchId"];
}









	 
	 
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");



//Heading
// Add some data

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Transaction Date')
            ->setCellValue('B1', 'Order ID')
            ->setCellValue('C1', 'Amount Paid')
            ->setCellValue('D1', 'Currency')
			->setCellValue('E1', 'Transaction Status');
			
			
			
//geting balance credit

if(isset($_GET['userid'])){
	$vUserId = $_GET['userid'];
}

if(isset($_GET['todate'])){
	$vToDate = $_GET['todate'];
}

if(isset($_GET['fromdate'])){
	$vFromDate = $_GET['fromdate'];
}


function getMonNumber($arg){

	switch ($arg) {
  case 'Jan':
    $vMonth = 1;
	return $vMonth;
    break;
  case Feb:
    $vMonth = 2;
	return $vMonth;
    break;
  case 'Mar':
    $vMonth = 3;
	return $vMonth;
    break;
  case 'Apr':
    $vMonth = 4;
	return $vMonth;
	break;
	  case 'May':
    $vMonth = 5;
	return $vMonth;
	break;
	  case 'Jun':
    $vMonth = 6;
	return $vMonth;
	break;
	  case 'Jul':
    $vMonth = 7;
	return $vMonth;
	break;
	  case 'Aug':
    $vMonth = 8;
	return $vMonth;
	break;
	  case 'Sep':
    $vMonth = 9;
	return $vMonth;
	break;
	  case 'Oct':
    $vMonth = 10;
	return $vMonth;
	break;
	  case 'Nov':
    $vMonth = 11;
	return $vMonth;
	break;
	  case 'Dec':
    $vMonth = 12;
	return $vMonth;
	break;
	

}
}


$arrFromDate = explode(' ',$vFromDate);
	 $vFromMonth = getMonNumber($arrFromDate[1]);	
	$arrToDate = explode(' ',$vToDate);
	$vToMonth = getMonNumber($arrToDate[1]);	


	
	
	$vFromTimestamp = mktime(0,0,0,$vFromMonth,$arrFromDate[2],$arrFromDate[3]);
	$vToTimestamp = mktime(0,0,0,$vToMonth,$arrToDate[2],$arrToDate[3]);
	$vToTimestamp = $vToTimestamp + 86399;
	
	
	
if($vFromDate != '' and $vToDate != ''){
	$objTransaction = $DB->get_records_sql("select * from {$CFG->prefix}cm_payment_history where userid = $vUserId and createdtime >= $vFromTimestamp and createdtime <= $vToTimestamp");
}else{
	$objTransaction = $DB->get_records_sql("select * from {$CFG->prefix}cm_payment_history where userid = $vUserId");
}


			
			
				 $row = 2;
foreach($objTransaction as $transaction){
	
	
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, date("F j,Y, g:i a",$transaction->createdtime))
			->setCellValue('B'.$row, $transaction->order_id)
			->setCellValue('C'.$row, $transaction->amount)
			->setCellValue('D'.$row, $transaction->currency)
            ->setCellValue('E'.$row, $transaction->status);
			$row++;
			

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Payment Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Payment Report.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
