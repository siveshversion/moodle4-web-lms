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
error_reporting(E_ALL);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


function get_timetaken($quiz_id, $user_id){ 

global $DB;
if($user_id != ''){
  $sql = "SELECT mqa.timestart,mqa.timefinish, mqa.sumgrades as grade  FROM mdl_quiz_attempts mqa 
            JOIN mdl_quiz mq on mq.id = mqa.quiz
            JOIN mdl_user u on u.id = mqa.userid
            WHERE quiz = '$quiz_id' and userid in ('$user_id') order by grade desc limit 1";
             $course_result = $DB->get_records_sql($sql); 
}
             foreach($course_result as $timetaken)
{   
  $timetaken = $timetaken->timefinish-$timetaken->timestart; 

    $sec =  ($timetaken / 60);
 //$a =  round($row['grade']);   
}
 return gmdate("H:i:s", $timetaken);
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
            ->setCellValue('C1', 'Status')
			->setCellValue('D1', 'Completed on')
			->setCellValue('E1', 'Time taken')
			->setCellValue('F1', 'Attempt')
			->setCellValue('G1', 'Correct Answers')
			->setCellValue('H1', 'Grade');
			
			
			





	
	
	  $token = $CFG->moodletoken;
      $domainname = $CFG->wwwroot;

	  
	  require_once($CFG->dirroot . '/course/lib.php');
	  
	  
	  if(isset($_GET['courseid'])){
		  $course_id = $_GET['courseid'];
	  }
	  
	  if(isset($_GET['quizid'])){
		  $vQuizId = $_GET['quizid'];
	  }
	  
	   if(isset($_GET['role'])){
		  $vRole = $_GET['role'];
	  }
	  
	  if(isset($_GET['cohortid'])){
		  $sel_cohort = $_GET['cohortid'];
	  }
	  
	  $vWhere = '';
	  
	    if(isset($_GET['domain'])){
			
			
		  $vDomain = $_GET['domain'];
		  
		  
		   if($vDomain != '' and $vDomain != 'null'){
			   
			   
		  $vWhere .= " and u.cm_domain = '$vDomain'";
		  
		  
		  }
		  
	  }
	  
	  if(isset($_GET['name'])){
		  $firstname = $_GET['name'];
		  
		  if($firstname != '' and $firstname != 'null'){
		  $vWhere .= " and u.firstname like ('$firstname%')";
		  }
	  }
	  
	  
	  
	  /*$course_id = 188;
	  $vQuizId = 1097;
	  $sel_cohort = 0;*/
	  
  $students = "SELECT DISTINCT u.id AS userid
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
        WHERE e.status =0
        AND u.suspended =0
        AND u.deleted =0
        AND (
        ue.timeend =0
        OR ue.timeend > NOW( ) 
        )
        AND ue.status =0
        AND courseid ='$course_id'";
		
		
$students_result = $DB->get_records_sql($students);     
//echo 'Num rows '.$students_result->num_rows;    
 
  $stu_csv = array();
  foreach($students_result as $stu)
  { 
    array_push($stu_csv, $stu->userid);
  }
   
  $out = "";
    foreach($stu_csv as $arr) {
        $out .= $arr.','; 
    }
     
 
$out = rtrim($out, ", "); 
$out = rtrim($out, ", "); 
		
		
		if($sel_cohort==0){
			
			if($out != ''){
$sql = "SELECT  mqa.*,u.id as user_id,u.firstname,u.lastname,mq.sumgrades as noq, mq.grade as maxgrade,mq.id as quiz_id FROM mdl_quiz_attempts mqa 
            JOIN mdl_quiz mq on mq.id = mqa.quiz
            JOIN mdl_user u on u.id = mqa.userid 
            WHERE quiz = $vQuizId and mqa.userid in ($out)  $vWhere group by u.id";
			}
} else{           

if($out != ''){
 $sql = "SELECT mc.id,mc.name, mqa.*,u.id as user_id,u.firstname,u.lastname,mq.sumgrades as noq, mq.grade as maxgrade,mq.id as quiz_id FROM mdl_quiz_attempts mqa 
            JOIN mdl_quiz mq on mq.id = mqa.quiz
            JOIN mdl_user u on u.id = mqa.userid
            join mdl_cohort_members mcm on mcm.userid = u.id 
            JOIN mdl_cohort mc on mc.id = mcm.cohortid
            WHERE quiz = $vQuizId and mqa.userid in ($out) and mc.id = $sel_cohort $vWhere group by u.id"; 
}
}

		
$students_result = $DB->get_records_sql($sql);     

 
  $stu_csv = array();
  foreach($students_result as $result)
  { 
    array_push($stu_csv, $result->userid);
  }
   
  $out = "";
    foreach($stu_csv as $arr) {
        $out .= $arr.','; 
    }
     
 
$out = rtrim($out, ", "); 
$out = rtrim($out, ", "); 

$sel_cohort = 0;

if($sel_cohort=='0'){
$sql = "SELECT  mqa.*,u.id as user_id,u.firstname,u.lastname,mq.sumgrades as noq, mq.grade as maxgrade,mq.id as quiz_id FROM mdl_quiz_attempts mqa 
            JOIN mdl_quiz mq on mq.id = mqa.quiz
            JOIN mdl_user u on u.id = mqa.userid 
            WHERE quiz = $vQuizId $vWhere group by u.id";
} else{           
 $sql = "SELECT mc.id,mc.name, mqa.*,u.id as user_id,u.firstname,u.lastname,mq.sumgrades as noq, mq.grade as maxgrade,mq.id as quiz_id FROM mdl_quiz_attempts mqa 
            JOIN mdl_quiz mq on mq.id = mqa.quiz
            JOIN mdl_user u on u.id = mqa.userid
            join mdl_cohort_members mcm on mcm.userid = u.id 
            JOIN mdl_cohort mc on mc.id = mcm.cohortid
            WHERE quiz = $vQuizId and mqa.userid in ($out) and mc.id = $sel_cohort $vWhere group by u.id"; 
}

$course_result = $DB->get_records_sql($sql); 



 $rows = [];
 
 
 
 
$row = 2;
$sno = 1;

foreach($students_result as $attempt){
	/*echo '<pre>';
	print_r($attempt);
	echo '</pre>';*/
	  // Get total attempts - start
	  
	  $wstoken = $DB->get_field('external_tokens','token',array('userid'=>2,'externalserviceid '=>8));
	  
	  	  $restformat = 'json';
	  $functionnasme = 'mod_quiz_get_user_attempts';
      $params = array( 'quizid' => $vQuizId,'userid' => $attempt->user_id);
	  
       $serverurl = $CFG->wwwroot . '/webservice/rest/server.php'. '?wstoken=' . $wstoken . '&wsfunction='.$functionnasme;      
	  
	  
      $curl = new curl;
      $restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
       $resp = $curl->post($serverurl . $restformat, $params);
	  
	  unset($arrOutput);
      $arrOutput  = json_decode($resp,true);	  
	  
	  
	  
	  	$arrAttempts = $arrOutput[attempts];
		
		

			$arrBestGrade = array();
			foreach($arrAttempts as $grade){
				$arrBestGrade[$grade[id]] = $grade[sumgrades];
			}
			
			
			

			arsort($arrBestGrade);
			
			

	  $vFinalAttemptId = array_key_first($arrBestGrade);
	  
	  
	  
	  
	  $vFinalGrade = $arrBestGrade[$vFinalAttemptId];
	  
	  
	  $vTotalAttempt = count($arrAttempts);
 $vCorrectAnswer = 0;
$vTotalQuestions = 0;

//get total question

if($vFinalAttemptId != ''){
  $sql = "SELECT qa.questionid FROM mdl_quiz_attempts quiza JOIN mdl_question_usages qu ON qu.id = quiza.uniqueid JOIN mdl_question_attempts qa ON qa.questionusageid = qu.id JOIN mdl_question_attempt_steps qas ON qas.questionattemptid = qa.id LEFT JOIN mdl_question_attempt_step_data qasd ON qasd.attemptstepid = qas.id WHERE quiza.id = ".$vFinalAttemptId." and qas.userid = ".$attempt->user_id." group BY qa.questionid";
 
	 
	   
	   $objTotalQuestions = $DB->get_records_sql($sql);
}
	   foreach($objTotalQuestions as $question){
		   $vTotalQuestions++;
	   }


	
	
		  if($vFinalAttemptId != ''){
	
   $sql = "SELECT qas.id,quiza.userid, quiza.quiz, quiza.id AS quizattemptid, quiza.attempt, quiza.sumgrades, qu.preferredbehaviour, qa.slot, qa.behaviour, qa.questionid, qa.maxmark, qa.minfraction, qa.flagged, qas.sequencenumber, qas.state, qas.fraction, qas.userid, qasd.name, qasd.VALUE, qa.questionsummary, qa.rightanswer, qa.responsesummary FROM mdl_quiz_attempts quiza JOIN mdl_question_usages qu ON qu.id = quiza.uniqueid JOIN mdl_question_attempts qa ON qa.questionusageid = qu.id JOIN mdl_question_attempt_steps qas ON qas.questionattemptid = qa.id LEFT JOIN mdl_question_attempt_step_data qasd ON qasd.attemptstepid = qas.id WHERE quiza.id = ".$vFinalAttemptId." and quiza.userid = ".$attempt->user_id." and qas.fraction != '' group by questionid,state ORDER BY quiza.userid, quiza.attempt, qa.slot, qas.sequencenumber, qasd.name";

$objTransaction = $DB->get_records_sql($sql); 
if($attempt->user_id == 245186){
	/*echo '<pre>';
	print_r($objTransaction);
	echo '<pre>';*/
}


$vCorrectAnswer = 0;


 foreach($objTransaction as $answer){
	 
	 if($answer->fraction > 0){
		 
		 $vCorrectAnswer++;
		 
	 }
	 

} 

$timestamp = $attempt->timefinish; 
if($timestamp > 0)
{
	$vCompletedOn =  date('d M Y H:i:s',$timestamp);
} else{ 
$vCompletedOn = "N / A";
}

$vTimeTaken = get_timetaken($attempt->quiz,$attempt->user_id);

if($vGrade == ''){
							$vUserGrade = 0;
						}else{
							$vUserGrade = $vGrade;
						}
						
							$vGrade =  round($vFinalGrade).' / '.round($attempt->noq,0);
							if($vFinalGrade == ''){
								$vFinalGrade = 0;
							}
							

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$row,$sno)
			->setCellValue('B'.$row, $attempt->firstname.' '.$attempt->lastname)
			->setCellValue('C'.$row, $attempt->state)
            ->setCellValue('D'.$row, $vCompletedOn)
			->setCellValue('E'.$row, $vTimeTaken)
			->setCellValue('F'.$row, $vTotalAttempt)
			->setCellValue('G'.$row, $vCorrectAnswer .' / '.$vTotalQuestions)
			->setCellValue('H'.$row, $vFinalGrade);
			



	}
	
	$sno++;
	$row++;
}
		  
		
  
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Overall Assessment Report');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Overall Assessment Report.xlsx"');
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
