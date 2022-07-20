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
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');*/
set_time_limit(100000);
error_reporting(0);

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


$mcatid = $_GET['mcatid'];

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
            ->setCellValue('A1', '#')
            ->setCellValue('B1', 'Product')
            ->setCellValue('C1', 'Module')
            ->setCellValue('D1', 'Course Name')
			->setCellValue('E1', 'Course Summary');
		/* 	->setCellValue('F1', 'Topic Name')
			->setCellValue('G1', 'Duration')
			->setCellValue('H1', 'Course Summary')
			->setCellValue('I1', 'Tags'); */
			
			
  if(is_siteadmin()){
	  
	  
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

	$categorytypes =	$DB->get_records_sql("SELECT c.id,c.fullname,c.shortname,c.summary,cc.id as catid,cc.name,cc.parent,c.courselevel,c.coursetype,c.points,c.hours,c.mints FROM mdl_course c join mdl_course_categories cc on c.category = cc.id where c.coursetype != 3 and cc.id IN($subcat2) and c.visible = 1 and c.id > 1");
  } else {
	  global $USER;
	 	   $trers = $DB->get_record_sql("SELECT t1,t2,t3 from mdl_course_categorytype where id=$USER->usertype ");
$t = 0;
 if(!empty($trers->t3)){
	 $t = '1,2,3';
}  else if(!empty($trers->t2)){
	 $t = '1,2';
}  else if(!empty($trers->t1)){
	 $t = '1';
}    
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

 if(!empty($subcat2)){
	  $categorytypes =	$DB->get_records_sql("SELECT c.id,c.fullname,c.shortname,c.summary,cc.id as catid,cc.name,cc.parent,c.courselevel,c.coursetype,c.points,c.hours,c.mints FROM mdl_course c join mdl_course_categories cc on c.category = cc.id where c.coursetype != 3 and cc.id IN($subcat2) and c.tires IN($t) and c.visible = 1 and c.id > 1");
 }

  }
  
  

$s = 1;
$row = 2;
 foreach ($categorytypes as $categorytype) { 
	
 if($categorytype->parent == 0){
	 
	 
	 $cat = $categorytype->name ;
	 $sub = "-";
 } else {
	 $catid = $categorytype->parent ;
	 $catn = $DB->get_record_sql("SELECT name FROM  mdl_course_categories where id = $catid");
	 $sub = $categorytype->name ;
	 $cat = $catn->name;
 }


     $q1 = $DB->get_records_sql("SELECT cm.id,cm.module,cm.instance FROM `{course_modules}` as cm  where cm.course = $categorytype->id and cm.deletioninprogress = 0 and module != 9");
$ff = '';
foreach ($q1 as $q){
$mm = $DB->get_record_sql("SELECT name FROM {modules} where id = $q->module ");

 
 $tmn = 'mdl_'.$mm->name ;
  $actmm = $DB->get_record_sql("SELECT name FROM $tmn where id = $q->instance ");
 $ff .= $actmm->name. ',';
}

$ff = trim($ff,',') ;



if($categorytype->courselevel == 1){
	$vLevel =  "Beginner";
} else if($categorytype->courselevel == 2){
	$vLevel = "Intermediate"  ;
} else {
	$vLevel =  "Advanced";
}

$cts = $DB->get_record_sql("SELECT id,coursetype FROM {cm_course_type} where id = $categorytype->coursetype ");

if($categorytype->hours == '' && $categorytype->mints == ''){
	  $vDuration = 'N/A';
  }else if($categorytype->hours != '' && $categorytype->mints != ''){
   $vDuration = $categorytype->hours.' : '.$categorytype->mints;
  }else if($categorytype->hours == '' && $categorytype->mints != ''){
	  $vDuration = '00 : '.$categorytype->mints;
  }else if($categorytype->hours != '' && $categorytype->mints == ''){
	  $vDuration = $categorytype->hours.' : 00';
  }
 
 
 $summary = $categorytype->summary ;
		



$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$row, $s)
			->setCellValue('B'.$row, $cat)
			->setCellValue('C'.$row, $sub)
			->setCellValue('D'.$row, $categorytype->fullname)
            ->setCellValue('E'.$row, strip_tags($summary));
			/* ->setCellValue('F'.$row, $ff)
			->setCellValue('G'.$row, $vDuration)
			->setCellValue('H'.$row, strip_tags($summary))
			->setCellValue('I'.$row, '');
			 */
			$row++;
			$s++;

}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Anthology Catalog');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Anthology Catalog.xlsx"');
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