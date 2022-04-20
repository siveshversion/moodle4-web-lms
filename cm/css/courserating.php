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

require_once('../config.php');
require_once($CFG->dirroot.'/lib/enrollib.php');
require_once($CFG->dirroot. '/course/lib.php');

global $CFG, $OUTPUT, $USER ;
 $site = get_site();

if ($CFG->forcelogin) {
    require_login();
}

$PAGE->set_heading($site->fullname);
	?>
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

 </style>

		
		<?php
		
echo $OUTPUT->header();

	echo $OUTPUT->heading('Course Ratings');
	echo '<br>' ;	
		$cid = $_GET['id'];
		
$courses = $DB->get_record_sql("select count(id) as id,ratingnumber from {course_rating}
where courseid = $cid and userid = $USER->id ");

if($courses->id < 1){
//echo "<a class='btn btn-primary' style='float: right;margin-top: -35px;' href='$CFG->wwwroot/cm/rating.php?id=$cid'>Write a review</a>";
			
		
	if(!empty($_REQUEST['submit'])){	
	$courseid = $_GET['id'];
		        $newcategory = new stdClass();
			    $newcategory->courseid = $_GET['id'];
			    $newcategory->userid = $USER->id;
			    $newcategory->ratingnumber = $_REQUEST['rating'];
                $newcategory->title = $_REQUEST['title'];
                $newcategory->comments = $_REQUEST['comment'];
		        $newcategory->created = time();
				
               $newcategorytype = $DB->insert_record('course_rating', $newcategory);
			   if(isset($newcategorytype)){
				  redirect("$CFG->wwwroot/cm/courserating.php?id=$courseid" ,'Rating Data is Added Successfully..',1);
			   }
		
	}
	
		?>
		 
		 
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
	
		<link rel="stylesheet" type="text/css" href="css/style.css">
<div class="row">
<div class="col-sm-12">
<form  action="" method="post" style="margin-left: 2%;">
<div class="form-group">
<h4>Create Course Review</h4>
<br>
<h6>Rate</h6>
<button type="button" class="btn btn-warning btn-sm rateButton" aria-label="Left Align" style="border-radius: 3px;height: 30px;">
<span class="material-icons">star</span>
</button> 
<button type="button" class="btn btn-default btn-grey btn-sm rateButton" aria-label="Left Align" style="border-radius: 3px;height: 30px;">
<span class="material-icons">star</span>
</button>
<button type="button" class="btn btn-default btn-grey btn-sm rateButton" aria-label="Left Align" style="border-radius: 3px;height: 30px;">
<span class="material-icons">star</span>
</button>
<button type="button" class="btn btn-default btn-grey btn-sm rateButton" aria-label="Left Align" style="border-radius: 3px;height: 30px;">
<span class="material-icons">star</span>
</button>
<button type="button" class="btn btn-default btn-grey btn-sm rateButton" aria-label="Left Align" style="border-radius: 3px;height: 30px;">
<span class="material-icons">star</span>
</button>
<input type="hidden" class="form-control" id="rating" name="rating" value="1">
<input type="hidden" class="form-control" id="courseid" name="courseid" value="12345678">
</div>
<div class="form-group">
<label for="usr">Add a headline </label>
<input type="text" class="form-control" style="width:50%" id="title" name="title" >
</div>
<div class="form-group">
<label for="comment">Write your review </label>
<textarea class="form-control" style="width:50%" rows="5" id="comment" name="comment" ></textarea>
</div>
<div class="form-group">
<input type="submit" class="btn btn-info" name="submit" id="submit" value="Save Review"> 
</div>
</form>
</div>
</div>	
 <script src="js/jquery-3.3.1.js"></script>
 <script type="text/javascript"  src="js/rating.js"></script>
 <style>
 .material-icons {
	 
	 font-family: 'Material Icons';
	 font-size: .8203125rem; 
     line-height: 0;
 }
 .btn-info {
 
     width: fit-content;
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
    padding: 0.1875rem 1.125rem;
    background-image: linear-gradient(90deg, #4fc1e9, #4a89dc);
 }

 </style>
 
<?php } ?>
 
 
<link rel="stylesheet" type="text/css" href="css/coursecatelearn.css">
<table id="example" class="display" style="width:100%">
  <thead>
            <tr>
                <th></th>
               

            </tr>
        </thead>
        <tbody>

              	
		<?php
 
	
$courseratings =	$DB->get_records_sql("select * from {course_rating} where courseid = $cid order by id desc ");
		

 foreach ($courseratings as $courserating) { 
 
       require_once($CFG->libdir. '/coursecatlib.php');
		
	   ?>
<tr>
<td>
    <div class=''>
		<div class='row'>
			<div class='[ col-xs-8 col-sm-offset-2 col-sm-12 ]'>
				<ul class='event-list'>
					
						<li>
						<?php 

                       $user = $DB->get_record('user', array('id' => $courserating->userid ));
						$pic = $OUTPUT->user_picture($user); ?>

						 <span style="margin-top: 5px"> <?php echo $pic ;?></span>
						    <div class='info'>
							<h2 class='title'><?php echo $user->firstname .' '. $user->lastname ; ?></h2>
							<span style="font-size:13px;font-weight:600"><?php echo $courserating->title ; ?></span>
							<div id='pstylev' style="font-size: 12px;padding: 5px 0px;margin-top: 3px;" ><?php echo $courserating->comments ; ?></div>
							
							<?php 
							
		
	
      $ratnum = number_format($courserating->ratingnumber, 1);
	  
	 if($ratnum == '0.0' || $ratnum == '0.1' || $ratnum == '0.2' || $ratnum == '0.3' || $ratnum == '0.4' ){
	    $image =  'img/rating/0.png';
	} 
	if($ratnum == '0.5' || $ratnum == '0.6' || $ratnum == '0.7' || $ratnum == '0.8' || $ratnum == '0.9' ){
		$image =  'img/rating/0.5.png';  
	}
	if($ratnum == '1.0' || $ratnum == '1.1' || $ratnum == '1.2' || $ratnum == '1.3' || $ratnum == '1.4' ){
		$image =  'img/rating/1.png';
	} 
	if($ratnum == '1.5' || $ratnum == '1.6' || $ratnum == '1.7' || $ratnum == '1.8' || $ratnum == '1.9' ){
		$image =  'img/rating/1.5.png';
	} 
	if($ratnum == '2.0' || $ratnum == '2.1' || $ratnum == '2.2' || $ratnum == '2.3' || $ratnum == '2.4' ){
		$image =  'img/rating/2.png';
	} 
	if($ratnum == '2.5' || $ratnum == '2.6' || $ratnum == '2.7' || $ratnum == '2.8' || $ratnum == '2.9' ){
		$image =  'img/rating/2.5.png';
	}  
	if($ratnum == '3.0' || $ratnum == '3.1' || $ratnum == '3.2' || $ratnum == '3.3' || $ratnum == '3.4' ){
		$image =  'img/rating/3.png';
	} 
	if($ratnum == '3.5' || $ratnum == '3.6' || $ratnum == '3.7' || $ratnum == '3.8' || $ratnum == '3.9' ){
		$image =  'img/rating/3.5.png';
	}
	if($ratnum == '4.0' || $ratnum == '4.1' || $ratnum == '4.2' || $ratnum == '4.3' || $ratnum == '4.4' ){
		$image =  'img/rating/4.png';
	} 
	if($ratnum == '4.5' || $ratnum == '4.6' || $ratnum == '4.7' || $ratnum == '4.8' || $ratnum == '4.9' ){
		$image =  'img/rating/4.5.png';
	}
	if($ratnum == '5.0'){
	    $image =  'img/rating/5.png';
	} 
	
	$overrat = explode('.',$ratnum);				
		if( $overrat[1] != 0){
			$rat = $ratnum ;
		}else{
			$rat = $overrat[0];
		}
	  
							?>
							
						<div style="margin-top: 5px;margin-bottom: 10px;">	
						<img style="width: 90px;" src="<?php echo  $image; ?>">
						<span class='baimg' style="padding-right: 12px;"> <?php echo $rat ; ?></span> 
						</div>

							
						</div>
						
						
				
					</li> 
					
					
				
				</ul>
			</div>
		</div>
	</div>
	</td>
	</tr>
<?php	   
 } 
 
 ?>
  
               
           
  </tbody>
</table>		
 

		
		<?php
 
echo $OUTPUT->footer();