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
 * Lib for myloc
 *
 * @package    Custom Lib for myloc
 * @copyright  2020 Balamurugan M <bala.mr01@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

function get_rolelist(){
	global $DB;
	$roles = $DB->get_records_sql("SELECT * FROM {role} where sortorder >1 and id !=5 order by sortorder asc");
return $roles;
	
}


function get_manager_rolelist($userrole){
	global $DB;
	$roles = $DB->get_records_sql("SELECT * FROM {role} where sortorder >3 and id !=5 order by sortorder asc");
return $roles;
	
}

function get_rolename($rid){
	global $DB;
	$role = $DB->get_record_sql("SELECT name FROM {role} where id = $rid");
return $role->name;
	
}

function get_role_usercnt($rid,$realrole){
	global $DB;
	
		//$users = $DB->get_records_sql("SELECT * FROM {user} where role = $rid and deleted =0 and suspended = 0 ");
		$users = $DB->get_records_sql("SELECT * FROM {user} where id in (SELECT userid FROM {role_assignments} WHERE userid in (SELECT id FROM {user} WHERE deleted =0 and suspended = 0 and role = $rid )
	AND roleid =$realrole)");
	
return $users;
	
}
function get_darole_usercnt($rid,$cm_bu_id){
	global $DB;
	if(is_siteadmin()){
		$users = $DB->get_records_sql("SELECT * FROM {user} where role = $rid and deleted =0 and suspended = 0 ");
	}else{
		$users = $DB->get_records_sql("SELECT * FROM {user} where role = $rid and cm_bu_id = $cm_bu_id and deleted =0 and suspended = 0");
	}
return $users;
	
}
function get_userbuname($buid){
	global $DB;
	
		$buname = $DB->get_record_sql("SELECT bu_name FROM {cm_business_units} where id = $buid");
	
return $buname->bu_name;
	
}


function get_burole_usercnt($rid,$realrole,$cm_bu_id){
	global $DB;
	
		//$users = $DB->get_records_sql("SELECT * FROM {user} where role = $rid and cm_bu_id = $cm_bu_id and deleted =0 and suspended = 0");
	$users = $DB->get_records_sql("SELECT * FROM {user} where id in (SELECT userid FROM {role_assignments} WHERE userid in (SELECT id FROM {user} WHERE cm_bu_id=$cm_bu_id and deleted =0 and suspended = 0 and role = $rid)
	AND roleid =$realrole)");
return $users;
	
}



function get_cmrole_usercnt_new($rid,$realrole,$cm_bu_id){
	global $DB;
	
		$users = $DB->get_records_sql("SELECT * FROM {user} where id in (SELECT userid FROM {role_assignments} WHERE userid in (SELECT id FROM {user} WHERE cm_bu_id=$cm_bu_id and deleted =0 and suspended = 0 and role = $rid)
	AND roleid =$realrole)");
	
return $users;
	
}


/*
function get_burole_usercnt($rid,$cm_bu_id){
	global $DB;
	
		$users = $DB->get_records_sql("SELECT * FROM {user} where role = $rid and cm_bu_id = $cm_bu_id and deleted =0 and suspended = 0");
	
return $users;
	
}*/

function get_cm_users($val,$buid,$user_role){
     global $DB;
	 
	 //senthamizh code
	if($_REQUEST['sub']){	
	
	$cmstr = '';
	$tempqns = array();
	if($_REQUEST['dept'] != 0){
		$cmstr .= " cm_department = ".$_REQUEST['dept']." and ";
	}
	if($_REQUEST['location'] != 0){
		$cmstr .= "cm_location = ".$_REQUEST['location']." and ";
	}
	if($_REQUEST['positions'] != 0){
		$cmstr .= "cm_position = ".$_REQUEST['positions']." and ";
	}
	}
     if($val ==0){
		if(!empty($user_role)){
			 
			if($cmstr != ''){
			$cmstr = rtrim($cmstr," and ");
			$users = $DB->get_records_sql("select * from {user} u where u.deleted=0 and u.id > 2 and role = $user_role and $cmstr order by u.firstname,u.lastname asc ");
			}else {
			 $users = $DB->get_records_sql("select * from {user} u where u.deleted=0 and u.id > 2 and role = $user_role order by u.firstname,u.lastname asc ");
			}
		}else{
			if($cmstr != ''){
			$cmstr = rtrim($cmstr," and ");
			$users = $DB->get_records_sql("select * from {user} u where u.deleted=0 and u.id > 2 and $cmstr order by u.firstname,u.lastname asc ");
			}else {
			$users = $DB->get_records_sql("select * from {user} u where u.deleted=0 and u.id > 2 order by u.firstname,u.lastname asc ");
			}
		}
     }else {
		if(!empty($user_role)){
			if($cmstr != ''){
			$cmstr = rtrim($cmstr," and ");
			$users = $DB->get_records_sql("select * from {user} u where u.deleted=0 and u.id > 2  and role = $user_role and cm_bu_id = $buid and $cmstr order by u.firstname,u.lastname asc ");
			}else {
			$users = $DB->get_records_sql("select * from {user} u where u.deleted=0 and u.id > 2  and role = $user_role and cm_bu_id = $buid order by u.firstname,u.lastname asc ");
			}
		
		}else{
			if($cmstr != ''){
			$cmstr = rtrim($cmstr," and ");
			 $users = $DB->get_records_sql("select * from {user} u where u.deleted=0 and u.id > 2  and cm_bu_id = $buid and $cmstr order by u.firstname,u.lastname asc ");

			}else {
			$users = $DB->get_records_sql("select * from {user} u where u.deleted=0 and u.id > 2  and cm_bu_id = $buid order by u.firstname,u.lastname asc ");
			}
		}
     }
     return $users;
 }
 
   function get_user_buname($buid){
     global $DB;
     
     $buname = $DB->get_record_sql("select bu_name from {cm_business_units} cbu where cbu.id = $buid");
     return $buname->bu_name;
  
 }
 
   function get_users_batch_name_new($userid){
	 global $DB;
	 $ubatch= $DB->get_record_sql("SELECT group_concat(name) as bname from {cohort} where id in (SELECT cohortid FROM {cohort_members}
	 WHERE userid = $userid)");
	 return $ubatch->bname;
 }
 
 function get_all_my_course_cnt($buid){
	global $DB,$USER;
	if(is_siteadmin()){
		$course = $DB->get_records_sql("select * from {course} c where c.visible =1 order by c.id desc");
	}else{
		$course = $DB->get_records_sql("select * from {course} c where c.visible =1 and cm_bu_id = $USER->cm_bu_id order by c.id desc");
	}
	
	return $course;
}


function get_estudent_count($courseid){
	 global $DB,$USER;
     $contextid = $DB->get_record_sql("SELECT id FROM {context} WHERE contextlevel = 50 AND instanceid=$courseid");
	 $contextids = $contextid->id;
	 if(!empty($contextids)){
		 $userids =	$DB->get_record_sql("SELECT GROUP_CONCAT(userid) as userids FROM {role_assignments} WHERE contextid =$contextids AND roleid =5");
		 $userid = $userids->userids;
		 $userid = trim($userid,',');
		 if(!empty($userid)){
			 if(is_siteadmin()){
				$eusers =	$DB->get_records_sql("SELECT * FROM {user} WHERE id IN($userid)  AND id>2 and deleted =0");
			 }else{
				$eusers =	$DB->get_records_sql("SELECT * FROM {user} WHERE id IN($userid) AND cm_bu_id=$USER->cm_bu_id AND id>2 and deleted =0"); 
			 }
		 }else{
			 $eusers = array();
		 }
	 }else{
			 $eusers = array();
		 }
     return $eusers;
 }
 
  function get_efaculty_count($courseid){
	 global $DB,$USER;
     $contextid = $DB->get_record_sql("SELECT id FROM {context} WHERE contextlevel = 50 AND instanceid=$courseid");

	 $contextids = $contextid->id;
	  if(!empty($contextids)){
		 $efaculty =	$DB->get_record_sql("SELECT GROUP_CONCAT(userid) as userids FROM {role_assignments} WHERE contextid =$contextids AND roleid =3");
		 $userid = $efaculty->userids;
		
		 if(!empty($userid)){
			 
			  if(is_siteadmin()){
				$efacultys =	$DB->get_records_sql("SELECT * FROM {user} WHERE id IN($userid) AND id>2 and deleted =0");
			  }else{
				  $efacultys =	$DB->get_records_sql("SELECT * FROM {user} WHERE id IN($userid) AND cm_bu_id=$USER->cm_bu_id AND id>2 and deleted =0");
			  }
		 }else{
			 $efacultys = array();
		 }
	}else{
			 $efacultys = array();
	 }
     return $efacultys;
 }
 
  function get_course_enrolid($courseid){
	 global $DB,$USER;
     $enrolid = $DB->get_record_sql("SELECT * FROM {enrol}  WHERE courseid=$courseid AND enrol = 'manual' AND STATUS =0");
	 return $enrolid->id;
 }
 
  function get_course_bu_name($cid){
	 global $DB;
	 $ccate= $DB->get_record_sql("Select bu_name from {cm_business_units} where id = (SELECT cm_bu_id FROM {course} WHERE id =$cid)");
	 return $ccate->bu_name;
 }
 
  function get_user_bu_name($cm_bu_id){
	  global $DB;
	 $buname= $DB->get_record_sql("SELECT bu_name FROM {cm_business_units} WHERE id =$cm_bu_id");
	 return $buname->bu_name;
 }
 
 function get_all_course_cnt(){
	global $DB,$USER;
		$course = $DB->get_records_sql("SELECT * FROM {course} WHERE  visible =1 and id != 1 order by id desc");
	
	return $course;
}

function get_bu_name($proid){
	 global $DB;
	 $bu_id = $DB->get_record_sql("SELECT cm_bu_id FROM {course}  WHERE id =$proid");
	  if(!empty($bu_id->cm_bu_id)){
	 $bu_names = $DB->get_record_sql("SELECT GROUP_CONCAT(bu_name) AS collegename FROM {cm_business_units} where id in
	($bu_id->cm_bu_id)");
	$res = $bu_names->collegename;
		 }else{
			 $res = "";
		 }

	return $res;
	
 }
 
 function get_my_createdcourse($courseid,$bu_id){
	 	global $DB,$USER;
		$res = 0;
		$mcourse = $DB->get_record_sql("SELECT id FROM {course} WHERE  visible =1 and id != 1 and id=$courseid and cm_bu_id = $bu_id ");
		if(!empty($mcourse->id)){
			$res = 1;
		}else{
			$res = 0;
		}
	return $res ;
 }