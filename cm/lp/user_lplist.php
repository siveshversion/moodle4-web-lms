<?php
/**
 * Competency - Learning Path
 *
 * @package    Learning Path 
 * @copyright  2019 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once('../../config.php');
require_once($CFG->dirroot . '/cm/lib/cmlib.php');


$PAGE->set_context(context_system::instance());
global $CFG, $DB, $USER;
$site = get_site();

require_login();


$PAGE->set_url('/cm/user_lplist.php');

$PAGE->set_title('User Learning Plan List');
$PAGE->set_heading('');
$PAGE->set_pagelayout('standard');
echo $OUTPUT->header();
?>


<?php
if ($USER->id > 2) {
    $uid = $USER->id;
} else {
    $uid = $_GET['userid'];
}


if($_POST['submit'] != ''){
$rid = $_POST['rid'] ;
$cid = $_POST['cid'] ;
$sdate = $_POST['goal'] ;

$objRecord = new stdClass();
$objRecord->id = $rid;
$objRecord->goal_start_date = date('d-m-Y');
$objRecord->goal_end_date = $sdate;

$DB->update_record('cm_my_lp',$objRecord);
}
?>


<link href="css/styles.css" rel="stylesheet" type="text/css"/>
	<link href="fmt.css" rel="stylesheet" type="text/css">

<style>
#page-header {
	display : none;
}

nav.navbar ul.navbar-nav .popover-region .popover-region-toggle {
width :40px !important;	
}
.navbar .action-menu .userpicture{
	margin-left: 0px !important;
}
img.userpicture {
	margin-right: 0px !important;
	
}
.navbar .popover-region-header-actions .icon, .navbar .popover-region-toggle .icon {
	color : #1177d1 !important ;
}

    .progress {
        border: 0;
        background-image: none;
        filter: none;
        -webkit-box-shadow: none;
        -webkit-box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
        -moz-box-shadow: none;
        box-shadow: none;
        box-shadow: inset 0 1px 2px rgba(0,0,0,.1);
        height: 25px!important;
        overflow: hidden;
        background-color: #f5f5f5!important;
        border-radius: 3px!important;
        margin-bottom: -1px!important;
    }
    .w-full {
        width: 75%;
        margin-left: 39%;
       
    }
    .w-100 {
    width: 91%!important;
}
    p {
        margin:0px;
    }	
	
	.m-l-0 {
		margin-left: 3px!important;
	}

.text { 
 font-size: 15px;
}
.tl-grid-dashboard-course-wrapper {
	margin-right: 10px;
}

#region-main {
	margin-top: 25px;
}
.slicon-menu {
	margin-left: -2px;
    font-weight: bold;
}
</style>
	<div class="tabs">
	<input type="hidden" name="tval" id="tval" value=<?php echo $_GET['tab'] ; ?> >
  <input type="radio" name="tabs" id="tabone" >
  <label for="tabone"style="text-transform: uppercase;font-weight: bold;"
 >Assigned Learning Plans</label>
  <div class="tab">
    <span class="notifications" id="user-notifications"></span>
                    
	<?php
	 $uid = $USER->id;
                        $lpdetails = get_users_lp($uid);
                        if(count($lpdetails) > 0){
                        foreach ($lpdetails as $lpks => $lpvs) {
                            $lpids = get_lp_details($lpvs->lp_id);
                            $courseids = get_assigned_courses($lpids->id);
							$man_courses = get_lp_man_course($lpvs->lp_id);
							
                            $completed_course = get_user_coursecompletion($man_courses->courseids, $uid,$lpvs->lp_id);
                            $completed_coursecnt = get_user_coursecompletion_cnt($uid,$lpvs->lp_id);
							 $sedates = get_user_learning_path_enddate($uid, $lpvs->lp_id);
							 
							  if(!empty($lpids->lpimage)){
		  $lpimg = "$CFG->wwwroot/cm/lp/lpimages/$lpids->lpimage";
		 } else {
		 $lpimg = "$CFG->wwwroot/cm/1548346739_fundamentals.png";
		 }
			$progress = 0;
		    if (sizeof($completed_course) > 0) { 
												
                    $progress = ((sizeof($completed_course) / $man_courses->cnt) * 100) . "%";
					
			}
			

						$ccount = $DB->get_record_sql("select count(*) as cnt from {cm_lp_course} where lp_id=$lpids->id and lp_courseid > 0 ");

					  
                    if (($ccount->cnt) != 0) {
                        $cc = $ccount->cnt;
                    } else {
                       $cc = '0';
                    } 
			
?>
<div class="span2 tl-grid-dashboard-course-wrapper" data-coursesset="0"><div class="tl-grid-dashboard-image-wrapper text-center">
	<a href="../lp/lp_courselist.php?lpid=<?php echo $lpids->id; ?>&userid=<?php echo $uid;?>"><img class="avatar-square" src="<?php echo $lpimg ; ?>"></a>
	
	</div><div class="tl-grid-dashboard-course-title text-center"><div class="coursename tl-bold-item  tl-ellipsis">
	<a href="../lp/lp_courselist.php?lpid=<?php echo $lpids->id; ?>&userid=<?php echo $uid;?>">
	<span title="Introduction to TalentLMS (001)" class="tl-formatted-course-name"><?php echo ucfirst($lpids->lpname); ?> 
	<span class="tl-formatted-course-code">(<?php echo $cc ; ?>)</span></span></a></div>
	<div class="coursestatus"><span class="label label-progress tl-label-progress text-left tl-cursor-pointer tl-grid-mode-filter" data-filter="progress" title="Show courses in progress">
	<span class="label label-success" style="width:<?php echo round($progress);?>px;"><?php echo  round($progress)."%"; ?></span></span>
	
	</div></div>
	</div>	

	
<?php } 
		} else { ?>
		
	<div class="item">
<div class="span2 tl-grid-dashboard-course-wrapper" data-coursesset="0" style="width: 85%;box-shadow: none !important;margin-bottom: 300px;">
	<div class="tl-grid-dashboard-course-title " style="border:none"><div class="coursename tl-bold-item  tl-ellipsis" style="margin: 25px;">
	<a target="_blank" style="font-weight: bold;"><span title="" class="tl-formatted-course-name">
	<?php echo 'There are no learning plans assigned to you.
' ?> <span class="tl-formatted-course-code"></span></span></a></div>
	<div class="coursestatus">
	<!--<span class="label label-progress tl-label-progress text-left tl-cursor-pointer tl-grid-mode-filter" data-filter="progress" 
	title="Show courses in progress">-->
	<span class="label label-success" style="width:0px;display:none">0%</span>
	<!--</span>-->
	</div>
	
	</div>
	</div>

	</div>

	
		
		<?php } ?>
			
		
  </div>
  
  <input type="radio" name="tabs" id="tabtwo">
  <label for="tabtwo" style="text-transform: uppercase;font-weight: bold;"
 >My Learning Plan</label>
  
  
  
  <div class="tab">
  
  <div style="float:right;margin-top: -10px;padding: 10px;padding-right:0px;margin-bottom:10px;"> <a style='color: #fff !important;background-color: #1177d1;' class='btn btn-primary '  href='lp_user_courses.php' data-lity>
Add Courses
   </a></div> 
   
    <!--  <style>
.container_card  h5:before {
    content:"• ";
	color: #008acf !important;
}
  </style> -->
   <div style="clear:both"></div>
    <?php
				
		//Get courseid from My learning Plan (mdl_cm_my_lp) - Start

$objMyLPCourses = $DB->get_record_sql("SELECT GROUP_CONCAT(courseid SEPARATOR ',') as courseids FROM {$CFG->prefix}cm_my_lp where userid = $USER->id");
$arrMyLPCourses = explode(',',$objMyLPCourses->courseids);

//Get courseid from My learning Plan (mdl_cm_my_lp) - End


		$objMyLPCourses = $DB->get_records_sql("select b.id as courseid,b.fullname,b.summary as coursename,b.visible,points from {$CFG->prefix}cm_my_lp a,{$CFG->prefix}course b where a.userid = $USER->id and a.courseid = b.id");
		if(!empty($objMyLPCourses)){
		foreach($objMyLPCourses as $course){
			echo 		'<div class="" style="margin-bottom:20px;box-shadow: 3px 2px 6px 3px #ddd;
    background-color: #fff;" >';
	

  echo '<div class="column">
  

  
  <div class="card">';
  
  echo '<div class="container_card" style="padding: 15px 15px 0px 15px;">';
 /*  if(strlen($course->fullname) > 25){
	 $vCourseName = substr($course->fullname,0,25).'...';
    
  }else{ */
	 $vCourseName = $course->fullname;
 /*  } */
 
 if($course->course_type == 3){
		$courselink = $course->courselink;
$tag = "target=_blank";
		} else {
					$courselink = new moodle_url('/course/view.php', array('id' => $course->courseid,'visible' => 1));
$tag = "";
		}
 
  echo '<div style="float:left"><h4><b> <a class="cuLinkText" '.$tag.' href="'.$courselink.'">'.$vCourseName.'</a></b></h4></div>';
 
echo  '<div id="divMyLP_'.$course->courseid.'" style="float:right;margin-bottom:5px;"><a title="Remove this course from My Learning Plan" href="javascript:void(o);" class="cusLink" onclick="fnMyLP('.$course->courseid.',0)"><img style="width: 30px;
    margin-top: -4px;
    padding: 2px;" src="./img/minus.png"></a></div>';
	
 //$vPercentage = get_user_course_status($course->courseid,$USER->id); 
 $completed_course = get_user_courseprogress($USER->id,$course->courseid);
 
  $progress = 0;
if($completed_course ==2) {
                            $progress="100";
} else if($completed_course ==1) {
                                $progress ="50";
                           }else{
                               $progress ="0";
                           }
						   
						   
						   if($progress == 100){
$prg =  '<span style="background-color: green;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;margin-right: 12px;">Completed</span> ';
} else if($progress == 0){
$prg =  '<span style="background-color: red;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;margin-right: 12px;">Not Started</span> ';
} else {
$prg =  '<span style="background-color: blue;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;margin-right: 12px;">In Progress</span> ';

}
                  echo $prg ;


  echo '<div style="clear:both"></div>';
 

    echo '<div style="float:right" class="cusTextSmall">Points : <b> '.$course->points.' </b></div>';

	  if(strlen($course->summary) > 50){
	$vSummary = substr($course->summary,0,50).'...';
	 
	  }else{
$vSummary = $course->summary;
	  }

						
					echo  '<div  style="float:left;margin-bottom:5px;"><a title="Remainder" href="javascript:void(o);" class="cusLink" onclick="fnGoal('.$course->courseid.')"><img style="width:22px" src="./img/watch3.png"></a></div>';
					
	$courseids = $DB->get_record_sql("select * from {cm_my_lp} where courseid = $course->courseid and userid = $USER->id ");
								
	$completeddate = $DB->get_record_sql("select timecompleted from {course_completions} where course = $course->courseid  and userid = $USER->id ");
	$edate = $completeddate->timecompleted;
	$cc = strtotime($courseids->goal_end_date);
	$todaydate = date("d-m-Y");
	$tdate =  strtotime($todaydate);
 if(!empty($courseids->goal_end_date)){
	if(!empty($edate)){
	if($cc > $edate){
		echo '<span style="font-size: 12px;
		padding: 5px;padding-left:10px"> Due Date :&nbsp; <b style="color:green">' . $datel =  date("d-m-Y", strtotime($courseids->goal_end_date)) ; 
		echo '</b></span>';
	} else if($cc < $edate) {
		
		echo '<span style="font-size: 12px;
		padding: 5px;padding-left:10px"> Due Date :&nbsp; <b style="color:red">' . $datel =  date("d-m-Y", strtotime($courseids->goal_end_date)) ; 
		echo '</b></span>';
	} else if($tdate > $cc){
		
		echo '<span style="font-size: 12px;
		padding: 5px;padding-left:10px"> Due Date : &nbsp; <b style="color:red">' . $datel =  date("d-m-Y", strtotime($courseids->goal_end_date)) ; 
		echo '</b></span>';
	} else {
		echo '<span style="font-size: 12px;
		padding: 5px;padding-left:10px"> Due Date : &nbsp;<b>' . $datel =  date("d-m-Y", strtotime($courseids->goal_end_date)) ; 
		echo '</b></span>';	
	}
	} else {

	 if($tdate > $cc){
		
		echo '<span style="font-size: 12px;
		padding: 5px;padding-left:10px"> Due Date :&nbsp; <b style="color:red">' . $datel =  date("d-m-Y", strtotime($courseids->goal_end_date)) ; 
		echo '</b></span>';
	} else {
		echo '<span style="font-size: 12px;
		padding: 5px;padding-left:10px"> Due Date : &nbsp;<b>' . $datel =  date("d-m-Y", strtotime($courseids->goal_end_date)) ; 
		echo '</b></span>';	
	}
	
	}
 } else {
	 ?>
 
 <form method="post" id="form1<?php echo $course->courseid ; ?>" style="display:none;margin-top: -9px;font-size: 12px;">
 <input type="hidden" name="rid" id="rid" value="<?php echo $courseids->id ; ?>" >
 <input type="hidden" name="cid" id="cid" value="<?php echo $courseids->courseid ; ?>" >
 <input type="date" name="goal" id="goal" style="float:left;margin-left:5px;" required></textarea>
 <input style="float:left;margin-left:5px;" type="submit" name="submit" value="Add"/>
 </form>
 
 <?php }
						
						
  echo '</div>';
echo '</div>';
  echo '</div>'; 
  echo '</div>';
		} 
		
		} else {
			
 ?>
	<div class="item">
<div class="span2 tl-grid-dashboard-course-wrapper" data-coursesset="0" style="width: 85%;box-shadow: none !important;margin-bottom: 300px;">
	<div class="tl-grid-dashboard-course-title " style="border:none"><div class="coursename tl-bold-item  tl-ellipsis" style="margin: 25px;">
	<a target="_blank" style="font-weight: bold;"><span title="" class="tl-formatted-course-name">
	<?php echo 'There are no learning plans assigned to you.
' ?> <span class="tl-formatted-course-code"></span></span></a></div>
	<div class="coursestatus">
	<!--<span class="label label-progress tl-label-progress text-left tl-cursor-pointer tl-grid-mode-filter" data-filter="progress" 
	title="Show courses in progress">-->
	<span class="label label-success" style="width:0px;display:none">0%</span>
	<!--</span>-->
	</div>
	
	</div>
	</div>

	</div>
  
  <?php
		}

		?>
		
  </div>
  <script src="js/jquery.min.js"></script>
 
<link href="css/lity.css" rel="stylesheet"/>
<script src="js/lity.js"></script>
</div>
<style>

.cusTextSmall{
	font-size:12px;
	coloe:#666666;
	
}
/**
 * Tabs
 */
.tabs {
	display: flex;
	flex-wrap: wrap; // make sure it wraps
	border-left:1px solid #cccccc !important;
}
.tabs label {
	order: 1; // Put the labels first
	display: block;
	padding: .5rem 2rem;
	margin-right: 0.2rem;
	cursor: pointer;
background: #fff;
  font-weight: bold;
  transition: background ease 0.2s;
}
.tabs .tab {
  order: 99; // Put the tabs last
  flex-grow: 1;
	width: 100%;
	display: none;
  padding: 1rem;
  background: #fff;

}
.tabs input[type="radio"] {
	display: none;
}
.tabs input[type="radio"]:checked + label {
	  color: #495057;
background-color: #eef5f9;
border-color: #dee2e6 #dee2e6 #eef5f9;
 transition: background ease 0.2s;
border : 1px solid #dee2e6;
border-bottom : none; 
	     
}
.tabs input[type="radio"]:checked + label + .tab {
	display: block;
border-top: 1px solid #dee2e6;
margin-top : -8px;
}


@media (max-width: 45em) {
  .tabs .tab,
  .tabs label {
    order: initial;
  }
  .tabs label {
    width: 100%;
    margin-right: 0;
    margin-top: 0.2rem;
  }
}


/* Float four columns side by side */
.column {
  float: left;
  width: 100%;
  padding: 0 2px;
}

/* Remove extra left and right margins, due to padding in columns */
.row {margin: 5px}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
.container_card{
	margin:10px;
}


		.column .card {
  /* Add shadows to create the "card" effect */
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
}

/* On mouse-over, add a deeper shadow */
.column .card:hover {
  box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
}

/* Add some padding inside the card container */

.column .card {
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  border-radius: 5px; /* 5px rounded corners */
  
}



/* Responsive columns - one column layout (vertical) on small screens */
@media screen and (max-width: 600px) {
  .column {
    width: 100%;
    display: block;
    margin-bottom: 5px;
  }
}

.cuLinkText{
	color:#008acf !important;
}
</style>

<script>  
$(document ).ready(function() {

var tabv = $('#tval').val();
if(tabv == 8){

$('#tabone').attr('checked', 'checked');
} else if(tabv == 9){
$('#tabtwo').attr('checked', 'checked');
} else {
$('#tabone').attr('checked', 'checked');

}

$("#tabtwo").attr("onclick","new_function_name()");
$("#tabone").attr("onclick","new_tab()");
 
var today = new Date().toISOString().split('T')[0];
document.getElementsByName("goal")[0].setAttribute('min', today);

});


function new_function_name() {

var currentUrl = location.href;
var arr = currentUrl.split('?');
var url = arr[0];
/* var newUrl = url + "?tab=9" ;

//redirect to new page
location.href = newUrl; */

 var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=9';
         window.history.pushState({path:newurl},'',newurl);


}

function new_tab() {
//$("#tabone").attr("checked","checked");
var currentUrl = location.href;
var arr = currentUrl.split('?');
var url = arr[0];

var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=8';
         window.history.pushState({path:newurl},'',newurl);

/* var newUrl = url + "?tab=8" ;
location.href = newUrl; */

}
function fnGoal(cid){

$('#form1'+cid).show();
}

	function fnMyLP(courseid,mode){
	 $("#divMyLP_"+courseid).html('Updating...');    
			    $.ajax({
            url: './ajax/ajax_update_my_lp.php',
            type: 'post',
            data: {mode:mode,courseid:courseid},
            success:function(response){                

                $("#divMyLP_"+courseid).html(response);                
				       location.reload();  
            }
          })
}
	

	
                        $(".image-checkbox").each(function () {
                            if ($(this).find('input[type="checkbox"]').first().attr("checked")) {
                                $(this).addClass('image-checkbox-checked');
                            } else {
                                $(this).removeClass('image-checkbox-checked');
                            }
                        });

                        // sync the state to the input
                        $(".image-checkbox").on("click", function (e) {

                            var clickedEle = $(this).find('input[type="checkbox"]');
                            if ($(this).hasClass('image-checkbox-checked')) {
                                alert("-- Un checked --" + clickedEle.val());
                            } else {
                                alert("-- checked --" + clickedEle.val());
                            }


                            $(this).toggleClass('image-checkbox-checked');
                            var $checkbox = $(this).find('input[type="checkbox"]');
                            $checkbox.prop("checked", !$checkbox.prop("checked"));
                            e.preventDefault();
                        });
                    </script>
					
					<style>
					p {
						font-size :13px;
					}						
			</style>

<?php
echo $OUTPUT->footer();
