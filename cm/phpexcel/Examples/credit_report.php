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


$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot."/cm/api/methods.php?methodname=getCreditTransactions");
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);

$data = array(
    'user_id' => $vUserId,
    'fromdate' => $vFromDate,
	'todate' => $vToDate
);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$contents = curl_exec($ch);

$arrResult = json_decode($contents,true);

$arrData = $arrResult['Data'];

/*echo '<pre>';
print_r($arrData);

exit;*/
	 
	 
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
            ->setCellValue('B1', 'Course Name')
            ->setCellValue('C1', 'Credit')
            ->setCellValue('D1', 'Transaction Type')
			->setCellValue('E1', 'Balance')
			->setCellValue('F1', 'Mode')
			->setCellValue('G1', 'Subcription Plan')
			->setCellValue('H1', 'Start Date')
			->setCellValue('I1', 'Expire Date')
			->setCellValue('J1', 'Status')
			->setCellValue('K1', 'Remarks');
			
			
			

			
				 $row = 2;
foreach($arrData as $transaction){
	
	
	

		
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, $transaction['transactionDate'])
			->setCellValue('B'.$row, $transaction['courseName'])
			->setCellValue('C'.$row, $transaction['totalCredits'])
			->setCellValue('D'.$row, $transaction['transType'])
			->setCellValue('E'.$row, $transaction['creditBalance'])
			->setCellValue('F'.$row, $transaction['mode'])
			->setCellValue('G'.$row, $transaction['subscriptionName'])
			->setCellValue('H'.$row, $transaction['startDateTime'])
			->setCellValue('I'.$row, $transaction['expireDateTime'])
			->setCellValue('J'.$row, 'Success')
			->setCellValue('K'.$row, $transaction['remarks']);
			$row++;
			

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Credit Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Credit Report.xlsx"');
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
