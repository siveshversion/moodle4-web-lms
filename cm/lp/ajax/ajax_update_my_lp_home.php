<?php
require_once('../../../config.php');
		
if(isset($_POST["courseid"])){
	$vCourseId = $_POST["courseid"];
}
if(isset($_POST["courseid"])){
	$vMode = $_POST["mode"];
}
if($vMode == 1){
	$objMyLP = new stdClass();
	$objMyLP->userid = $USER->id;
	$objMyLP->courseid = $vCourseId;
	$objMyLP->timemodified = time();
	$insrt = $DB->insert_record('cm_my_lp', $objMyLP);
	
	if(isset($insrt)){
		echo '<a href="javascript:void(o);" title="Remove this course from My Learning Plan" class="cusLink" onclick="fnMyLP('.$vCourseId.',0)"><img width="50%" src="'.$CFG->wwwroot.'/cm/lp/img/minus.png"></a>';
	}
}else{

	if($DB->delete_records('cm_my_lp',array("userid"=>$USER->id,"courseid"=>$vCourseId))){
		echo '<a href="javascript:void(o);" title="Add this course to My Learning Plan" class="cusLink" onclick="fnMyLP('.$vCourseId.',1)"><img width="50%" src="'.$CFG->wwwroot.'/cm/lp/img/plus22.png"></a>';
	}
}

?>