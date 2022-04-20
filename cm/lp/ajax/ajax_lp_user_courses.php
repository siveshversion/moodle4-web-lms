<?php
require_once('../../../config.php');
$vSearch = trim($_POST["search"],' ');
$vLPId = $_POST["lp_id"];


	$objSelectedCourses = $DB->get_record_sql("SELECT GROUP_CONCAT(courseid SEPARATOR ',') as selected_courses FROM {$CFG->prefix}cm_my_lp where userid = $USER->id");
	$arrSelectedCourses = explode(',',$objSelectedCourses->selected_courses);
	
if($vSearch == ''){

	
if($objSelectedCourses->selected_courses != ''){
	/*$q1 = $DB->get_record_sql("select GROUP_CONCAT(c.id) as id from {course} c where c.visible =1  and c.cm_bu_id = $USER->cm_bu_id ");
$q2 = $DB->get_record_sql("SELECT GROUP_CONCAT(course_id) as id FROM {cm_courses} WHERE bu_id  = $USER->cm_bu_id");
$cids = $q1->id .','. $q2->id;
					
	$cidds = trim($cids,',');*/
	$q1 = $DB->get_record_sql("select GROUP_CONCAT(c.id) as id from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid join {cm_courses} cc on cc.course_id = c.id where u.id = $USER->id and c.visible != 0 and c.category !=0 and cc.bu_id = $USER->cm_bu_id");
					
	$cidds = trim($q1->id,',');
	if(!empty($cidds)){
$objExistingCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.id in ($objSelectedCourses->selected_courses) and a.category = b.id and a.id in ($cidds)");
	}
foreach($objExistingCourses as $course){
echo '<label class="container">'.$course->fullname.' (<b>Category : </b>'.$course->catname.')';


echo '<input type="checkbox" checked=checked onclick="fnAjaxCall('.$course->id.');" id="chkCourse_'.$course->id.'">';

  
  echo '<span class="checkmark"></span>';
echo '</label>';
}


}
}

if($vSearch != ''){
//$q1 = $DB->get_record_sql("select c.id,c.fullname,u.email AS Email,ue.timestart from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid join {cm_courses} cc on cc.course_id = c.id where u.id = $USER->id and c.visible != 0 and c.category !=0 and cc.bu_id = $USER->cm_bu_id ");
//$q2 = $DB->get_record_sql("SELECT GROUP_CONCAT(course_id) as id FROM {cm_courses} WHERE bu_id  = $USER->cm_bu_id");
//$cids = $q1->id .','. $q2->id;
$q1 = $DB->get_record_sql("select GROUP_CONCAT(c.id) as id from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid join {cm_courses} cc on cc.course_id = c.id where u.id = $USER->id and c.visible != 0 and c.category !=0 and cc.bu_id = $USER->cm_bu_id");
					
	 $cidds = trim($q1->id,',');
	if(!empty($cidds)){
		//echo "select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.visible = 1 and (lower(a.fullname) like '%".strtolower($vSearch)."%' or lower(b.name) like '%".strtolower($vSearch)."%') and a.category = b.id and a.id in($cidds)";
$objCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.visible = 1 and (lower(a.fullname) like '%".strtolower($vSearch)."%' or lower(b.name) like '%".strtolower($vSearch)."%') and a.category = b.id and a.id in($cidds)");
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
