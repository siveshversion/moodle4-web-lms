<?php
require_once('../../../config.php');
$vChecked = $_POST["lp_checked"];
$vCourseId = $_POST["lp_courseid"];
$vLPId = $_POST["lp_id"];
					if($vChecked == 1){
							$coursedid = new stdClass();
							$coursedid->lp_id = $vLPId;
							$coursedid->lp_type = '1';       
							$coursedid->creator = $USER->id;
							$coursedid->timecreated = time();
							$coursedid->lp_courseid = $vCourseId;
							$coursedid->status = 1;
							$instcid = $DB->insert_record('cm_lp_course', $coursedid);  
					}else if($vChecked == 0){
						$DB->delete_records('cm_lp_course',array('lp_id'=>$vLPId,"lp_courseid"=>$vCourseId));
					}


?>

