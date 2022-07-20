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


   function get_quiz($id){
global $DB;
             $course = "SELECT mdl_feedback_value.id,mdl_user.firstname, mdl_user.lastname, c.fullname, mdl_feedback.name as module, mdl_feedback_item.presentation as presentation, mdl_feedback_item.name as question, mdl_feedback_value.value as response FROM mdl_feedback_value
                  INNER JOIN mdl_feedback_completed ON mdl_feedback_completed.id = mdl_feedback_value.completed
                  INNER JOIN mdl_user ON mdl_feedback_completed.userid = mdl_user.id 
                  INNER JOIN mdl_feedback ON mdl_feedback_completed.feedback = mdl_feedback.id 
                  INNER JOIN mdl_feedback_item ON mdl_feedback_value.item = mdl_feedback_item.id 
                  INNER JOIN mdl_course c on c.id = mdl_feedback.course
                  WHERE mdl_feedback.course = '$id'
                  ORDER BY question";
              $course_result = $DB->get_records_sql($course); 
              $tmp = array();
              $ress = 0;
                foreach($course_result as $row)
              {
                  $tot = substr_count($row->presentation, "|")+1; 
                  $res = ($row->response/$tot)*100; 
                   
                   
                 array_push($tmp,  $res);
              }  
               return  $tmp;
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
            ->setCellValue('A1', 'Sl. No')
            ->setCellValue('B1', 'Course Name')
            ->setCellValue('C1', 'Feedback %');
			
			
			if(isset($_GET['userid'])){
			$logged_in_user = $_GET['userid'];
			}
			
			$course = "SELECT cr.id,count(DISTINCT mcm.cohortid) as batch, count(u.firstname) as no_of_stu, u.firstname, u.lastname, u.email, cr.fullname, ra.userid, ra.roleid, ra.contextid, cr.id FROM mdl_role_assignments ra LEFT JOIN mdl_user u on u.id = ra.userid left join mdl_context cx on cx.id = ra.contextid left join mdl_course cr on cr.id = cx.instanceid left join mdl_cohort_members mcm on mcm.userid = u.id WHERE contextid IN (SELECT contextid FROM mdl_role_assignments WHERE roleid = 3 and userid='$logged_in_user' AND contextid IN (SELECT id from mdl_context c WHERE c.contextlevel='50')) and roleid = 5 group by fullname";
$course_result = $DB->get_records_sql($course); 
			
		
			
				 $row = 2;
				 $i = 1;
foreach($course_result as $record){
	
	
	$tot = get_quiz($record->id);
	$cnt = count($tot)*100; 
         if($cnt==0){
			 $cnt = 1;
			 } 
			 $avg = array_sum($tot)/$cnt; 
			 $vFeedback =  round($avg*100);    

		
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, $i)
			->setCellValue('B'.$row, $record->fullname)
			->setCellValue('C'.$row, $vFeedback);
			$row++;
			

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Feedback Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Feedback Report.xlsx"');
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
