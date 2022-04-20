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


echo $OUTPUT->header();
?>
	<link href="fmt.css" rel="stylesheet" type="text/css">

<style>
#page-header {
	display : none;
}
.tl-grid-dashboard-course-wrapper {
	margin-right : 15px;
}
#page {
	min-height: 390px;
}
</style>


  		<h3 style="color: #807e7e;font-weight: 400;font-size: 19px;margin-bottom:20px;">Learning plans</h3>


<div class="card-text content mt-3" id="yui_3_17_2_1_1574228776267_99">
            		
    <div id="block-myoverview-5dd4d328210125dd4d3280c7ea2" class="block-myoverview" data-region="myoverview">
       <ul id="block-myoverview-view-choices-5dd4d328210125dd4d3280c7ea2" class="nav nav-tabs" role="tablist">
	   
	   <li class="nav-item" id="yui_3_17_2_1_1574228776267_98">
				<a class="nav-link active show" href="#enrolled_div" role="tab" data-toggle="tab" data-tabname="courses" id="enroldiv" aria-selected="false" style="text-transform: uppercase;font-weight: bold;">
				   
				ASSIGNED LEARNING PLANS
				</a>
			</li>
	   
		   <li class="nav-item" id="yui_3_17_2_1_1574228776267_98">
				<a class="nav-link" href="#inprogress_div" role="tab" data-toggle="tab" data-tabname="timeline" id="inpd" aria-selected="true" style="text-transform: uppercase;font-weight: bold;" >
				  MY LEARNING PLAN 
				</a>
			</li>
			
		</ul>


<div class="tab-content content-centred">

<div role="tabpanel" class="tab-pane fade in active show" id="enrolled_div">

 <section id="region-main">
  

  
    
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
		  $lpimg = "../cm/lpimages/$lp->lpimage";
		 } else {
		 $lpimg = "../cm/1548346739_fundamentals.png";
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
	<a href="../cm/lp_coursedesign.php?lpid=<?php echo $lpids->id; ?>&userid=<?php echo $uid;?>#1"><img class="avatar-square" src="<?php echo $lpimg ; ?>"></a>
	
	</div><div class="tl-grid-dashboard-course-title text-center"><div class="coursename tl-bold-item  tl-ellipsis">
	<a href="../cm/lp_coursedesign.php?lpid=<?php echo $lpids->id; ?>&userid=<?php echo $uid;?>#1">
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
			
		
		
	
  </section>
  
  </div>
 
 <div role="tabpanel" class="tab-pane fade" id="inprogress_div">
     
	 <div style="float:right;margin-right:5px;margin-top: 5px;
    margin-bottom: 15px;">
	<a style='color: #fff !important;margin-bottom:10px' class='btn btn-primary '  href='lp_user_courses.php?lpid=<?php echo $lpname->id; ?>' data-lity>
	Add Courses
	</a>		
    </div>
	 
	<div style="clear:both"></div> 
	   <?php
				
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
 
 if($course->coursetype == 3){
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


 
/* 
  if($vPercentag == 0){
	  $vStatus = '<span style="background-color:red;color:#ffffff;border-radius:5px;padding:5px;">Not Started</span>';
  }else if($vPercentag == 100){
	  $vStatus = '<span style="background-color:green;color:#ffffff;border-radius:5px;padding:5px;">Completed</span>';
  }else{
	  $vStatus = '<span style="background-color:orange;color:#ffffff;border-radius:5px;padding:5px;>In Progress</span>';
  }
  echo '<div style="float:right" class="cusTextSmall">Status : <b>'.$vStatus.'</b></div>'; */
  echo '<div style="clear:both"></div>';
 

  
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
	  
	  
<script src="js/jquery-3.3.1.js"></script>
<link href="css/lity.css" rel="stylesheet"/>
<script src="js/lity.js"></script>  
	<script>
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
				refreshtab();
				       
            }
          })
}


function refreshtab(){
	location.reload();
}
</script>  
<style>
.card {
	background : #fff !important;
}
</style>	  
</div>

 </div>
  </div>
  </div>

<?php
echo $OUTPUT->footer();
