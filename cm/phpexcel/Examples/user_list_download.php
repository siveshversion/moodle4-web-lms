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
date_default_timezone_set('Asia/Calcutta');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


	 
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
			->setCellValue('A1', 'Sl.No')			
            ->setCellValue('B1', 'Username')
			->setCellValue('C1', 'Domain')
            ->setCellValue('D1', 'First Name')
			->setCellValue('E1', 'Last Name')
			->setCellValue('F1', 'Cohort')
			->setCellValue('G1', 'Date of Creation')
			->setCellValue('H1', 'Last Access');
			
			
			if(isset($_GET['userid'])){
			$vTeacherId = $_GET['userid'];
			}
			
			if(isset($_GET['role'])){
			$vRole = $_GET['role'];
			}
			
			if(isset($_GET['domain'])){
			$vDomain = $_GET['domain'];
			}
			
			
			if($vRole == 'Trainer'){
				
			$sql = "select b.id,b.firstname,b.cm_domain,b.lastname,b.email,b.timecreated,b.suspended,b.username,b.lastaccess,a.student_id from mdl_cm_students a,mdl_user b where a.created_by = $vTeacherId and a.student_id = b.id and b.deleted = 0 order by b.id desc";
			
			}else if($vRole == 'cadmin'){
				
				//$sql = "select id,firstname,lastname,email,timecreated,suspended,username,lastaccess,id as student_id,cm_domain from {user} where deleted = 0 and cm_domain = '$vDomain' order by id desc";
				$sql = "SELECT b.*,b.id as student_id FROM mdl_cm_students a,mdl_user b WHERE a.student_id = b.id and b.cm_domain IS NOT NULL and b.deleted = 0 and b.confirmed = 1 and b.cm_domain = '$vDomain' order by id desc";
				
				
			}else if($vRole == 'hr'){
			
			$q = "select id,firstname,lastname,email,timecreated,suspended,
                username,lastaccess,idas student_id,cm_domain 
                from {user} where deleted = 0 order by id desc";
				
			}
			
			
			
$objUsers = $DB->get_records_sql($sql); 
			
		
			
				 $row = 2;
				 $i = 1;
foreach($objUsers as $user){
	
	if($user->lastaccess == 0){
		$vLastAccess = 'N/A';
	}else{
		$vLastAccess = date('d-M-Y h:i:s',$user->lastaccess);
	}
	
	
										$vStudentId = $user->student_id;
										$sql = "select b.id as cohort_id,b.name as cohort_name from mdl_cohort_members a,mdl_cohort b where a.userid = $vStudentId and a.cohortid = b.id";
										$objCohorts = $DB->get_records_sql($sql); 
$vCohorts = '';
  foreach($objCohorts as $row_1){
	  $vCohorts .= $row_1->cohort_name.', ';
  }	  
  
  $vCohorts = trim($vCohorts,', ');
						
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, $i)			
			->setCellValue('B'.$row, $user->username)
			->setCellValue('C'.$row, $user->cm_domain)
			->setCellValue('D'.$row, $user->firstname)
			->setCellValue('E'.$row, $user->lastname)
			->setCellValue('F'.$row, $vCohorts)
			->setCellValue('G'.$row, date('d-M-Y h:i:s',$user->timecreated))
			->setCellValue('H'.$row, $vLastAccess);
			$row++;
			$i++;
			

}



// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('User Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="User Report.xlsx"');
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
