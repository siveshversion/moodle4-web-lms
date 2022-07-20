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
error_reporting(E_ALL);




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



			
//geting balance credit

if(isset($_GET['userId'])){
	$vUserId = $_GET['userId'];
}

if(isset($_GET['todate'])){
	$vToDate = $_GET['todate'];
}

if(isset($_GET['fromdate'])){
	 $vFromDate = $_GET['fromdate'];
}


if(isset($_GET['buId'])){
	$buId = $_GET['buId'];
}


if(isset($_GET['token'])){
	$wstoken = $_GET['token'];
}



	
	$arrFromDate = explode(' ',$vFromDate);
	 $vFromMonth = getMonNumber($arrFromDate[1]);	
	$arrToDate = explode(' ',$vToDate);
	$vToMonth = getMonNumber($arrToDate[1]);	
	

$sdate = $arrFromDate[2].'/'.$vFromMonth.'/'.$arrFromDate[3];
$edate = $arrToDate[2].'/'.$vToMonth.'/'.$arrToDate[3];


/*$userId = 31;
$buId = 127;
$wstoken = '6c55a6c123a01a545efc8c20fd3a0d8a';
$sdate =  '01/07/2022';
$edate = '02/07/2022';*/




$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot."/cm/api/methods.php?methodname=courseReport");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);

$data = array(
    'userId' => $vUserId,
    'buId' => $buId,
	'wstoken' => $wstoken,
	'sdate' => $sdate,
	'edate' => $edate
);



curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$contents = curl_exec($ch);

$arrResult = json_decode($contents,true);

$arrData = $arrResult['Data'];



	 
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
            ->setCellValue('A1', 'SNo')
            ->setCellValue('B1', 'Course Name')
            ->setCellValue('C1', 'Enrolled')
            ->setCellValue('D1', 'Completed')
			->setCellValue('E1', 'In Progress')
			->setCellValue('F1', 'Not Started');
			
			
			

			
				 $row = 2;
				 $sNo = 1;
foreach($arrData as $transaction){
	
	
	

		
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, $sNo)
			->setCellValue('B'.$row, $transaction['course_fullname'])
			->setCellValue('C'.$row, $transaction['enrolled_cnt'])
			->setCellValue('D'.$row, $transaction['completed_cnt'])
			->setCellValue('E'.$row, $transaction['inprogress_cnt'])
			->setCellValue('F'.$row, $transaction['notstarted_cnt']);
			$row++;
			$sNo++;
			

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Credit Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Course Report.xlsx"');
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
