<?php

/**
 * Competency - Learning Path
 *
 * @package    Learning Path
 * @copyright  2019 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../config.php';
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

    $courseid = $DB->get_records_sql("select lp_courseid as courseid, ctype from {cm_lp_course} where lp_id = $lp_id");
    foreach ($uids as $k => $v) {
        $userdid = new stdClass();
        $userdid->lp_id = $lp_id;
        $userdid->lp_type = '1';
        $userdid->userid = $v;
        $userdid->timecreated = time();
        $userdid->status = 1;

        foreach ($courseid as $ck => $course) {
            $userdid->courseid = $course->courseid;
            $userdid->ctype = $course->ctype;
            $insertedid = $DB->insert_record('cm_lp_assignment', $userdid);
            $enrolid = $DB->get_record_sql("select id from {enrol} where courseid =$course->courseid and enrol='manual' and status =0");
            if (!$DB->record_exists('user_enrolments', array('enrolid' => $enrolid->id, 'userid' => $v))) {
                $usrenrol = new stdClass();
                $usrenrol->status = "0";
                $usrenrol->enrolid = $enrolid->id;
                $usrenrol->userid = $v;
                $usrenrol->timestart = time();
                $usrenrol->timeend = "0";
                $usrenrol->modifierid = $USER->id;
                $usrenrol->timecreated = time();
                $usrenrol->timemodified = time();
                $DB->insert_record('user_enrolments', $usrenrol);
            }

            $sql_context = $DB->get_record_sql("SELECT id from {context} where instanceid = $course->courseid and contextlevel = 50");
            if (!$DB->record_exists('role_assignments', array('contextid' => $sql_context->id, 'userid' => $v, 'roleid' => 5))) {
                $contextid = $sql_context->id;
                $groupmodify = new stdClass();
                $groupmodify->roleid = 5;
                $groupmodify->contextid = $contextid;
                $groupmodify->userid = $v;
                $groupmodify->timemodified = time();
                $groupmodify->modifierid = $USER->id;
                $groupmodify->component = "0";
                $groupmodify->itemid = "0";
                $groupmodify->sortorder = "0";
                $inserted = $DB->insert_record('role_assignments', $groupmodify);
            }
        }
    }
    $ccount = $DB->get_record_sql("select count(id) as cnt from {cm_admin_learning_path} where id=$lp_id");
    $ccount1 = $ccount->cnt;
    $masterupdate = new stdClass();
    $masterupdate->id = $lp_id;
    $masterupdate->coursecnt = $ccount1;
    $masterupdate->lastmodified = time();
    $masterupdate->modifier = $USER->id;
    $updateid = $DB->update_record('cm_admin_learning_path', $masterupdate);
}
