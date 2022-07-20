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

/*ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);*/
date_default_timezone_set('Europe/London');
set_time_limit(100000);
error_reporting(0);

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
            ->setCellValue('A1', 'Course Name')
            ->setCellValue('B1', 'Enrolled On')
            ->setCellValue('C1', 'Last Access')
            ->setCellValue('D1', 'Status');
			$vWhere = '';
			if($_SESSION["sesCourseId"] != ''){
	$vWhere .= " and a.courseid = ".$_SESSION["sesCourseId"];
}

if(is_siteadmin()){
		   $query = "select b.id,b.userid, a.courseid as courseid,c.usertypename,c.firstname,c.lastname,c.email from {$CFG->prefix}enrol a,{$CFG->prefix}user_enrolments b,{$CFG->prefix}user c where a.id = b.enrolid and b.userid = c.id $vWhere";
} else {
	  $query = "select b.id,b.userid, a.courseid as courseid,c.usertypename,c.firstname,c.lastname,c.email from {$CFG->prefix}enrol a,{$CFG->prefix}user_enrolments b,{$CFG->prefix}user c where a.id = b.enrolid and b.userid = c.id and c.usertype = $USER->usertype $vWhere";

}

$objEnrolledUsers = $DB->get_records_sql($query);
 
		$row = 2;
foreach($objEnrolledUsers as $enrolled_course){
	
	  $vUserStatus = get_user_course_status($enrolled_course->courseid,$enrolled_course->userid);
if($vUserStatus == 0){
	$vStatus = 'Not Started';
}else if($vUserStatus == 100){
	$vStatus = 'Completed';
}else{
	$vStatus = 'In Progress';
}


$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, $enrolled_course->firstname.' '.$enrolled_course->lastname)			
			->setCellValue('B'.$row, $enrolled_course->usertypename)
			->setCellValue('C'.$row, $enrolled_course->email)
            ->setCellValue('D'.$row, $vStatus);
			$row++;

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Course-User Wise Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Course-User Wise Report.xlsx"');
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
