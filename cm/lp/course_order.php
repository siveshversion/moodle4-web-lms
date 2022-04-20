<?php

/**
 * Competency - Learning Path
 *
 * @package    Learning Path 
 * @copyright  2019 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
global $CFG, $DB, $USER;
$PAGE->set_pagetype('site-index');
$PAGE->set_other_editing_capability('moodle/course:manageactivities');
$PAGE->set_docs_path('');
$PAGE->set_pagelayout('frontpage');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);

require_login();

if ($_POST['data']) {
    $course_data = $_POST['data'];
	$course_details = explode('-', $course_data);
	$lp_id = $course_details[1];
    $courseid = $course_details[0];
	$ccidd = trim($courseid,',');
    $course_details = explode(',', $ccidd);
    //print_object($course_details);
	
	$i = 1;
	foreach ($course_details as $cids) {
		
	
		 $id = $DB->get_record_sql("select id from {cm_lp_course} where lp_id=$lp_id and lp_courseid=$cids");

		
        $masterupdate1 = new stdClass();
        $masterupdate1->id = $id->id;
        $masterupdate1->sorder = $i;
        $updateid = $DB->update_record('cm_lp_course', $masterupdate1);
		$i++;
    }
	if(isset($updateid)){
	echo '1' ;
	}
 
}
	
 
	 

 






