<?php

/**
 * Competency - Learning Path
 *
 * @package    Learning Path 
 * @copyright  2019 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../config.php');
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
    $lp_id = $course_details[0];
    $courseid = $course_details[1];
    $cid = rtrim($courseid, ",");
    $cids = explode(',', $cid);
    $cresult = sizeof($cids);
    foreach ($cids as $k => $v) {

        $coursedid = new stdClass();
        $coursedid->lp_id = $lp_id;
        $coursedid->lp_type = '1';
        $coursedid->creator = $USER->id;
        $coursedid->timecreated = time();
        $coursedid->lp_courseid = $v;
        $coursedid->status = 1;
        $insertedid = $DB->insert_record('cm_lp_course', $coursedid);
    }
    $ccount = $DB->get_record_sql("select count(id) as cnt from {cm_lp_course} where lp_id=$lp_id");

    $masterupdate = new stdClass();
    $masterupdate->id = $lp_id;
    $masterupdate->coursecnt = $ccount->cnt;
    $masterupdate->lastmodified = time();
    $masterupdate->modifier = $USER->id;
    $updateid = $DB->update_record('cm_admin_learning_path', $masterupdate);
}
	
 
	 

 






