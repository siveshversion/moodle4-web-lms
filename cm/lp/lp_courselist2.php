<?php
/**
 * Competency - Learning Path
 *
 * @package    Learning Path
 * @copyright  2019 Siveshversion
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once '../../config.php';
require_once $CFG->dirroot . '/cm/lib/cmlib.php';
?>



<?php
$PAGE->set_context(context_system::instance());
global $CFG, $DB, $USER;
$site = get_site();

require_login();

$PAGE->set_url('/cm/lp_courselist.php');

$PAGE->set_title('Learning Plan');
$PAGE->set_heading('Learning Plan');
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();
?>

<style>
#page-header {
	display : none;
}
.summary_container {
	max-height : 90px !important;
}




/* Float four columns side by side */
.column {
  float: left;
  width: 100%;
  padding: 0 2px;
  height : 85px;
}

/* Remove extra left and right margins, due to padding in columns */
.card {
	margin-right: 5px;
  background-color : #fff !important;
}

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
.cusTextSmall{
	font-size:12px;
	coloe:#666666;

}
#region-main {
	background-color : #fff;
	min-height: 12rem !important;
}
.navbar .popover-region-header-actions .icon, .navbar .popover-region-toggle .icon {
	color: #008acf !important;
}
</style>

<?php

$lpid = $_GET['lpid'];

if (!empty($_REQUEST['ccid'])) {
    $cid = $_REQUEST['ccid'];
    $delete = $DB->delete_records('cm_lp_course', array('lp_courseid' => $cid, 'lp_id' => $lpid));
    if (isset($delete)) {
        redirect('lp_courselist.php?lpid=' . $lpid, 'Course deleted successfully ', 1);
    }
}

if ($USER->id == 2) {
    $uid = $_GET['userid'];

} else {
    $uid = $USER->id;
}
if (!empty($_REQUEST['id'])) {
    $ucenterid = $_REQUEST['id'];

}

?>

  <link href="css/lp_courselist.css" rel="stylesheet" type="text/css"/>
  <link href="css/progress-wizard.min.css" rel="stylesheet" type="text/css"/>
 <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
<?php $lpdetails = get_lp_details($lpid);

$courseids = get_assigned_courses($lpid);
?>

<div id="page-wrapper">
    <div id="page" class="" style="margin-top:-10px;padding-left:10px !important;">
          <header id="" class="row" style = ''>
            <div class="col-12 ">

                                    <div class="page-context-header" >

									<div class="page-header-headings" style="
    margin-top: 17px;
    margin-bottom: -12px;
"><h3><span  class="fa fa-road" style="margin-right:5px;color:black"></span> <?php echo $lpdetails->lpname; ?></h3></div>
									</div>
									<div><a style='float:right;padding: 5px 20px;' class='btn btn-primary '  href='learningpath_list.php'>Back </a></div>



            </div>
        </header>

        <div id="page-content" class="row pb-3">
            <div id="region-main-box" class="col-12">
                 <section id="region-main" >

                    <span class="notifications" id="user-notifications"></span>
                    <div role="main"><span id="maincontent"></span>
					<ul class="course_extended_menu_list ilp">   </ul>

<div class="w-full pull-left planview_container">
	<div class="w-100 pull-left planview_topcontent" style="position:absolute;padding-bottom: 12px;margin-left: -5px;">
		<div class="col-md-3 d-none-1023 pull-left p-0">
		<?php if (!empty($lpdetails->lpimage)) {?>
			<div  style="background-image:url(<?php echo "../lp/lpimages/$lpdetails->lpimage" ?>);" alt="Pre Plan" title="Pre Plan" class="lpimg_bg">
			<img src="<?php echo "lpimages/$lpdetails->lpimage" ?>" title="Pre Plan" class="lpimg_bg" style="width: 100%;">

</div>
		<?php } else {?>
			<div  style="background-image:url(<?php echo "'../cm/img/image3.jpg'" ?>);" alt="Pre Plan" title="Pre Plan" class="lpimg_bg">
			<img src="<?php echo "1548346739_fundamentals.png" ?>" title="Pre Plan" class="lpimg_bg" style="width: 100%;">

			</div>
		<?php }?>
		</div>
		<div class="col-md-9 d-md-lg-full pull-left">

			<div class="w-100 pull-left pb-10">
				<span class="w-100 pull-left"><b>Description :</b></span>
				<div class="w-100 pull-left description">


						<p class="m-0"><?php echo $lpdetails->lpdesc; ?>
 ...</p>
				</div>
			</div>

				<div class="w-100 pull-left">
				<div class="col-md-5 pull-left p-0">
					<div class="w-100 pull-left mb-1">
						<span class="w-100 pull-left"><b>Details :</b></span>
					</div>
					<div class="w-100 pull-left">
						<span class="text-muted pull-left" style="min-width: 100px;">Plan Type
						<span class="mr-2 pull-right">:</span>
                                                </span><span><strong><?php echo " Assigned"; //if($lpdetails->lp_type ==1){ echo " Assigned"; }else{ echo "Self";}  ?></strong></span>
					</div>
					<div class="w-100 pull-left">
						<span class="text-muted pull-left" style="min-width: 100px;">No of days to complete
						<span class="mr-2 pull-right">:</span>
						</span><span title="<?php echo $lpdetails->lpdays; ?>"><strong><?php echo $lpdetails->lpdays; ?></strong></span>
					</div>

									<div class="w-full pull-left">
						<span class="text-muted pull-left" style="min-width: 100px;">Due Date
						<span class="mr-2 pull-right">:</span>
						</span><span><strong><?php

$courseids = $DB->get_record_sql("select * from {cm_lp_assignment} where lp_id = $lpid and userid = $USER->id");

$completeddate = $DB->get_record_sql("select timecreated from {cm_lp_completion_stauts} where lp_id = $lpid  and userid = $USER->id ");
$edate = $completeddate->timecreated;
$cc = strtotime($courseids->goal_end_date);
$todaydate = date("d-m-Y");
$tdate = strtotime($todaydate);
if (!empty($courseids->goal_end_date)) {
    if (!empty($edate)) {
        if ($cc > $edate) {

            echo '<span style="
		padding: 5px;padding-left:10px">  <b style="color:green">' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
            echo '</b></span>';
        } else if ($cc < $edate) {

            echo '<span style="
		padding: 5px;padding-left:10px">  <b style="color:red">' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
            echo '</b></span>';
        } else if ($tdate > $cc) {

            echo '<span style="
		padding: 5px;padding-left:10px">  <b style="color:red">' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
            echo '</b></span>';
        } else {
            echo '<span style="
		padding: 5px;padding-left:10px">  <b>' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
            echo '</b></span>';
        }
    } else {

        if ($tdate > $cc) {

            echo '<span style="
		padding: 5px;padding-left:10px">  <b style="color:red">' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
            echo '</b></span>';
        } else {
            echo '<span style="
		padding: 5px;padding-left:10px">  <b>' . $datel = date("d-m-Y", strtotime($courseids->goal_end_date));
            echo '</b></span>';
        }

    }

}
?>

				   </strong></span>
					</div>

					<div class="w-100 pull-left">
						<span class="text-muted pull-left" style="min-width: 100px;">Credits
						<span class="mr-2 pull-right">:</span>
						</span><span><strong><?php echo $lpdetails->points; ?></strong></span>
					</div>

				</div>
				<div class="col-md-4 pull-left pl-0">
					<div class="w-100 pull-left mb-1">
						<span class="w-100 pull-left"><b>Courses :</b></span>
					</div>
					<div class="w-100 pull-left">
						<span class="text-muted pull-left" style="min-width: 100px;">Assigned
						<span class="mr-2 pull-right">:</span>
						</span><span><strong>          <?php
//$ccount = $DB->get_record_sql("select count(*) as cnt from {cm_lp_course} where lp_id=$lpid and lp_courseid > 0 ");
$ccount = $DB->get_record_sql("select count(*) as cnt from {cm_lp_course} a,{$CFG->prefix}course b  where lp_id=$lpid and a.lp_courseid = b.id and lp_courseid > 0 and b.visible =1");

if (($ccount->cnt) != 0) {
    echo $ccount->cnt;
} else {
    echo '0';
}?></strong></span>
					</div>



					<?php if (is_siteadmin()) {?>

					 <div class="w-full pull-left">
						<span class="text-muted pull-left" style="min-width: 100px;">Completed Users
						<span class="mr-2 pull-right">:</span>
						</span><span><strong><?php

    $lpc = $DB->get_record_sql("select count(id) as cmp from {cm_lp_completion_stauts} where lp_id = $lpid ");?>

				<?php echo $lpc->cmp; ?>


				   </strong></span>
					</div>

					<?php }?>
				<!--	<div class="w-full pull-left">
						<span class="text-muted pull-left" style="min-width: 100px;">Mandatory
						<span class="mr-2 pull-right">:</span>
						</span><span><strong><?php $ccount = $DB->get_record_sql("select count(*) as cnt from {cm_lp_course} where lp_id=$lpid and ctype =1 and lp_courseid > 0");

if (($ccount->cnt) != 0) {
    echo $ccount->cnt;
} else {
    echo '0';
}?></strong></span>
					</div>
					<div class="w-full pull-left">
						<span class="text-muted pull-left" style="min-width: 100px;">Optional
						<span class="mr-2 pull-right">:</span>
						</span><span><strong><?php $ccount = $DB->get_record_sql("select count(*) as cnt from {cm_lp_course} where lp_id=$lpid and ctype = 0 and lp_courseid > 0");

if (($ccount->cnt) != 0) {
    echo $ccount->cnt;
} else {
    echo '0';
}?></strong></span>
					</div> -->


				</div>


     <?php

/*

if(!empty($_GET['userid'])){ ?>
<div class="w-100 pull-left">

<ul class="progress-indicator">
<?php
$background_colors = array('secondary','warning', 'danger', 'info', 'completed','warning', 'danger', 'info', 'completed','warning', 'danger', 'info', 'completed');
$x =1;$y=0;$z=1;
//$rand_background = $background_colors[array_rand($background_colors)];
if(!empty($_GET['userid'])){
$completed_course = get_user_coursecompletion_cnt($USER->id,$lpid);

// $completed_course = 1;
//echo sizeof($completed_course) ."!!!!!!!!!";
if (sizeof($completed_course) > 0) {
$y =  sizeof($completed_course);
} else {
$y = 0;
}
}

foreach ($courseids as $ck => $cv) {

 */
?>



			</div>
		</div>
	</div>
</div>

</div>

</section>


   <section  class=" block  card mb-3" role="complementary"  style="border-color: #fff;" data-block="myoverview" aria-label="">
<?php

?>

   <?php

if (is_siteadmin()) {?>
   <?php $lpc = $DB->get_record_sql("select count(id) as comp from {cm_lp_completion_stauts} where lp_id = $lpid ");

    if ($lpc->comp != 0) {?>
  <div style="padding: 10px;padding-right:0px;text-align: right;">

  <a style='color: #fff !important;' class='btn btn-primary '  >
Assign Courses
   </a>

   </div>
 <?php
} else {
        ?>
   <div style="padding: 10px;padding-right:0px;text-align:right"> <a style='color: #fff !important;padding: 5px 22px;' class='btn btn-primary '  href='lp_courses.php?lpid=<?php echo $lpid; ?>' data-lity>
Assign Courses
   </a></div>
 <?php }?>
<style>
   #order-button {
	   display : block;

   }

   </style>
   <?php }?>
                                          <?Php
$query = "select b.id as courseid,b.fullname,b.summary as coursename,b.visible,b.points,b.hours,b.mints,b.course_type,b.courselink,a.sorder from {$CFG->prefix}cm_lp_course a,{$CFG->prefix}course b where a.lp_id = $lpid and a.lp_courseid = b.id and b.visible =1 order by a.sorder+0 asc";
$objMyLPCourses = $DB->get_records_sql($query);

$vv = array();
$otest = $DB->get_records_sql("select b.id as cidd from {$CFG->prefix}cm_lp_course a,{$CFG->prefix}course b where a.lp_id = $lpid and a.lp_courseid = b.id and b.visible = 1 order by a.sorder+0 asc");
foreach ($otest as $te) {
    $vv[] = $te->cidd;

}

$csata = implode(',', $vv);?>
			   <input type="hidden" name="ciddd" id ="ciddd" value="<?php echo $csata; ?>">
			   <input type="hidden" name="lpid" id ="lpid" value="<?php echo $lpid; ?>">

			   <?php

if (count($objMyLPCourses) != 0) {

    $m = 1;
    foreach ($objMyLPCourses as $course) {

        if ($course->course_type == 3) {
            $courselink = $course->courselink;
            $tag = "target=_blank";
        } else {
            $courselink = new moodle_url('/course/view.php', array('id' => $course->courseid, 'visible' => 1));
            $tag = "";
        }?>
						   <input type="hidden" name="tag<?php echo $course->courseid; ?>" id ="tag<?php echo $course->courseid; ?>" value="<?php echo $tag; ?>">

						   <input type="hidden" name="clink<?php echo $course->courseid; ?>" id ="clink<?php echo $course->courseid; ?>" value="<?php echo $courselink; ?>">

						   <input type="hidden" name="cname<?php echo $course->courseid; ?>" id ="cname<?php echo $course->courseid; ?>" value="<?php echo $course->fullname; ?>">
<?php
if (!is_siteadmin() && empty($userconstadmin->id)) {

            if ($course->course_type == 3) {
                $courselink = $course->courselink;
                $tag = "target=_blank";
            } else {
                $courselink = new moodle_url('/course/view.php', array('id' => $course->courseid, 'visible' => 1));
                $tag = "";
            }
            echo '<div class="row">';

            echo '<div class="column">';

            echo ' <div class="card">';

            echo '<div class="container_card">';
        }

        $vCourseName = $course->fullname;

        if (!is_siteadmin()) {

            global $USER;

            if ($m == 1) {
                echo '<div style="float:left"><h4><b> <a class="cuLinkText" ' . $tag . ' href="' . $courselink . '">' . $vCourseName . '</a></b></h4></div>';
            } else {
                $mm = $m - 1;
                $chklpcourseid = $DB->get_record_sql("select lp_courseid from {cm_lp_course} where lp_id=$lpid and sorder = $mm and lp_courseid > 0");
                if (!empty($chklpcourseid->lp_courseid)) {
                    $completed_course = get_user_courseprogress($USER->id, $chklpcourseid->lp_courseid);
                    $progress = 0;
                    if ($course->course_type == 3) {
                        $certsts = $DB->get_record_sql("select adminid,timesubmit from {cm_coursesubmit_user}  where userid = $USER->id and courseid = $chklpcourseid->lp_courseid ");
                        if (!empty($certsts->adminid)) {
                            $progress = 100;

                        }
                    } else {

                        if ($completed_course == 2) {
                            $progress = "100";
                        } else if ($completed_course == 1) {
                            $progress = "50";
                        } else {
                            $progress = "0";
                        }

                    }
                }
                if ($progress == 100) {

                    echo '<div style="float:left"><h4><b> <a class="cuLinkText" ' . $tag . ' href="' . $courselink . '">' . $vCourseName . '</a></b></h4></div>';
                } else {
                    echo '<div style="float:left"><h4><b> <a class="cuLinkText" style="opacity: 0.4;cursor: default;">' . $vCourseName . '</a></b></h4></div>';

                }
            }

        }

        $completed_course = get_user_courseprogress($USER->id, $course->courseid);
        $progress = 0;
        if ($course->course_type == 3) {
            $certsts = $DB->get_record_sql("select adminid,timesubmit from {cm_coursesubmit_user}  where userid = $USER->id and courseid = $course->courseid ");
            if (!empty($certsts->adminid)) {
                $compteprogress = 100;
                $prg = '<span style="background-color: green;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;margin-right: 12px;">Completed</span> ';
            } else {
                $compteprogress = 0;
                $prg = '<span style="background-color: red;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;margin-right: 12px;">Not Started</span> ';
            }

        } else {

            if ($completed_course == 2) {
                $progress = "100";
            } else if ($completed_course == 1) {
                $progress = "50";
            } else {
                $progress = "0";
            }

            if ($progress == 100) {
                $prg = '<span style="background-color: green;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;">Completed</span> ';
            } else if ($progress == 0) {
                $prg = '<span style="background-color: red;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;">Not Started</span> ';
            } else {
                $prg = '<span style="background-color: blue;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;">In Progress</span> ';

            }
        }
        if (!is_siteadmin()) {
            echo $prg;
        }
        ?>
		 <input type="hidden" name="prg<?php echo $course->courseid; ?>" id ="prg<?php echo $course->courseid; ?>" value="<?php echo $progress; ?>">
<?php
if (!is_siteadmin()) {
            echo '<div style="clear:both"></div>';
        }
        if ($course->points == '') {
            $vPoints = 'N/A';
        } else {
            $vPoints = $course->points;
        }

        if (!is_siteadmin()) {

            echo '<div style="float:right" class="cusTextSmall">Duration : <b>' . $vDuration . '</b> Points : <b>' . $vPoints . '</b></div>';

            /* restrict text */

            global $USER;

            if ($m == 1) {
            } else {
                $mm = $m - 1;
                $chklpcourseid = $DB->get_record_sql("select lp_courseid from {cm_lp_course} where lp_id=$lpid and sorder = $mm and lp_courseid > 0");
                if (!empty($chklpcourseid->lp_courseid)) {
                    $completed_course = get_user_courseprogress($USER->id, $chklpcourseid->lp_courseid);
                    $progress = 0;
                    if ($course->course_type == 3) {
                        $certsts = $DB->get_record_sql("select adminid,timesubmit from {cm_coursesubmit_user}  where userid = $USER->id and courseid = $chklpcourseid->lp_courseid ");
                        if (!empty($certsts->adminid)) {
                            $progress = 100;

                        }
                    } else {

                        if ($completed_course == 2) {
                            $progress = "100";
                        } else if ($completed_course == 1) {
                            $progress = "50";
                        } else {
                            $progress = "0";
                        }

                    }
                }
                if ($progress == 100) {

                } else {
                    $restord = $course->sorder - 1;
                    $restcname = $DB->get_record_sql("select fullname from {cm_lp_course} rlc join {course} rc on rc.id = rlc.lp_courseid where lp_id=$lpid and sorder = $restord ");

                    echo '<div style="float:left" class="cusTextSmall"><span class="badge badge-info" style="color: #212529;background-color: #5bc0de;margin-right: 5px;font-size: 12px;">Restricted</span>Not available unless: The course <b>' . $restcname->fullname . '</b> is complete</div>';

                }

            }
            /* end restrict */

        }

        ?>
  		 <input type="hidden" name="dur<?php echo $course->courseid; ?>" id ="dur<?php echo $course->courseid; ?>" value="<?php echo $vDuration; ?>">
  		 <input type="hidden" name="pont<?php echo $course->courseid; ?>" id ="pont<?php echo $course->courseid; ?>" value="<?php echo $vPoints; ?>">
		 <?php

        if (strlen($course->summary) > 50) {
            $vSummary = substr($course->summary, 0, 50) . '...';

        } else {
            $vSummary = $course->summary;
        }

        /*if(!in_array($course->courseid,$arrMyLPCourses)){
        echo  '<div id="divMyLP_'.$course->courseid.'" style="float:left;margin-bottom:5px"><a title="Add this course to My Learning Plan" href="javascript:void(o);" class="cusLink" onclick="fnMyLP('.$course->courseid.',1)"><img width="50%" src="./img/learning_plan.png"></a></div>';
        }else{

        echo  '<div id="divMyLP_'.$course->courseid.'" style="float:left;margin-bottom:5px;"><a title="Remove this course from My Learning Plan" href="javascript:void(o);" class="cusLink" onclick="fnMyLP('.$course->courseid.',0)"><img width="50%" src="./img/learning_plan_tick.png"></a></div>';

        }*/

        if (!is_siteadmin()) {
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        if ($m == 1) {
            $m++;
        } else {
            $mm = $m - 1;
            $chklpcourseid = $DB->get_record_sql("select lp_courseid from {cm_lp_course} where lp_id=$lpid and sorder = $mm and lp_courseid > 0");
            if (!empty($chklpcourseid->lp_courseid)) {
                $completed_course = get_user_courseprogress($USER->id, $chklpcourseid->lp_courseid);
                $progress = 0;
                if ($course->course_type == 3) {
                    $certsts = $DB->get_record_sql("select adminid,timesubmit from {cm_coursesubmit_user}  where userid = $USER->id and courseid = $chklpcourseid->lp_courseid ");
                    if (!empty($certsts->adminid)) {
                        $progress = 100;
                    }
                } else {

                    if ($completed_course == 2) {
                        $progress = "100";
                    } else if ($completed_course == 1) {
                        $progress = "50";
                    } else {
                        $progress = "0";
                    }
                }
            }
            if ($progress == 100) {
                $m++;
            }
        }
    }

} else {

    if (!is_siteadmin()) {

        echo "<p style='text-align: center;font-size: 16px;'>No courses assigned this Learning Plan</p>";
    }
}
?>


<script>




    function test(val){
       var checked = $('.onoffswitch-inner:before').val();// $("input[type='checkbox']").val();

       var myarr = val.split("-");
        var myvar = myarr[0];
        var ctype = myarr[1];
       var flag = 0;
       if($(".onoffswitch-label").hasClass('clicked')){ // click blue button
                if(ctype == 1){
                    flag = 0;
                }else{
                    flag = 1;
                }
           $(".onoffswitch-label").removeClass('clicked');
       }else{ // click orange button
           flag = 0;
           $(".onoffswitch-label").addClass('clicked');
       }



        var lp_id = document.getElementById("lpid").value;

                var dataString = 'data='+ lp_id +"-"+myvar+"-"+flag;
               $.ajax
                     ({
                             type: "POST",
                             url: "../cm/ctype_change.php",
                             data: dataString,
                             cache: false,
                             success: function(html)
                             {
                             location.reload();
                             }
                     });

    }



</script>

<?php if (is_siteadmin()) {?>
 <link rel='stylesheet' href='css/jquery-ui.css'>

   <?php if (count($objMyLPCourses) != 0) {?>
<!-- partial:index.partial.html -->
<div class="container1"></div>

<button id="order-button" style="
    width: 20%;
    margin-top: 13px;
" class="btn btn-primary">Set the Order</button>

<div class="order-display"></div>
   <?php }?>
<!-- partial -->

<?php if (count($objMyLPCourses) != 0) {?>

<script>
$(document).ready(function(){
	$('#order-button').show();
 var x,y,
     container = $('.container1'),
     button = $('#order-button'),
     orderDisplay = $('.order-display'),
     groovyBox = $('#groovy-box');


 container.delegate('.box','mouseenter mouseout',handleMouse);
 container.sortable();

 button.button();

 button.click(function(e){
   var ret='',
       ar = container.sortable('toArray')
   for (key in ar) {
     ret += ar[key] + ',';
   }
   $('#orderid').val(ret);
//alert(ret);
   orderDisplay.html();

stroefn(ret);

 });

// groovyBox.css('backgroundColor','#ffd');

 function handleMouse(e) {
   if (e.type == "mouseenter") {
     $(this).addClass('');
   }
   else if (e.type == "mouseout") {
     $(this).removeClass('');
   }
 }




	var cc = $('#ciddd').val();
var a = cc.split(",");
for (i = 0; i < a.length; i++) {
	 var box = $('<div class="box"></div>');

	var tag = $('#tag'+a[i]).val();
	var clink = $('#clink'+a[i]).val();
	var cname = $('#cname'+a[i]).val();
	var prg = $('#prg'+a[i]).val();

	var pont = $('#pont'+a[i]).val();
	/* if(prg == 100) {
	var prg =  '<span style="background-color: green;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;">Completed</span> ';
} else if(prg == 0) {
var prg  =  '<span style="background-color: red;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;">Not Started</span> ';
} else {
var prg  =  '<span style="background-color: blue;color:#fff;padding: 1px 10px;border-radius: 3px;font-size: 13px;float: right;">In Progress</span> ';
} */

     box.html(i+1+'<div class="row" style="margin-top: -22px;margin-left: 31px;cursor: default;"><div class="column"><div class="card"><div class="container_card"><div style="float:left;"><h4><b><a style="font-size: 20px;" class="cuLinkText" '+tag+' href="'+clink+'">'+cname+'</a></b></h4></div><div style="clear:both"></div><div style="float:right;color:#373a3c;" class="cusTextSmall"> Points : <b>'+pont+'</b></div></div></div></div></div>');
     box.attr('id',a[i]);
	 box.sortable();
     container.append(box);
}



});

function stroefn(strdta){

	        var lp_id = document.getElementById("lpid").value;

	 var dataString = 'data='+ strdta +'-'+lp_id ;

               $.ajax
                     ({
                             type: "POST",
                             url: "course_order.php",
                             data: dataString,
                             cache: false,
                             success: function(html)
                             {

                             location.reload();
                             }
                     });
}

</script>
<?php
} else {
    echo "<p style='text-align: center;font-size: 16px;'>No courses assigned this Learning Plan</p>	";

}
}

?>
</section>

   </div>
</div>
</div>
</div>


<style>
#over img {
	padding-right : 0px !important;
}
.box  {
	background-image: url(../../cm/css/i.png);
    background-repeat: no-repeat;
	background-position: left;
	color:#FFF;
	cursor: move;
}

#order-button {
 font-size: 0.8em;
}
.ui-button-text-only .ui-button-text {

	padding: 2px 8px !important;
}

span.ui-button-text {
	font-size :14px !important;
}
.ui-button-text{
	padding: 2px 5px !important;
    color: #FFF !important;

}

.order-display {
 font-family: monospace;
 font-size: 2em;
 padding: 5px;
}

#groovy-box {
 width: 200px;
 height: 200px;
 border: 1px solid lightgray;
}

</style>

<link href="css/lity.css" rel="stylesheet"/>
<script src="js/lity.js"></script>

 <link rel='stylesheet' href='css/jquery-ui.css'>

<script src='https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js'></script>
<?php
echo $OUTPUT->footer();
