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

function get_scromStatus($id, $course){
	global $DB;
   $course = "SELECT DISTINCT mcmc.id, mcmc.userid,mcmc.coursemoduleid,  mcm.instance, mcmc.completionstate
              FROM mdl_user u
              JOIN mdl_user_enrolments ue ON ue.userid = u.id
              JOIN mdl_enrol e ON e.id = ue.enrolid
              JOIN mdl_role_assignments ra ON ra.userid = u.id
              JOIN mdl_context ct ON ct.id = ra.contextid
              AND ct.contextlevel =50
              JOIN mdl_course c ON c.id = ct.instanceid
              AND e.courseid = c.id
              JOIN mdl_role r ON r.id = ra.roleid
              AND r.shortname =  'student'
                      JOIN mdl_course_modules mcm on mcm.course = c.id
                      JOIN mdl_course_modules_completion mcmc on mcmc.coursemoduleid = mcm.id 
              WHERE e.status =0
              AND u.suspended =0
              AND u.deleted =0
              AND (
              ue.timeend =0
              OR ue.timeend > NOW( ) 
              )
              AND ue.status =0
              AND c.id ='$course' and mcm.course = $course and mcm.module = 18 and mcmc.userid = $id";

    $course_result = $DB->get_records_sql($course); 
    $tmp = array();
      foreach($course_result as $row)
    {
      array_push($tmp, $row->completionstate);
    }
     return $tmp;
 }
 
 
 function get_totalSCORM($course){
   global $DB;
  $course = "SELECT c.id,COUNT(c.id) as tot FROM mdl_course c JOIN mdl_course_modules mcm on mcm.course = c.id WHERE c.id = $course and mcm.module = 18"; 
    $course_result = $DB->get_records_sql($course); 
    $tmp = array();
      foreach($course_result as $row)
    {
      array_push($tmp, $row->tot);
    }
     return $tmp;
 }
 
 
 
 function get_Internalscore($user_id, $course){ 

global $DB;

  $sql = "SELECT mqa.id as attemptID,mqa.sumgrades  as grade, mq.sumgrades FROM mdl_course_modules_completion cmc JOIN mdl_user u ON cmc.userid = u.id JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id JOIN mdl_course c ON cm.course = c.id JOIN mdl_modules m ON cm.module = m.id JOIN mdl_quiz mq on c.id = mq.course JOIN mdl_quiz_attempts mqa on mq.id = mqa.quiz WHERE u.id = '$user_id' and mqa.userid = '$user_id' and  c.id = '$course' group by attemptID order by grade desc";
  
  if($user_id == 245186){
	 // echo $sql;
}


  $a = array();
             $course_result = $DB->get_records_sql($sql); 
			 
			 if($user_id == 245186){
				/* echo '<pre>';
				 print_r($course_result);
				 echo '</pre>';*/
				 
}


             foreach($course_result as $row)
{
  array_push($a,  (array)$row);
  
}
return $a;
}

function get_totalQuiz($course){
   global $DB;
  $course = "SELECT c.id,COUNT(c.id) as tot FROM mdl_course c JOIN mdl_course_modules mcm on mcm.course = c.id WHERE c.id = $course and mcm.module = 16"; 
    $course_result = $DB->get_records_sql($course); 
    $tmp = array();
      foreach($course_result as $row)
    {
      array_push($tmp, $row->tot);
    }
     return $tmp;
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
			->setCellValue('A1', 'S.No')
            ->setCellValue('B1', 'Student Name')
            ->setCellValue('C1', 'Scorm Completed Status')
            ->setCellValue('D1', 'Internal Score')
			->setCellValue('E1', 'External Score')
			->setCellValue('F1', 'Grade');
			
			
			if(isset($_GET['courseid'])){
			$course_id = $_GET['courseid'];
			}
			
			if(isset($_GET['cohortid'])){
			$cohort_id = $_GET['cohortid'];
			}
			
			if(isset($_GET['domain'])){
			$vDomain = $_GET['domain'];
			}
			
			
	
  if($cohort_id != 0){ 
  $students = "SELECT DISTINCT u.id AS userid, u.firstname, u.lastname, c.id FROM mdl_user u JOIN mdl_user_enrolments ue ON ue.userid = u.id JOIN mdl_enrol e ON e.id = ue.enrolid JOIN mdl_cohort_members mcm on mcm.userid = u.id JOIN mdl_cohort mc on mc.id = mcm.cohortid JOIN mdl_role_assignments ra ON ra.userid = u.id JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel =50 JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id JOIN mdl_role r ON r.id = ra.roleid AND r.shortname = 'student' WHERE e.status =0 AND u.suspended =0 AND u.deleted =0 AND u.cm_domain='$vDomain' /*AND ( ue.timeend =0 OR ue.timeend > UNIX_TIMESTAMP(NOW()) )*/ AND ue.status =0 AND courseid ='$course_id' AND mc.id = '$cohort_id'";
  
}else{
$students = "SELECT DISTINCT u.id AS userid, u.firstname, u.lastname, c.id FROM mdl_user u JOIN mdl_user_enrolments ue ON ue.userid = u.id JOIN mdl_enrol e ON e.id = ue.enrolid JOIN mdl_role_assignments ra ON ra.userid = u.id JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel =50 JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id JOIN mdl_role r ON r.id = ra.roleid AND r.shortname = 'student' WHERE e.status =0 AND u.suspended =0 AND u.deleted =0 AND u.cm_domain='$vDomain' /*AND ( ue.timeend =0 OR ue.timeend > UNIX_TIMESTAMP(NOW()) )*/ AND ue.status =0 AND courseid ='$course_id'";
}
  
  
$students_result = $DB->get_records_sql($students);     
			
				 $row = 2;
				 $i = 1;
foreach($students_result as $result){
	
	
	$scorm_status = get_scromStatus($result->userid,$result->id);
	$tot_scrom = get_totalSCORM($result->id);
	
	if($tot_scrom[0]>0){
                           if($tot_scrom[0] == count($scorm_status)){
                           if(in_array(0, $scorm_status)){
                              $vScormStatus =  "In Progress";
                            }else{
                              $vScormStatus =  "Yes";
                            }
                          }else{
                            $vScormStatus =  "Not Completed";
                          }
                        }else{
                          $vScormStatus =  "SCORM Not Created";
                        }
						
						//Get Internal Score
						
						$ins = get_Internalscore($result->userid,$result->id);
                        $tot_quiz = get_totalQuiz($result->id);
						
						/*echo '<pre>';
						print_r($tot_quiz);
						echo '</pre>';*/
						
						
                        $atot = '';
						$otot = '';
                               foreach ($ins as $key => $value) {
                                $atot += $value['grade']; 
                                $otot += $value['sumgrades']; 
                               }

                              if($tot_quiz[0]>0){
                               if($otot!=0||$atot!=-0){
                                $Intot[$i] = round(($atot/$otot)*70);
                               $vInternalScore =  $Intot[$i];
                             }else{
                              $vInternalScore =  "Not taken";
                             }
                           } else{
                            $vInternalScore = "Quiz Not Created";
                           }
						   
						   
						   if(isset($Intot[$i])&&isset($Extot[$i])){
                            $vGrade  = $Intot[$i]+$Extot[$i]; 
							 }else{
                            $vGrade  =  "Not Applicable";
                          } 

		
		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row, $i)
			->setCellValue('B'.$row, $result->firstname.' '.$result->lastname)
			->setCellValue('C'.$row, $vScormStatus)
			->setCellValue('D'.$row, $vInternalScore)
			->setCellValue('E'.$row, 'Not taken')
			->setCellValue('F'.$row, $vGrade);
			
			$row++;
			$i++;
			

}

//exit;
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Course Completion Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Course Completion Report.xlsx"');
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
