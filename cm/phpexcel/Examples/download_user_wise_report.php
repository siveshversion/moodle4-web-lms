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
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
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
            ->setCellValue('A1', 'Name')
            ->setCellValue('B1', 'Consitituent')
            ->setCellValue('C1', 'Enrolled')
            ->setCellValue('D1', 'Not Started')
			->setCellValue('E1', 'In Progress')
			->setCellValue('F1', 'Completed');
			if(is_siteadmin()){
 $query = "select * from {$CFG->prefix}user where deleted = 0 and usertype = $_SESSION[selected_usertype] and username != 'guest'";
			} else {
				 $query = "select * from {$CFG->prefix}user where deleted = 0 and usertype = $USER->usertype and username != 'guest'";

			}

 $objUsers = $DB->get_records_sql($query);
		$row = 2;
foreach($objUsers as $user){
	
/* if(is_siteadmin()){	  
    $query = "select b.id,b.userid, a.courseid as courseid from {$CFG->prefix}enrol a,{$CFG->prefix}user_enrolments b,{$CFG->prefix}user c where a.id = b.enrolid and b.userid = $user->id and b.userid = c.id";
} else {
    $query = "select b.id,b.userid, a.courseid as courseid from {$CFG->prefix}enrol a,{$CFG->prefix}user_enrolments b,{$CFG->prefix}user c where a.id = b.enrolid and b.userid = $user->id and b.userid = c.id and c.usertype = $USER->usertype";
	
} */

if(is_siteadmin()){
			$query = "select b.id,b.userid, a.courseid as courseid from {$CFG->prefix}enrol a,{$CFG->prefix}user_enrolments b,{$CFG->prefix}course c where a.id = b.enrolid and b.userid = $user->id and a.courseid = c.id";
	} else {
		$trers = $DB->get_record_sql("SELECT t1,t2,t3 from mdl_course_categorytype where id=$USER->usertype ");

 $catg = $trers ->t1;
			 if(!empty($trers ->t2)){
			 $catg .= ','. $trers ->t2;
			 } 
			  if(!empty($trers ->t3)){
			 $catg .= ','. $trers ->t3;
			 } 
			 
			 	if(!empty($catg)){
  $maincats = $DB->get_records_sql("select id,name,path from {course_categories} where id IN($catg) and visible = 1 and id !=1 and parent = 0");
	}
 $sub2 = array();
foreach ($maincats as $maincat){ 
  $mcatid = $maincat->id;
	if(!empty($mcatid)){
	$subdatas = $DB->get_records_sql("select id from {course_categories}  WHERE FIND_IN_SET($mcatid, REPLACE(path, '/', ','))  and visible = 1");

		if(count($subdatas) != 0 ){
			$sub1 = array();
			foreach ($subdatas as $mainsubdata){
			$sub1[$mainsubdata->id] = $mainsubdata->id ;
			}
			$subcat2= implode(',',$sub1);
		}
	}
	$sub2[$subcat2] = $subcat2 ;
	
}
$allcids = implode(',',$sub2);

 if(!empty($allcids)){
		$query = "select b.id,b.userid, a.courseid as courseid from {$CFG->prefix}enrol a,{$CFG->prefix}user_enrolments b,{$CFG->prefix}course c,{$CFG->prefix}course_categories ccat where a.id = b.enrolid and b.userid = $user->id and a.courseid = c.id and c.category = ccat.id and ccat.id IN($allcids)";
 }
	} 

	 $objEnrolledCourses = $DB->get_records_sql($query);
	 $vEnrolledCount = 0;
	 $vNotStrated = 0;
$vInProgressCount = 0;
$vCompletedCount = 0;
	 foreach($objEnrolledCourses as $enrolled_course){
		 $vEnrolledCount++;


		 
		 $vUserStatus = get_user_course_status($enrolled_course->courseid,$enrolled_course->userid);
if($vUserStatus == 0){
	$vNotStrated++;
}else if($vUserStatus == 100){
	$vCompletedCount++;	
}else{
	$vInProgressCount++;	
}
	 }
	 




$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$row, $user->firstname.' '.$user->lastname)
			->setCellValue('B'.$row, $user->usertypename)
			->setCellValue('C'.$row, $vEnrolledCount)
			->setCellValue('D'.$row, $vNotStrated)
            ->setCellValue('E'.$row, $vInProgressCount)
			->setCellValue('F'.$row, $vCompletedCount);
			$row++;

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('User Wise Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="User Wise Report.xlsx"');
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