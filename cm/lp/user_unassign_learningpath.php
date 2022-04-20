<?php

/**
 * Competency - Learning Path
 *
 * @package    Learning Path
 * @copyright  2019 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../../config.php';
global $CFG, $DB, $USER;
$PAGE->set_pagetype('site-index');
$PAGE->set_other_editing_capability('moodle/course:manageactivities');
$PAGE->set_docs_path('');
$PAGE->set_pagelayout('frontpage');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);
require_login();

if ($_POST['data']) {

    $user_data = $_POST['data'];
    $user_details = explode('-', $user_data);
    $lp_id = $user_details[0];
    $userid = $user_details[1];
    $uid = rtrim($userid, ",");
    $uids = explode(',', $uid);
    $uresult = sizeof($uids);
    foreach ($uids as $k => $v) {

        $res = $DB->delete_records('cm_lp_assignment', array('lp_id' => $lp_id, 'userid' => $v));
        $courseid = $DB->get_records_sql("select lp_courseid as courseid from {cm_lp_course} where lp_id = $lp_id");
        foreach ($courseid as $ck => $course) {
            $sql = $DB->get_record_sql("select id from {enrol} where id = (select enrolid from {user_enrolments} where userid = $v) and
                    courseid = $course->courseid and  enrol = 'manual' and status = 0");
            $enrolid = $sql->id;
            if (!empty($enrolid)) {
                $DB->delete_records('user_enrolments', array('userid' => $v, 'enrolid' => $enrolid));
            }
            $sql_context = $DB->get_record_sql("SELECT id from mdl_context where instanceid = $course->courseid and contextlevel = 50");
            $contextid = $sql_context->id;
            if (!empty($contextid)) {
                $DB->delete_records('role_assignments', array('contextid' => $contextid, 'userid' => $v, 'roleid' => 5));
            }
        }
    }
    $ccount = $DB->get_record_sql("select count(*) as cnt from {cm_admin_learning_path} where id=$lp_id");
    $masterupdate = new stdClass();
    $masterupdate->id = $lp_id;
    $masterupdate->usercnt = $ccount->cnt;
    $masterupdate->lastmodified = time();
    $masterupdate->modifier = $USER->id;
    $updateid = $DB->update_record('cm_admin_learning_path', $masterupdate);
}
