<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Allows you to edit course preference.
 *
 * @copyright 2016 Joey Andres  <jandres@ualberta.ca>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core_user
 */

require_once(__DIR__ . "/../../config.php");


@$data = $_POST['data'];

if($data != '') {
	$ids = explode('-', $data);
	$uid = $ids[0];
	$lpid = $ids[1];
	$uids = explode(',', $uid);
    $uresult = sizeof($uids);
    
	$existid = $DB->get_records_sql("select * from {cm_lp_assignment} where userid NOT IN($uid) and lp_id=$lpid and lp_type =1");
	foreach ($existid as $exid) {
	     $res = $DB->delete_records('cm_lp_assignment', array('id' => $exid->id));
		 
		 $courseid = $DB->get_records_sql("select lp_courseid as courseid from {cm_lp_course} where lp_id = $lpid");
        foreach ($courseid as $ck => $course) {
			if(!empty($course->courseid)){
				$enrolid = $DB->get_record_sql("select id from {enrol} where courseid =$course->courseid and enrol='manual' and status =0");
         echo $enrolid->id ;

           
            if (!empty($enrolid->id)) {
                $DB->delete_records('user_enrolments', array('userid' => $exid->userid, 'enrolid' => $enrolid->id));
            }
			
            $sql_context = $DB->get_record_sql("SELECT id from mdl_context where instanceid = $course->courseid and contextlevel = 50");
            $contextid = $sql_context->id;
            if (!empty($contextid)) {
                $DB->delete_records('role_assignments', array('contextid' => $contextid, 'userid' => $exid->userid, 'roleid' => 5));
            }
		}
        }
		 
	}
	
    $courseid = $DB->get_records_sql("select lp_courseid as courseid, ctype from {cm_lp_course} where lp_id = $lpid");
    foreach ($uids as $k => $v) {
				
	if (!$DB->record_exists('cm_lp_assignment', array('lp_id' => $lpid, 'userid' => $v, 'lp_type' => 1))) {
	
        $userdid = new stdClass();
        $userdid->lp_id = $lpid;
        $userdid->lp_type = '1';
        $userdid->userid = $v;
        $userdid->timecreated = time();
        $userdid->status = 1;
        
        foreach ($courseid as $ck => $course) {
            $userdid->courseid = $course->courseid;
            $userdid->ctype = $course->ctype;
            $userdid->goal_start_date = date("d-m-Y");
            $userdid->goal_end_date = $endday;
            $insertedid = $DB->insert_record('cm_lp_assignment', $userdid);
				
			if(!empty($course->courseid)){	
									
            $enrolid = $DB->get_record_sql("select id from {enrol} where courseid =$course->courseid and enrol='manual' and status =0");
            if(!empty($enrolid->id)){
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
           if(!empty($sql_context->id)){
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
			}
        }
		
		if(isset($insertedid)){
			
		$userdata = $DB->get_record('user', array('id' => $v));
        $supportuser = $DB->get_record('user', array('id' => $USER->id));
		
		$lpn = $DB->get_record('cm_admin_learning_path', array('id' => $lpid));

		
        $a = new stdClass();
		$a->firstname   = $userdata->firstname;
		$a->lastname    = $userdata->lastname;
		$a->lpn        	= $lpn->lpname;
		$a->link        = 'https://theacademy.campusmgmt.com/lms/index.php?saml=off';


		if($userdata->ctype == 'Customer'){
			$message = get_string('lpucustomer', '', $a);
			$messagehtml = text_to_html(get_string('lpucustomer', '', $a), false, false, true);
		} else if($userdata->ctype == 'Internal'){
			$message = get_string('lpuinternal', '', $a);
			$messagehtml = text_to_html(get_string('lpuinternal', '', $a), false, false, true);
		}  else if($userdata->ctype == 'Partner'){
			$message = get_string('lpupartner', '', $a);
			$messagehtml = text_to_html(get_string('lpupartner', '', $a), false, false, true);
		}
		
        $subject = "You have been Enrolled into a Learning Plan - $lpn->lpname";
        //email_to_user($userdata, $supportuser, $subject, $message,  $messagehtml);
			
			
		}
    }
	}


}


