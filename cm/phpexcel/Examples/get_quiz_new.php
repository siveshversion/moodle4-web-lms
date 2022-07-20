<?php 
include "db.php";
error_reporting(E_ALL);
date_default_timezone_set('Europe/Berlin');
          include "db.php";
function get_user_data(){
   $a['uid'] = $_POST['uid'];
  $a['username'] = $_POST['username'];
  $a['course_txt'] = $_POST['course_txt'];
  $course_id = $_POST['course'];
  $a['activity'] = get_Activity($course_id);
  $i=1;
  foreach ($a['activity'] as $key => $value) {
  $a['activity_data'][$i] =  get_ActivityData($a['uid'],$value,$course_id);
  $i++;
  }
  return json_encode($a);
}
if (isset($_POST['get_user_data'])) {   
  echo get_user_data();
  die;
}

function get_Activity($course_id){
  
  { include "db.php";
  $sql = "SELECT mcm.instance, mm.name FROM mdl_course_modules mcm JOIN mdl_modules mm on mcm.module = mm.id WHERE mcm.course = $course_id order by mm.name asc";
  $a = array();
             $course_result = $conn->query($sql); 
             while($row = $course_result->fetch_assoc())
{
  array_push($a,  $row);
  
}
return $a;
}
}
function get_ActivityData($userid, $activity,$course){ 
    include "db.php";
  $tbl = $activity['name'];
  $tbls['tbl'] = $activity['name'];
  $id = $activity['instance'];

   
  $sql = "SELECT  name FROM  mdl_".$tbl." where course = '$course' and id = '$id'";
  $a = array();
             $course_result = $conn->query($sql); 
             while($row = $course_result->fetch_assoc())
{

 if($tbl=='scorm'){
      $stats = get_IndividualscormData($userid, $course,$id);
      $status['status'] = $stats;
  }elseif($tbl=='quiz'){
      $stats = get_IndividualquizData($userid, $course,$id); 
      $status['marks'] = $stats;
  }elseif($tbl=='assign'){
      $stats = get_IndividualassignData($userid, $course,$id); 
      $status['marks'] = $stats;
  }elseif($tbl=='feedback'){
      $stats = get_IndividualfeedbackData($userid, $course,$id); 
      $status['marks'] = $stats;
  }elseif($tbl=='workshop'){
      $stats = get_IndividualfeedbackData($userid, $course,$id); 
      $status['marks'] = $stats;
  }

  array_push($a,  $row);
  array_push($a,  $tbls);
  array_push($a,  $status);
  
}
return $a;
 
}

function get_IndividualscormData($userid, $course,$instance){
  include "db.php";
  /*$sql = "SELECT mdl_course_modules_completion.userid, mdl_course.fullname, mdl_scorm.name, mdl_course_modules_completion.completionstate FROM `mdl_scorm` JOIN mdl_course on mdl_scorm.course = mdl_course.id JOIN mdl_course_modules on mdl_scorm.id = mdl_course_modules.instance JOIN mdl_course_modules_completion on mdl_course_modules_completion.coursemoduleid = mdl_course_modules.id WHERE mdl_scorm.course = $course and mdl_course_modules_completion.userid = $userid and mdl_course_modules.instance = $instance";*/
  $sql = "SELECT mdl_course_modules_completion.completionstate FROM `mdl_scorm` JOIN mdl_course on mdl_scorm.course = mdl_course.id JOIN mdl_course_modules on mdl_scorm.id = mdl_course_modules.instance JOIN mdl_course_modules_completion on mdl_course_modules_completion.coursemoduleid = mdl_course_modules.id WHERE mdl_scorm.course = $course and mdl_course_modules_completion.userid = $userid and mdl_course_modules.instance = $instance";
  $a = array();
             $course_result = $conn->query($sql); 
             while($row = $course_result->fetch_assoc())
{
  array_push($a,  $row);
  
}
return $a;
}

function get_IndividualquizData($userid,$course,$instance){
  include "db.php";
  /*$sql = "SELECT mdl_course_modules_completion.userid, mdl_course.fullname, mdl_scorm.name, mdl_course_modules_completion.completionstate FROM `mdl_scorm` JOIN mdl_course on mdl_scorm.course = mdl_course.id JOIN mdl_course_modules on mdl_scorm.id = mdl_course_modules.instance JOIN mdl_course_modules_completion on mdl_course_modules_completion.coursemoduleid = mdl_course_modules.id WHERE mdl_scorm.course = $course and mdl_course_modules_completion.userid = $userid and mdl_course_modules.instance = $instance";*/
  $sql = "SELECT mq.id as quiz_id, u.id as uid, c.id as coursesID, mq.name, floor(mqa.sumgrades) as grade,mqa.id as attemptID, floor(mq.sumgrades) as totalgrade FROM mdl_course_modules_completion cmc JOIN mdl_user u ON cmc.userid = u.id JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id JOIN mdl_course c ON cm.course = c.id JOIN mdl_modules m ON cm.module = m.id JOIN mdl_quiz mq on c.id = mq.course JOIN mdl_quiz_attempts mqa on mq.id = mqa.quiz WHERE u.id = '$userid' and mqa.userid = '$userid' and  c.id = '$course' and mq.id = '$instance' GROUP by mq.id  order by grade desc";
  $a = array();
             $course_result = $conn->query($sql); 
             while($row = $course_result->fetch_assoc())
{
  $score['score'] = (($row['grade']/$row['totalgrade'])*100);
  $score['quiz_id'] = $row['quiz_id'];
  $score['uid'] = $row['uid'];
  $score['coursesID'] = $row['coursesID'];
  array_push($a,  $score);
  
}
return $a;
}
function get_IndividualassignData($userid,$course,$instance){
  include "db.php";   
  $sql = "SELECT ma.id, ma.name, floor(mag.grade)  as grade FROM mdl_assign_grades mag  JOIN mdl_assign ma on mag.assignment = ma.id   JOIN mdl_course c ON ma.course = c.id JOIN mdl_user u ON mag.userid = u.id       WHERE u.id = '$userid' and mag.userid = '$userid' and  c.id = '$course' and ma.id = '$instance' GROUP by ma.id  order by grade desc";
  $a = array();
             $course_result = $conn->query($sql); 
             while($row = $course_result->fetch_assoc())
{
  array_push($a,  $row);
  
}
return $a;
}
function get_IndividualfeedbackData($userid,$course,$instance){
  include "db.php";   
  $sql = "SELECT mq.id, mq.name, floor(mqa.sumgrades)  as grade,mqa.id as attemptID, mq.sumgrades FROM mdl_course_modules_completion cmc JOIN mdl_user u ON cmc.userid = u.id JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id JOIN mdl_course c ON cm.course = c.id JOIN mdl_modules m ON cm.module = m.id JOIN mdl_quiz mq on c.id = mq.course JOIN mdl_quiz_attempts mqa on mq.id = mqa.quiz WHERE u.id = '$userid' and mqa.userid = '$userid' and  c.id = '$course' and mq.id = '$instance' GROUP by mq.id  order by grade desc";
  $a = array();
             $course_result = $conn->query($sql); 
             while($row = $course_result->fetch_assoc())
{
  array_push($a,  $row);
  
}
return $a;
}
function get_attempt($course,$quiz_id, $user_id){ include "db.php";
  $sql = "SELECT DISTINCT mqa.id,
            u.username AS 'User',
            c.shortname AS 'Course',
            mq.name AS Activitytype  
            FROM mdl_course_modules_completion cmc 
            JOIN mdl_user u ON cmc.userid = u.id
            JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id
            JOIN mdl_course c ON cm.course = c.id
            JOIN mdl_modules m ON cm.module = m.id
            JOIN mdl_quiz mq on  c.id = mq.course
            JOIN mdl_quiz_attempts mqa on mq.id = mqa.quiz 
            WHERE u.id  = '$user_id'  and mqa.userid = '$user_id' and mqa.quiz = '$quiz_id'
             and c.id = '$course'";
             $course_result = $conn->query($sql); 
             return $course_result->num_rows;
}
function get_Internalscore($user_id, $course){ include "db.php";
  $sql = "SELECT mqa.sumgrades  as grade,mqa.id as attemptID, mq.sumgrades FROM mdl_course_modules_completion cmc JOIN mdl_user u ON cmc.userid = u.id JOIN mdl_course_modules cm ON cmc.coursemoduleid = cm.id JOIN mdl_course c ON cm.course = c.id JOIN mdl_modules m ON cm.module = m.id JOIN mdl_quiz mq on c.id = mq.course JOIN mdl_quiz_attempts mqa on mq.id = mqa.quiz WHERE u.id = '$user_id' and mqa.userid = '$user_id' and  c.id = '$course' group by attemptID order by grade desc";
  $a = array();
             $course_result = $conn->query($sql); 
             while($row = $course_result->fetch_assoc())
{
  array_push($a,  $row);
  
}
return $a;
}
function get_Externalscore($user_id, $course){ include "db.php";
  $sql = "SELECT mag.grade as user_grade, ma.grade as total_grade FROM mdl_course C JOIN mdl_assign ma ON ma.course = c.id JOIN mdl_assign_grades mag on mag.assignment = ma.id WHERE ma.course = $course and mag.userid = $user_id";
  $a = array();
             $course_result = $conn->query($sql); 
             while($row = $course_result->fetch_assoc())
{
  array_push($a,  $row);
  
}
return $a;
}

function get_timetaken($quiz_id, $user_id){ include "db.php";
  $sql = "SELECT mqa.timestart,mqa.timefinish, mqa.sumgrades as grade  FROM mdl_quiz_attempts mqa 
            JOIN mdl_quiz mq on mq.id = mqa.quiz
            JOIN mdl_user u on u.id = mqa.userid
            WHERE quiz = '$quiz_id' and userid in ('$user_id') order by grade desc limit 1";
             $course_result = $conn->query($sql); 
             while($row = $course_result->fetch_assoc())
{   
  $timetaken = $row['timefinish']-$row['timestart']; 

    $sec =  ($timetaken / 60);
 //$a =  round($row['grade']);   
}
 return gmdate("H:i:s", $timetaken);
}
function get_totalSCORM($course){
   include "db.php";
  $course = "SELECT COUNT(c.id) as tot FROM mdl_course c JOIN mdl_course_modules mcm on mcm.course = c.id WHERE c.id = $course and mcm.module = 18"; 
    $course_result = $conn->query($course); 
    $tmp = array();
      while($row = $course_result->fetch_assoc())
    {
      array_push($tmp, $row['tot']);
    }
     return $tmp;
 }
 function get_totalQuiz($course){
   include "db.php";
  $course = "SELECT COUNT(c.id) as tot FROM mdl_course c JOIN mdl_course_modules mcm on mcm.course = c.id WHERE c.id = $course and mcm.module = 16"; 
    $course_result = $conn->query($course); 
    $tmp = array();
      while($row = $course_result->fetch_assoc())
    {
      array_push($tmp, $row['tot']);
    }
     return $tmp;
 }
 function get_totalAssign($course){
   include "db.php";
  $course = "SELECT COUNT(c.id) as tot FROM mdl_course c JOIN mdl_course_modules mcm on mcm.course = c.id WHERE c.id = $course and mcm.module = 1"; 
    $course_result = $conn->query($course); 
    $tmp = array();
      while($row = $course_result->fetch_assoc())
    {
      array_push($tmp, $row['tot']);
    }
     return $tmp;
 }
 
function get_scromStatus($id, $course){ include "db.php";
   $course = "SELECT DISTINCT mcmc.id, mcmc.userid,mcmc.coursemoduleid,  mcm.instance, mcmc.completionstate
              FROM mdl_user u
              JOIN mdl_user_enrolments ue ON ue.userid = u.id
              JOIN mdl_enrol e ON e.id = ue.enrolid
              JOIN mdl_role_assignments ra ON ra.userid = u.id
              JOIN mdl_context ct ON ct.id = ra.contextid
              AND ct.contextlevel =50
              JOIN mdl_course c ON c.id = ct.instanceid
              AND e.courseid = c.id
              JOIN mdl_role r ON r.id = ra.roleid
              AND r.shortname =  'student'
                      JOIN mdl_course_modules mcm on mcm.course = c.id
                      JOIN mdl_course_modules_completion mcmc on mcmc.coursemoduleid = mcm.id 
              WHERE e.status =0
              AND u.suspended =0
              AND u.deleted =0
              AND (
              ue.timeend =0
              OR ue.timeend > NOW( ) 
              )
              AND ue.status =0
              AND c.id ='$course' and mcm.course = $course and mcm.module = 18 and mcmc.userid = $id";

    $course_result = $conn->query($course); 
    $tmp = array();
      while($row = $course_result->fetch_assoc())
    {
      array_push($tmp, $row['completionstate']);
    }
     return $tmp;
 }
 function get_quiz($id){ include "db.php";
 $course = "select id,name from mdl_quiz where course = '$id'";

$course_result = $conn->query($course); 
$tmp = array();
  while($row = $course_result->fetch_assoc())
{
  array_push($tmp, $row);
}
 return json_encode($tmp);
 }

if (isset($_POST['get_course'])) {   
        $tot_Assign = get_totalAssign($_POST['get_course']);
        if($tot_Assign[0]>0){
          echo "Yes";die;
        }else{
          echo "No";die;
        }
    }

  if (isset($_POST['labCategId'])) {  
        print_r(get_quiz($_POST['labCategId']));
    }

if(isset($_POST['get_Alluser'])){  
$course_id = $_POST['get_Alluser'];
  $students = "SELECT DISTINCT u.id AS userid, u.firstname, u.lastname, c.id FROM mdl_user u JOIN mdl_user_enrolments ue ON ue.userid = u.id JOIN mdl_enrol e ON e.id = ue.enrolid JOIN mdl_role_assignments ra ON ra.userid = u.id JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel =50 JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id JOIN mdl_role r ON r.id = ra.roleid AND r.shortname = 'student' WHERE e.status =0 AND u.suspended =0 AND u.deleted =0 /*AND ( ue.timeend =0 OR ue.timeend > UNIX_TIMESTAMP(NOW()) )*/ AND ue.status =0 AND courseid ='$course_id'";
$students_result = $conn->query($students);     
//echo 'Num rows '.$students_result->num_rows;    
if ($students_result->num_rows > 0) { 
  $usr_data = array();
  $stu_csv = array(); $i= 1; 
  while($row = $students_result->fetch_assoc())
  { $tot_Assign = get_totalAssign($row['id']); 
    if($tot_Assign[0]>0){

      $style = "display:block;";
    }else{
      $style = "display:none;";
    }
    array_push($usr_data, $row);
  ?>
    <tr id="<?php echo $row['userid']; ?>" >
                        <td><?php echo $i; ?> </td>
                        <td><?php echo '<a class="get_user_data" data-course='.$row['id'].' data-uid = '.$row['userid'].'    >'.$row['firstname']; ?></td>
                        <td><?php echo $row['lastname'];; ?></td>
                        <td><?php 
                          $scorm_status = get_scromStatus($row['userid'],$row['id']);
                          $tot_scrom = get_totalSCORM($row['id']);
                           /*print_r(count($scorm_status));
                            print_r($tot_scrom[0]);*/
                            if($tot_scrom[0]>0){
                           if($tot_scrom[0] == count($scorm_status)){
                           if(in_array(0, $scorm_status)){
                              echo "In Progress";
                            }else{
                              echo "Yes";
                            }
                          }else{
                            echo "Not Completed";
                          }
                        }else{
                          echo "SCORM Not Created";
                        }
                        //echo $row['state']; ?></td>
                        <td><?php $ins = get_Internalscore($row['userid'],$row['id']);
                        $tot_quiz = get_totalQuiz($row['id']);
                        $atot = '';$otot = '';
                               foreach ($ins as $key => $value) {
                                $atot += $value['grade']; 
                                $otot += $value['sumgrades']; 
                               }

                              if($tot_quiz[0]>0){
                               if($otot!=0||$atot!=-0){
                                $Intot[$i] = round(($atot/$otot)*70);
                               echo '<b>'.$Intot[$i].'</b>';
                             }else{
                              echo "Not taken";
                             }
                           } else{
                            echo "Quiz Not Created";
                           }
                         ?></td>
                        <td style="<?php echo $style; ?>"><?php $exs = get_Externalscore($row['userid'],$row['id']);
                        
                        $atot = '';$otot = '';
                               foreach ($exs as $key => $value) {
                                $atot += $value['user_grade']; 
                                $otot += $value['total_grade']; 
                               }
                                
                               if($otot!=0||$atot!=-0){ 
                                $Extot[$i] = round(($atot/$otot)*30);
                               echo '<b>'.$Extot[$i].'</b>';
                             }else{
                              echo "Not taken";
                             }
                                 ?></td>
                        <td>
                          <?php  if(isset($Intot[$i])&&isset($Extot[$i])){
                            echo $Intot[$i]+$Extot[$i]; 
                          }else{
                            echo "Not Applicable";
                          }  ?></td>
                         
                      </tr>
  <?php $i++;
  }
  //print_r($usr_data);
}
?>
<?php }
if(isset($_POST['cohortid'])){  
 $course_id = $_POST['course'];
 $d = $_POST['cohortid'];
 if($d != 0){ 
  $students = "SELECT DISTINCT u.id AS userid, u.firstname, u.lastname, c.id FROM mdl_user u JOIN mdl_user_enrolments ue ON ue.userid = u.id JOIN mdl_enrol e ON e.id = ue.enrolid JOIN mdl_cohort_members mcm on mcm.userid = u.id JOIN mdl_cohort mc on mc.id = mcm.cohortid JOIN mdl_role_assignments ra ON ra.userid = u.id JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel =50 JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id JOIN mdl_role r ON r.id = ra.roleid AND r.shortname = 'student' WHERE e.status =0 AND u.suspended =0 AND u.deleted =0 /*AND ( ue.timeend =0 OR ue.timeend > UNIX_TIMESTAMP(NOW()) )*/ AND ue.status =0 AND courseid ='$course_id' AND mc.id = '$d'";
}else{
$students = "SELECT DISTINCT u.id AS userid, u.firstname, u.lastname, c.id FROM mdl_user u JOIN mdl_user_enrolments ue ON ue.userid = u.id JOIN mdl_enrol e ON e.id = ue.enrolid JOIN mdl_role_assignments ra ON ra.userid = u.id JOIN mdl_context ct ON ct.id = ra.contextid AND ct.contextlevel =50 JOIN mdl_course c ON c.id = ct.instanceid AND e.courseid = c.id JOIN mdl_role r ON r.id = ra.roleid AND r.shortname = 'student' WHERE e.status =0 AND u.suspended =0 AND u.deleted =0 /*AND ( ue.timeend =0 OR ue.timeend > UNIX_TIMESTAMP(NOW()) )*/ AND ue.status =0 AND courseid ='$course_id'";
}

 
$students_result = $conn->query($students);     
//echo 'Num rows '.$students_result->num_rows;    
if ($students_result->num_rows > 0) { 
  $stu_csv = array(); $i= 1; 
  ?>
   <?php
  while($row = $students_result->fetch_assoc())
  { $tot_Assign = get_totalAssign($row['id']); 
    if($tot_Assign[0]>0){

      $style = "display:block;";
    }else{
      $style = "display:none;";
    }
?>  
                      <tr id="<?php echo $row['userid']; ?>" >
                        <td><?php echo $i; ?> </td>
                        <td><?php echo '<a class="get_user_data" data-course='.$row['id'].' data-uid = '.$row['userid'].'    >'.$row['firstname']; ?></td>
                        <td><?php echo $row['lastname'];; ?></td>
                        <td><?php 
                          $scorm_status = get_scromStatus($row['userid'],$row['id']);
                          $tot_scrom = get_totalSCORM($row['id']);
                           /*print_r(count($scorm_status));
                            print_r($tot_scrom[0]);*/
                            if($tot_scrom[0]>0){
                           if($tot_scrom[0] == count($scorm_status)){
                           if(in_array(0, $scorm_status)){
                              echo "In Progress";
                            }else{
                              echo "Yes";
                            }
                          }else{
                            echo "Not Completed";
                          }
                        }else{
                          echo "SCORM Not Created";
                        }
                        //echo $row['state']; ?></td>
                        <td><?php $ins = get_Internalscore($row['userid'],$row['id']);
                        $tot_quiz = get_totalQuiz($row['id']);
                        $atot = '';$otot = '';
                               foreach ($ins as $key => $value) {
                                $atot += $value['grade']; 
                                $otot += $value['sumgrades']; 
                               }

                              if($tot_quiz[0]>0){
                               if($otot!=0||$atot!=-0){
                                $Intot[$i] = round(($atot/$otot)*70);
                               echo '<b>'.$Intot[$i].'</b>';
                             }else{
                              echo "Not taken";
                             }
                           } else{
                            echo "Quiz Not Created";
                           }
                         ?></td>
                        <td style="<?php echo $style; ?>"><?php $exs = get_Externalscore($row['userid'],$row['id']);
                        
                        $atot = '';$otot = '';
                               foreach ($exs as $key => $value) {
                                $atot += $value['user_grade']; 
                                $otot += $value['total_grade']; 
                               }
                                
                               if($otot!=0||$atot!=-0){ 
                                $Extot[$i] = round(($atot/$otot)*30);
                               echo '<b>'.$Extot[$i].'</b>';
                             }else{
                              echo "Not taken";
                             }
                                 ?></td>
                        <td>
                          <?php  if(isset($Intot[$i])&&isset($Extot[$i])){
                            echo $Intot[$i]+$Extot[$i]; 
                          }else{
                            echo "Not Applicable";
                          }  ?></td>
                         
                      </tr>
                      <?php $i++; } ?>
                      
                      <?php 
                    }
                  }
?> <script type="text/javascript"> 
$(function () {
          
        $('#example3').dataTable({
          "aLengthMenu": [[10, 25, 50, 100,500,-1], [10, 25, 50,100,500,"All"]],
          
          "displayLength": 4,
          "bPaginate": true,
          
          "bLengthChange": false,
          "bFilter": false,
          "bSort": true,
          "bInfo": true,
          "bAutoWidth": false,
          "searchable": true,
          "dom": 'lfBrtip',
          "buttons": ['excel'],
          "processing": true,
          "serverSide": false,
          "searchable": false
        });
      }); 
  $('.get_user_data').on('click' ,function () { 
      course = $(this).attr('data-course');
      uid = $(this).attr('data-uid');
      username = $(this).html();
      course_txt = $("#labC option:selected").html();
    $.ajax({
                    processing : 'true',
                    serverSide : 'true', 
                    url: url+'/get_quiz_new.php',
                    type:"POST",
                    data :{ get_user_data:"abcde",course:course,course_txt:course_txt, username:username,uid:uid},
                     
                    success:function (data) {  
                        if(data){ 

                          $("#main_data").hide();
                          $("#ajax_data").show();
                          var obj = $.parseJSON(data);
                           
                          $("#course_name").html(obj.course_txt);
                          $("#user_name").html(obj.username);
                           $("#ajax_data #new_data tbody").html('');
                           /*$( "#ajax_data #new_data tbody tr" ).each( function(){
                              this.parentNode.removeChild( this ); 
                            });*/
                           $.each(obj.activity_data, function(key, value){
                             
                          if(value[1].tbl == 'scorm'){ var get_quiz_res = 'no_link';
                             
                            if (value[2].status.length == 0) {  
                           var sta = 'Not Taken';
                            }else{
                              var sta = value[2].status[0].completionstate;
                               if(sta==1){
                              sta = 'Yes';
                            }else{
                              sta = 'No';
                            }
                            }
                          }else if(value[1].tbl == 'quiz') { //console.log(value[2].marks);
                            if (value[2].marks.length == 0) {  
                           var sta = 'Not Taken';
                           var qid = '';   
                           var uid = '';
                           var cid = '';   
                            }else{
                              var sta = value[2].marks[0].score;     
                              var quiz = value[2].marks[0].quiz_id;   
                              var uid = value[2].marks[0].uid;
                              var cid = value[2].marks[0].coursesID; 
                              var get_quiz_res = 'get_quiz_res';  
                            }
                          }else if(value[1].tbl == 'assign'){  var get_quiz_res = 'no_link';
                            if (value[2].marks.length == 0) {  
                           var sta = 'Not Taken';
                            }else{
                              var sta = value[2].marks[0].grade;
                              var quiz = '';   
                               var uid = '';
                               var cid = '';                           
                            } 
                          }else if(value[1].tbl == 'feedback'){  var get_quiz_res = 'no_link';
                            var sta = 'Feedback';
                          }else if(value[1].tbl == 'workshop'){  var get_quiz_res = 'no_link';
                            var sta = 'Project';
                          }
                               $('<tr>').html('<td>'+key+'</td><td><img src="'+url+'/dist/img/' + value[1].tbl + '.svg" />' + value[0].name + '</td><td><a class="'+get_quiz_res+'" data-cid = "'+cid+'"  data-uid="'+uid+'"  data-quiz="'+quiz+'">' +sta+ '</a></td>').appendTo('#ajax_data #new_data tbody');
                              
                            });
                              $('.get_quiz_res').on('click',function(){ 
                                  cid = $(this).attr('data-cid');
                                  uid = $(this).attr('data-uid');
                                  quiz = $(this).attr('data-quiz');
                                  $.ajax({
                                                  processing : 'true',
                                                  serverSide : 'true', 
                                                  url: url+'/get_quiz.php',
                                                  type:"POST",
                                                  data :{ course_completion:'yes',cid:cid,uid:uid,quiz:quiz},
                                                   
                                                  success:function (data) {  
                                                      if(data){  
                                                        $.ajax({
                                                  processing : 'true',
                                                  serverSide : 'true', 
                                                  url: url+'/user_res_cc.php',
                                                  type:"POST",
                                                  data :{ course_completion:'yes',qua:data,uid:uid,quiz:quiz},
                                                   
                                                  success:function (data) {  
                                                      if(data){ 
                                                         $("#ajax_data").hide();
                                                        $('#assesment_data').show().html(data);
                                                           
                                                      }
                                                      else {
                                                          $('#labT').empty();
                                                      }
                                                  }
                                              });
                                                           
                                                      }
                                                      else {
                                                          $('#labT').empty();
                                                      }
                                                  }
                                              });
                                  
                               });
                           /*for(i=0;i<obj.activity_data.length;i++){
                            sl = i+1; data-uid = "'+uid+'" data-course = "'+cid+'"  data-qid="'+qid+'" 
                            console.log
                            $('<tr>').html('<td>'+sl+'</td><td>' + obj.activity_data[i][0].name + '</td><td></td>').appendTo('#ajax_data #new_data tbody');
                           }*/
                           
                             /*$.each(obj.activity, function(key, value){
                              console.log(value);
                                

                            }); */
                        }
                         
                    }
                });
 });

 $('.courses_page').on('click',function(){  
                      $("#ajax_data").hide();
                      $("#main_data").show();
                    });

 
  $('.get_quiz_res1').on('click',function(){ 
                      alert('test 1');return false;
                    });
  /*$('.get_quiz_res').on('click',function(){ 
    cid = $(this).attr('data-cid');
    uid = $(this).attr('data-uid');
    qid = $(this).attr('data-qid');
    $.ajax({
                    processing : 'true',
                    serverSide : 'true', 
                    url: url+'/get_quiz.php',
                    type:"POST",
                    data :{ course_completion:'yes',cid:cid,uid:uid,qid:qid},
                     
                    success:function (data) {  
                        if(data){ 
                           
                          $('#example1 tbody').html(data);
                            
                        }
                        else {
                            $('#labT').empty();
                        }
                    }
                });
  });*/
  
</script>
 <script type="text/javascript">
   </script>