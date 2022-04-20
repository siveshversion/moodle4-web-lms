<?php
require_once('../../../config.php');
$vSearch = trim($_POST["search"],' ');
$vLPId = $_POST["lp_id"];


	$objSelectedCourses = $DB->get_record_sql("SELECT GROUP_CONCAT(lp_courseid SEPARATOR ', ') as selected_courses FROM `mdl_cm_lp_course` WHERE lp_id = $vLPId");
	$arrSelectedCourses = explode(',',$objSelectedCourses->selected_courses);
	
if($vSearch == ''){

	
if($objSelectedCourses->selected_courses != ''){
	
if(is_siteadmin()){
$objExistingCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.id in ($objSelectedCourses->selected_courses) and a.category = b.id");
	} else {
$objExistingCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.id in ($objSelectedCourses->selected_courses) and a.category = b.id and a.cm_bu_id = $USER->cm_bu_id");
	}	
	
//$objExistingCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.id in ($objSelectedCourses->selected_courses) and a.category = b.id");

foreach($objExistingCourses as $course){
echo '<label class="container">'.$course->fullname.' (<b>Category : </b>'.$course->catname.')';


echo '<input type="checkbox" checked=checked onclick="fnAjaxCall('.$course->id.');" id="chkCourse_'.$course->id.'">';

  
  echo '<span class="checkmark"></span>';
echo '</label>';
}


}
}

if($vSearch != ''){
if(is_siteadmin()){
$objCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.visible = 1 and (lower(a.fullname) like '%".strtolower($vSearch)."%' or lower(b.name) like '%".strtolower($vSearch)."%') and a.category = b.id");
} else {
$objCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.visible = 1 and (lower(a.fullname) like '%".strtolower($vSearch)."%' or lower(b.name) like '%".strtolower($vSearch)."%') and a.category = b.id and a.cm_bu_id = $USER->cm_bu_id");	
}
foreach($objCourses as $course){
echo '<label class="container">'.$course->fullname.' (<b>Category : </b>'.$course->catname.')';
if(in_array($course->id,$arrSelectedCourses)){
echo '<input type="checkbox" checked=checked onclick="fnAjaxCall('.$course->id.');" id="chkCourse_'.$course->id.'">';
}else{
echo '<input type="checkbox" onclick="fnAjaxCall('.$course->id.');" value="'.$course->id.'" id="chkCourse_'.$course->id.'">';
}
  echo '<span class="checkmark"></span>';
echo '</label>';
}
}


?>
