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
    $ctype = $course_details[2];
    
    $id = $DB->get_record_sql("select id from {cm_lp_course} where lp_id=$lp_id and lp_courseid=$courseid");
    $aid = $id->id;

    $masterupdate = new stdClass();
    $masterupdate->id = $aid;
    $masterupdate->ctype = $ctype;
    $masterupdate->timecreated = time();
    $masterupdate->creator = $USER->id;

    $updateid = $DB->update_record('cm_lp_course', $masterupdate);

    $ctypeids = $DB->get_records_sql("select id from {cm_lp_assignment} where lp_id=$lp_id and courseid=$courseid");

    foreach ($ctypeids as $ck => $row) {
        $masterupdate1 = new stdClass();
        $masterupdate1->id = $row->id;
        $masterupdate1->ctype = $ctype;
        $masterupdate1->timecreated = time();
        $updateid = $DB->update_record('cm_lp_assignment', $masterupdate1);
    }
}
	
 
	 

 






