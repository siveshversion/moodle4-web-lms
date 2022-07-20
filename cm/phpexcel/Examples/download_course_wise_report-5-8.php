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
            ->setCellValue('B1', 'Enrolled')
            ->setCellValue('C1', 'Not Started')
            ->setCellValue('D1', 'In Progress')
			->setCellValue('E1', 'Completed');



$query = "select id,fullname,coursetype from {$CFG->prefix}course where visible = 1 and category !=0 and coursetype != 3";

$objCourses = $DB->get_records_sql($query);
 
		$row = 2;
foreach($objCourses as $course){
	
	$query = "select count(b.id) from {$CFG->prefix}enrol a,{$CFG->prefix}user_enrolments b,{$CFG->prefix}user c where a.id = b.enrolid and a.courseid = $course->id and b.userid = c.id";


$vEnrolledCount = $DB->count_records_sql($query);
$vNotStrated = 0;
$vInProgressCount = 0;
$vCompletedCount = 0;
   $query = "select b.userid, a.courseid as courseid from {$CFG->prefix}enrol a,{$CFG->prefix}user_enrolments b,{$CFG->prefix}user c where a.id = b.enrolid and a.courseid = $course->id and b.userid = c.id";

$objEnrolledUsers = $DB->get_records_sql($query);
	 
foreach($objEnrolledUsers aS $enrol){
	
$vUserStatus = get_user_course_status($enrol->courseid,$enrol->userid);
if($vUserStatus == 0){
	$vNotStrated++;
}else if($vUserStatus == 100){
	$vCompletedCount++;	
}else{
	$vInProgressCount++;	
}
}



$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, $course->fullname)			
			->setCellValue('B'.$row, $vEnrolledCount)
			->setCellValue('C'.$row, $vNotStrated)
            ->setCellValue('D'.$row, $vInProgressCount)
			->setCellValue('E'.$row, $vCompletedCount);
			$row++;

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Course Wise Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Course Wise Report.xlsx"');
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
