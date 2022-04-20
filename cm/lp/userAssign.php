<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 * 
 * 
 * @package    custom learning path
 * @subpackage competency
 * @copyright  2020 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/cm/lib/cmlib.php');

global $CFG, $USER, $DB;
$site = get_site();
$id = '';

$PAGE->set_pagelayout('standard');
$PAGE->set_context(context_system::instance());

$PAGE->set_url('/cm/lp/userAssign.php');
$PAGE->set_title('Assign Users to Learning Plan');
$PAGE->set_heading('Assign Users to Learning Plan');

require_login();

echo $OUTPUT->header();
?>
<style>
h1 {
font-size: 24px;
}
h3{
	font-size: 20px;
}
#page-header {
	 display : none !important;
}
</style>


<?php
$lpid = $_GET['lpid'];

if (empty($lpid)) {
    $lpid = 1;
} else {
    $lpid = $lpid;
}


//$assigneduser_details = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username,ctype,dept,cm_role from {user} where id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0");

if(!empty($_POST['assusers'])){
		$vSearch = $_POST['assusers'];
		if(is_siteadmin()){
		$assigneduser_details = $DB->get_records_sql("select * from (select u.id,concat(firstname,' ',lastname) as fullname,email,username from {user} u where id > 2 and id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0 and username != 'guest') a where  UPPER(a.fullname) like UPPER('%$vSearch%') ");
		} else {
		$assigneduser_details = $DB->get_records_sql("select * from (select u.id,concat(firstname,' ',lastname) as fullname,email,username from {user} u where id > 2 and id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0 and username != 'guest' and u.cm_bu_id = $USER->cm_bu_id) a where  UPPER(a.fullname) like UPPER('%$vSearch%') or UPPER(a.username) like UPPER('%$vSearch%') or UPPER(a.email) like UPPER('%$vSearch%')");
	
		}
	} else{
		if(is_siteadmin()){
		$assigneduser_details = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username from {user} where id > 2 and id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0");
		} else {

		$assigneduser_details = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username from {user} where id > 2 and id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0 and cm_bu_id = $USER->cm_bu_id");
			
		}
	}

if(count($assigneduser_details) == 0){
	
	if(!empty($_POST['allusers'])){
		$vSearch = $_POST['allusers'];
		if(is_siteadmin()){
		$user_details = $DB->get_records_sql("select * from (select u.id,concat(firstname,' ',lastname) as fullname,email,username from {user} u where id > 2 and deleted =0 and username != 'guest') a where UPPER(a.fullname) like UPPER('%$vSearch%') ");
		} else {
		$user_details = $DB->get_records_sql("select * from (select u.id,concat(firstname,' ',lastname) as fullname,email,username from {user} u where id > 2 and deleted =0 and username != 'guest' and u.cm_bu_id = $USER->cm_bu_id) a where UPPER(a.fullname) like UPPER('%$vSearch%') or UPPER(a.username) like UPPER('%$vSearch%') or UPPER(a.email) like UPPER('%$vSearch%') ");
		}
	} else {
		if(is_siteadmin()){
		$user_details = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username from {user} where id > 2 and deleted = 0 ");
		} else {
		$user_details = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username from {user} where id > 2 and deleted = 0 and cm_bu_id = $USER->cm_bu_id ");
	
		}
	}
} else {
	
	 if(!empty($_POST['allusers'])){
		$vSearch = $_POST['allusers'];
if(is_siteadmin()){
$auser = $DB->get_record_sql("select GROUP_CONCAT(id) as id from {user} where id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0");

$user_details = $DB->get_records_sql("select * from (select u.id,concat(firstname,' ',lastname) as fullname,email,username from {user} u  where deleted =0 and u.id NOT IN($auser->id) and username != 'guest') a where UPPER(a.fullname) like UPPER('%$vSearch%') or UPPER(a.username) like UPPER('%$vSearch%') or UPPER(a.email) like UPPER('%$vSearch%') "); 
} else {
$auser = $DB->get_record_sql("select GROUP_CONCAT(id) as id from {user} where id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0");

$user_details = $DB->get_records_sql("select * from (select u.id,concat(firstname,' ',lastname) as fullname,email,username from {user} u  where deleted =0 and u.id NOT IN($auser->id) and username != 'guest' and u.cm_bu_id = $USER->cm_bu_id) a where UPPER(a.fullname) like UPPER('%$vSearch%') or UPPER(a.username) like UPPER('%$vSearch%') or UPPER(a.email) like UPPER('%$vSearch%')"); 
	
}
	 } else {
	if(is_siteadmin()){	 
$auser = $DB->get_record_sql("select GROUP_CONCAT(id) as id from {user} where id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0");

$user_details = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username from {user} where deleted = 0 and id NOT IN($auser->id) and username != 'guest' ");
	} else {
$auser = $DB->get_record_sql("select GROUP_CONCAT(id) as id from {user} where id in (select userid from {cm_lp_assignment} where lp_id =$lpid group by userid) and deleted =0");

$user_details = $DB->get_records_sql("select id,concat(firstname,' ',lastname) as fullname,email,username from {user} where deleted = 0 and id NOT IN($auser->id) and username != 'guest' and cm_bu_id = $USER->cm_bu_id ");
	
	}

	}
}


?>

<link href="css/picklist.css" rel="stylesheet" type="text/css"/>
<?php $lpn = $DB->get_record('cm_admin_learning_path', array('id' => $lpid));
 ?>
<h3>Assign users to <?php echo $lpn->lpname ; ?> </h3>
<div><a class="cusLink" style="float:right; border: 1px solid;
        padding: 1px 10px;
        margin-bottom : 20px;
        background-color: #1177d1;
        color: #fff;" href="learningpath_list.php"> Back </a></div>
<br>
<br>



<?php
$noofenddate  = $lpn->lpdays;
$edate = date("d-m-Y");
$endday = date('d-m-Y', strtotime($edate. ' + '.$noofenddate.' days'));

if($_POST['add'] != ''){
 $uid = implode(',',$_POST['addselect']);

 $uids = explode(',', $uid);
    $uresult = sizeof($uids);
   
    $courseid = $DB->get_records_sql("select lp_courseid as courseid, ctype from {cm_lp_course} where lp_id = $lpid");
    if(!empty($courseid)){
	foreach ($uids as $k => $v) {
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
				
				//echo "enrol user"; exit;
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
				//echo "role user"; exit;
            }
		}
			}
			}
        }
		
		if(isset($insertedid)){
			
		$userdata = $DB->get_record('user', array('id' => $v));
        $supportuser = $DB->get_record('user', array('id' => $USER->id));
		
		/* $lpn = $DB->get_record('cm_admin_learning_path', array('id' => $lpid));

		
        $a = new stdClass();
		$a->firstname   = $userdata->firstname;
		$a->lastname    = $userdata->lastname;
		$a->lpn        	= $lpn->lpname;
		$a->link        = 'https://theacademy.campusmgmt.com/lms/index.php?saml=off';

		$message = get_string('lpupartner', '', $a);
		$messagehtml = text_to_html(get_string('lpupartner', '', $a), false, false, true);
		
		
        $subject = "You have been Enrolled into a Learning Plan - $lpn->lpname"; */
        //email_to_user($userdata, $supportuser, $subject, $message,  $messagehtml);
			
			
		}
    }
}else{
	 redirect("learningpath_list.php", 'Please assign courses to learning plan and then try to assign user ', 3);
}

       redirect("learningpath_list.php", 'User assigned successfully..', 3);

}



if($_POST['remove'] != ''){
	
$uid = implode(',',$_POST['removeselect']);
$uids = explode(',', $uid);
	 
    $uresult = sizeof($uids);
	
    foreach ($uids as $k => $v) {
		
        $res = $DB->delete_records('cm_lp_assignment', array('lp_id' => $lpid, 'userid' => $v));
		$userpointid = $DB->get_record_sql("select id from {cm_user_points} where point_type=2 and point_refid = $lpid and userid =$v");
		if(!empty($userpointid->id)){
		$res = $DB->delete_records('cm_user_points', array('id' => $userpointid->id));
		}
		$userlppointid = $DB->get_record_sql("select id from {cm_lp_completion_stauts} where lp_id=$lpid and userid = $v");
		if(!empty($userlppointid->id)){
		$res = $DB->delete_records('cm_lp_completion_stauts', array('id' => $userlppointid->id));
		}
	}
	
	foreach ($uids as $k => $v) {
	   $courseid = $DB->get_records_sql("select lp_courseid as courseid from {cm_lp_course} where lp_id = $lpid");
	   
        foreach ($courseid as $ck => $course) {
			if(!empty($course->courseid)){
				$enrolid = $DB->get_record_sql("select id from {enrol} where courseid =$course->courseid and enrol='manual' and status =0");
            if(!empty($enrolid->id)){

           
            if (!empty($enrolid->id)) {
                $DB->delete_records('user_enrolments', array('userid' => $v, 'enrolid' => $enrolid->id));
				$usercpointid = $DB->get_record_sql("select id from {cm_user_points} where point_type=1 and point_refid = $course->courseid and userid =$v");
				if(!empty($usercpointid->id)){
				$res = $DB->delete_records('cm_user_points', array('id' => $usercpointid->id));
				}
				
				
            }
			}
            $sql_context = $DB->get_record_sql("SELECT id from mdl_context where instanceid = $course->courseid and contextlevel = 50");
            $contextid = $sql_context->id;
            if (!empty($contextid)) {
                $DB->delete_records('role_assignments', array('contextid' => $contextid, 'userid' => $v, 'roleid' => 5));
            }
		}
        }
	
    }
 redirect("userAssign.php?lpid=$lpid", 'User unassigned successfully..', 3);

}

 ?>

<form name="form1" id="form1" method="post" action="">
<table id="assigningrole" summary="" class="admintable roleassigntable generaltable" cellspacing="0">
    <tbody><tr>
      <td id="existingcell">
          <p><label for="removeselect">Assigned users</label></p>
          <div class="userselector" id="removeselect_wrapper">
<select name="removeselect[]" id="removeselect" multiple="multiple" size="20" class="form-control no-overflow">
  <optgroup label="Assigned users (<?php echo count($assigneduser_details) ; ?>)">
  <?php foreach($assigneduser_details as $user){
	  $bu_name = $DB->get_record_sql("select bu_name from {cm_business_units} where id  = (SELECT cm_bu_id from {user} WHERE id=$user->id)");
	$urole = $DB->get_record_sql("SELECT role from {user} WHERE id=$user->id");
							if($urole->role == 5) {
								$role = get_string('c_student');
							}else if($urole->role == 3){
								$role = get_string('c_ctrainer');
							}else if($urole->role == 2){
								$role = get_string('c_admin');
							}else if($urole->role == 4){
								$role = get_string('c_tmanager');
							}else if($urole->role == 6){
								$role = get_string('c_cmanager');
							}else{
								if(is_siteadmin()){
									$role = get_string('c_groupadmin');
								}
							}
		$ufullname = $user->fullname ." (". $user->email.") (". $role."), (".$bu_name->bu_name.")";
	//$jobrole = $DB->get_record_sql("SELECT name FROM mdl_cm_jobroles where id = $user->cm_role");
	  ?>
    <option value="<?php echo $user->id ;?>"><?php echo $ufullname;  ?></option>
	<?php } ?>
  </optgroup>
</select>
<div class="form-inline">

<label for="removeselect_searchtext">Search</label>

<input type="text" name="assusers" id="assusers" size="15" value="<?php  echo $_REQUEST['assusers'];?>" onkeyup="sendform(this.value)" class="form-control">
<input type="submit" name="assbn" style="display:none;" value="Search">
<button name="clr" onclick="ref()" id="clr" style="
    padding: 4px 12px;
    margin-left: 1px;
    border-color: #ccc;
">Clear</button>

</div></div>


      </td>
      <td id="buttonscell">
          <div id="addcontrols">
              <input name="add" id="add" type="submit" value="◄&nbsp;Add" title="Add" class="btn btn-secondary" style="
    background-color: #ccc;
    color: #212529;
    background-color: #d9dce0;
    border-color: #d9dce0;
    border-radius: 0px;
"><br>
          </div>

          <div id="removecontrols">
              <input name="remove" id="remove" type="submit" value="Remove&nbsp;►" title="Remove" class="btn btn-secondary" style="
    background-color: #ccc;
    color: #212529;
    background-color: #d9dce0;
    border-color: #d9dce0;
    border-radius: 0px;
">
          </div>
      </td>
      <td id="potentialcell" style="width: 41%;">
          <p><label for="addselect">Potential users</label></p>
          <div class="userselector" id="addselect_wrapper">
<select name="addselect[]" id="addselect" multiple="multiple" size="20" class="form-control no-overflow">
  <optgroup label="Potential users (<?php echo count($user_details); ?>)">
    <?php foreach($user_details as $user){
		$bu_name = $DB->get_record_sql("select bu_name from {cm_business_units} where id  = (SELECT cm_bu_id from {user} WHERE id=$user->id)");
	$urole = $DB->get_record_sql("SELECT role from {user} WHERE id=$user->id");
							if($urole->role == 5) {
								$role = get_string('c_student');
							}else if($urole->role == 3){
								$role = get_string('c_ctrainer');
							}else if($urole->role == 2){
								$role = get_string('c_admin');
							}else if($urole->role == 4){
								$role = get_string('c_tmanager');
							}else if($urole->role == 6){
								$role = get_string('c_cmanager');
							}else{
								if(is_siteadmin()){
									$role = get_string('c_groupadmin');
								}
							}
		$ufullname = $user->fullname ." (". $user->email.") (". $role."), (".$bu_name->bu_name.")";
	//$jobrole = $DB->get_record_sql("SELECT name FROM mdl_cm_jobroles where id = $user->cm_role");
	  ?>
	<option value="<?php echo $user->id ; ?>"><?php echo $ufullname; ?></option>
	<?php } ?>
  </optgroup>
</select>
<div class="form-inline">
<label for="addselect_searchtext">Search</label>
<input type="text" name="allusers" id="allusers" size="15" value="<?php  echo $_REQUEST['allusers'];?>" onkeyup="sendform(this.value)" class="form-control">
<input type="submit" name="allbn" id="allbn" style="display:none;" value="Search"> 
<button name="clr2" onclick="ref2()" id="clr2" style="
    padding: 4px 12px;
    margin-left: 1px;
    border-color: #ccc;
">Clear</button>
</div></div>

      </td>
    </tr>
  </tbody></table>
  
</form>

<script>
function ref2(){
$('#allusers').val(' ');

}

function ref(){
$('#assusers').val(' ');

}

 function sendform(inputvalue)
{
	var iv = inputvalue.length;
	if(iv >= 3){
document.getElementById("form1").submit();
	}
// this is the value of input now you can submit your form
}
</script>
<?php
echo $OUTPUT->footer();
