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
 * CLI script to purge caches without asking for confirmation.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2011 David Mudrak <david@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/enrollib.php');
require_once($CFG->dirroot. '/course/lib.php');

global $CFG, $OUTPUT, $USER ;
 $site = get_site();

if ($CFG->forcelogin) {
    require_login();
}

$PAGE->set_heading($site->fullname);

	$objSelectedCourses = $DB->get_record_sql("SELECT GROUP_CONCAT(courseid SEPARATOR ',') as selected_courses FROM {$CFG->prefix}cm_my_lp where userid = $USER->id");
	$arrSelectedCourses = explode(',',$objSelectedCourses->selected_courses); 
	//print_object($objSelectedCourses);

	?>
	
	
<script src="js/jquery-3.3.1.js"></script>

<link href="css/fSelect.css" rel="stylesheet">
<script src="js/fSelect.js"></script>

<script>
(function($) {
    $(function() {
        window.fs_test = $('.test').fSelect();
    });
})(jQuery);
</script>
		<style>
#page-header {
	 display : none !important;
}
#accessibilitybar {
	display : none !important;
}
.navbar {
	display : none !important;
}

#nav-drawer{
	display : none !important;
}

#top-footer {
	display : none;
}
#page-footer {
display : none;
}

body.drawer-open-left {
    margin-left: 0px !important;
	background-color: #FFF;
}
#page {
	margin-top : 0px !important;
}

#region-main {
	border : none !important;
}

 </style>
<style>

.container {
  display: inline-block;
  margin-left:10px !important;
  position: relative;
  padding-left: 40px !important;
  padding-right: 40px !important;
  margin-bottom: 18px;
  cursor: pointer;
  font-size: 15px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.container input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.container:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.container input:checked ~ .checkmark {
  background-color: #008ACF ;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.container input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.container .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}
</style>
		<?php
		
echo $OUTPUT->header();

	echo $OUTPUT->heading('');



?>
<!--button onclick="myFunction()" class="cusButton">Click Me<i class="arrow down"></i></button-->
<div id="update_result"><span style="color:#FFF;">.</span></div>
<form name="frmLPCourses" method="post">
<input type="hidden" id="hidLPId" value="<?php echo $vLPId;?>">
<div class="cusFrame">
<div class="CusHeader">
Available Courses 
</div>

<div id="myDIV">
<div><?php $objCourses = $DB->get_records_sql("select c.id,c.fullname,(select name from {course_categories} where id = c.category) as catname from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid join {cm_courses} cc on cc.course_id = c.id where u.id = $USER->id and c.visible != 0 and c.category !=0 and cc.bu_id = $USER->cm_bu_id"); 
?>
<input type="text" placeholder="Search..." id="txtSearch" autocomplete="off"><?php if(sizeof($objCourses) > 0) { ?>(Courses available, please use Search...) <?php } else { ?> <b>(Courses Not Available, You are not enrolled any course) </b><?php } ?>
</div>
<div id="courses">
<?php // echo $objSelectedCourses->selected_courses;
if($objSelectedCourses->selected_courses != ''){ 
	if(is_siteadmin()){
$objExistingCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.id in ($objSelectedCourses->selected_courses) and a.category = b.id");
	} else {
		
//$objMyLPCourses = $DB->get_records_sql("select b.id as courseid,b.fullname,b.summary as coursename,b.visible,points from {$CFG->prefix}cm_my_lp a,{$CFG->prefix}course b where a.userid = $USER->id and a.courseid = b.id");
			
	
$objExistingCourses = $DB->get_records_sql("select c.id,c.fullname,(select name from {course_categories} where id = c.category) as catname from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid join {cm_courses} cc on cc.course_id = c.id where u.id = $USER->id and c.id in ($objSelectedCourses->selected_courses) and c.visible != 0 and c.category !=0 and cc.bu_id = $USER->cm_bu_id");
	//$objExistingCourses = $DB->get_records_sql("select a.id,a.fullname,a.shortname, (SELECT NAME FROM {course_categories} WHERE id = a.category) as catname from {course} a where a.visible =1 and a.cm_bu_id = $USER->cm_bu_id  OR a.id in (
//SELECT course_id FROM {cm_courses}  WHERE bu_id  = $USER->cm_bu_id)or a.id in ($objSelectedCourses->selected_courses) order by a.id DESC");	
	}
	//print_object($objExistingCourses);
foreach($objExistingCourses as $course){
echo '<label class="container">'.$course->fullname.' (<b>Category : </b>'.$course->catname.')';


echo '<input type="checkbox" checked=checked onclick="fnAjaxCall('.$course->id.');" value="'.$course->id.'" id="chkCourse_'.$course->id.'">';
  
  echo '<span class="checkmark"></span>';
echo '</label>';
}


}

if(is_siteadmin()){
if($objSelectedCourses->selected_courses != ''){ //c.id not in ($objSelectedCourses->selected_courses)
	$objCourses = $DB->get_records_sql("select c.id,c.fullname,(select name from {course_categories} where id = c.category) as catname from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid join {cm_courses} cc on cc.course_id = c.id where u.id = $USER->id and c.id not in ($objSelectedCourses->selected_courses) and c.visible != 0 and c.category !=0 and cc.bu_id = $USER->cm_bu_id limit 1,5");
	//$objCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.visible = 1 and a.id not in ($objSelectedCourses->selected_courses) and a.category = b.id limit 1,5");
}else{
	$objCourses = $DB->get_records_sql("select c.id,c.fullname,(select name from {course_categories} where id = c.category) as catname from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid join {cm_courses} cc on cc.course_id = c.id where u.id = $USER->id and c.visible != 0 and c.category !=0 and cc.bu_id = $USER->cm_bu_id limit 1,5");
}
} else {
if($objSelectedCourses->selected_courses != ''){
	//echo "select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b where a.visible = 1 and a.id not in ($objSelectedCourses->selected_courses) and a.category = b.id and a.cm_bu_id = $USER->cm_bu_id limit 1,5" ;
$objCourses = $DB->get_records_sql("select c.id,c.fullname,(select name from {course_categories} where id = c.category) as catname from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid join {cm_courses} cc on cc.course_id = c.id where u.id = $USER->id and c.id not in ($objSelectedCourses->selected_courses) and c.visible != 0 and c.category !=0 and cc.bu_id = $USER->cm_bu_id limit 1,5");
//$objCourses = $DB->get_records_sql("select a.id,a.fullname,a.shortname, (SELECT NAME FROM {course_categories} WHERE id = a.category) as catname from {course} a where a.visible =1 and a.cm_bu_id = $USER->cm_bu_id  OR a.id in (
//SELECT course_id FROM {cm_courses}  WHERE bu_id  = $USER->cm_bu_id) or a.id  not in ($objSelectedCourses->selected_courses) order by a.id DESC limit 1,5");	
}else{
	//echo "select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b  where a.visible = 1 and a.category = b.id and a.cm_bu_id = $USER->cm_bu_id limit 1,5";
	if($USER->role !=5){
	//$objCourses = $DB->get_records_sql("select a.id,a.fullname,b.name as catname from {$CFG->prefix}course a,{$CFG->prefix}course_categories b  where a.visible = 1 and a.category = b.id and a.cm_bu_id = $USER->cm_bu_id limit 1,5");
	}else{
	$objCourses = $DB->get_records_sql("select c.id,c.fullname,(select name from {course_categories} where id = c.category) as catname from {enrol}  e join {user_enrolments} ue on e.id = ue.enrolid join {course} c on e.courseid = c.id join {user} u on u.id = ue.userid join {cm_courses} cc on cc.course_id = c.id where u.id = $USER->id and c.visible != 0 and c.category !=0 and cc.bu_id = $USER->cm_bu_id limit 1,5");
	}
	//$objCourses = $DB->get_records_sql("select a.id,a.fullname,a.shortname, (SELECT NAME FROM {course_categories} WHERE id = a.category) as catname from {course} a where a.visible =1 and a.cm_bu_id = $USER->cm_bu_id  OR a.id in (
//SELECT course_id FROM {cm_courses}  WHERE bu_id  = $USER->cm_bu_id) order by a.id DESC limit 1,5");	
}	
}
foreach($objCourses as $course){
echo '<label class="container">'.$course->fullname.' (<b>Category : </b>'.$course->catname.')';


echo '<input type="checkbox" onclick="fnAjaxCall('.$course->id.');" value="'.$course->id.'" id="chkCourse_'.$course->id.'">';

  
  echo '<span class="checkmark"></span>';
echo '</label>';
}
?>
</div>
</div>
</div>
</form>
<style>
#update_result
{
margin:0px auto;
margin:5px;	
text-align:center;
color:#008ACF
}
.cusFrame{
	/*border:1px solid #008ACF;*/
}
.CusHeader{
	color:#008ACF;
	margin:0px auto;
	font-weight:bold;
	font-size:20px;
	
}
#txtSearch{
	margin-left: 0.5em;
    background: url(./img/search.png);
    background-repeat: no-repeat;
    background-position: left;
    background-size: 13px 13px;
    background-position-x: 5px;
    padding-left: 22px;
    margin: 10px 5px 5px 5px;
    border-radius: 5px;
    border: 1px solid #cccccc;
}
.cusCheckbox{
	margin:10px;
}
.arrow {
  border: solid black;
  border-width: 0 3px 3px 0;
  display: inline-block;
  padding: 3px;
}

.down {
  transform: rotate(45deg);
  -webkit-transform: rotate(45deg);
}

.mobilefooter {
display : none;	
}
</style>
<script>
function myFunction() {
  var x = document.getElementById("myDIV");
  if (x.style.display === "none") {
    alert("welcoem");
    x.style.display = "block";
  } else {
    alert("none");
    x.style.display = "none";
  }
}


$(document).on('keyup','#txtSearch',function(){
     $("#courses").html('fetching courses...');   
	 

    var vSearch = $(this).val();
	var vLPId = $('#hidLPId').val();
	
    $.ajax({
            url: './ajax/ajax_lp_user_courses.php',
            type: 'post',
            data: {search:vSearch,lp_id:vLPId},
            success:function(response){                              
				$("#courses").html(response);                 
            }
  
});
});


setTimeout(function(){
  if ($('#update_result').length > 0) {
    //$('#update_result').show();
  }
}, 5000)

function fnAjaxCall(vCourseId){
	  $('#update_result').show();
	  
	  		$("#update_result").html('Updadfting...'); 
 		
CheckboxId = "#chkCourse_"+vCourseId;	
vLPId = $('#hidLPId').val();


//alert(0);
if($(CheckboxId).prop("checked")){
	lp_checked = 1;
}else{
	lp_checked = 0;
}

  $.ajax({
            url: 'ajax/ajax_update_my_lp_home.php',
            type: 'post',
            data: {mode:lp_checked,courseid:vCourseId},
            success:function(response){  
			alert(response);
					$("#update_result").html('Courses Updated successfully!');    	              
            }  
  })
  
}


</script>
<?php 

echo $OUTPUT->footer();

