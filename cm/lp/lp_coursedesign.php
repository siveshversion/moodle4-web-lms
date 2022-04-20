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
require_login();
?>

<?php
$lpid = $_GET['lpid'];

if(!empty($_GET['userid'])){
 $uid = $_GET['userid'];
} else {
	$uid = $USER->id;
}
echo $OUTPUT->header();
?>
<style>
#page-header {
	display : none;
}
.lpathbox .width-set-list {
    background: #fff !important;
}
.lpathbox .boxbtn {
	padding : 0px !important;
	
}
.lpathbox .boxbtn, .courseInfo, .viewLpCourse {
	padding: 5px 10px ;
}
.lpathbox .boxbtn {
	padding : 15px 20px !important;
}
</style>


 <!DOCTYPE html>
<!-- saved from url=(0070)https://moodle.abaralms.net/learner/catalog/learning_path?course_id=74 -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>Learning Plan Course Details</title>
        <meta charset="1">
        
        <link rel="icon" href="https://d3ev7lf8o29h40.cloudfront.net/lms-updated/ent_portal1947/images/abarafavicon2_fdy1h9w_thumb.png" type="image/gif">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <style>
        :root {
    --border-color: #ddd;
    --color-black: #000000;
    --color-white: #ffffff;
    --color-offwhite: #eee;
    --color-green: #008000;
    --color-yellow: #fab51c;
    --color-grey: #808080;
    --color-red: #ff0000;
    --color-primary: #fd0c0c;
    --color-secondaryDark: #525252;
    --color-tertiaryDark: #3e3e3e;
    --color-custmSecondaryColor: #212121;
    --color-topHeaderBg: #ffffff;
    --main-text-color: #666666;
    --main-title: #333333;
    --main-whitetitle: #333333;
    --main-bg-color: #eeeeee;
    --color-dashboardcardBg: #fcfcfc;
    --color-cardDarkbg: #e5e5e5;
    --color-cardLightbg: #ffffff;
    --btn-textcolor: #ffffff;
}


        </style>
		<style>
#ui_whitetheme  .commonAccordion .card-body .card .card-header a{
      background: #ffffff;
}
#ui_whitetheme  #my-passport-ui #date-dropdown .form-group .input-group-prepend .input-group-text,
#ui_whitetheme .filterWrap .filter-card .form-control,
#ui_whitetheme .certificates-dropdown .dropdown-menu input,
#ui_whitetheme .inner-content .form-control, #ui_whitetheme .tab-pane .absolute .inner-content .form-control
{
  border: 1px solid #ddd;
}

#ui_whitetheme .vs-drop-details .input-group-text{  border-color: #ddd !important;}

 html .dashboardfull #topHeader nav.navbar{
  background-color: #ffffff !important;
}
 
#ui_whitetheme #cke_reply_body .cke_top,
#ui_whitetheme #cke_reply_body .cke_bottom,
#ui_whitetheme #cke_message_text .cke_top,
#ui_whitetheme #cke_message_text .cke_bottom{
  background-color: #eeeeee !important;
}
/*need to keep this*/

#ui_whitetheme #ui_login .lowerBtnWrap ul li p{
  color: #666666 !important;
}

#ui_whitetheme .messageWrapper .composeMail .dropData,
#ui_whitetheme .calenderWrapper .fc-ltr .fc-axis,
#ui_whitetheme #dashboard #marketingCalender .ei-event{
    background: #e5e5e5;
}

#ui_whitetheme #myModal hr {border-color: #ddd;}

#ui_whitetheme #kr-modal .modal-dialog .modal-body .string-limit-name,
#ui_whitetheme #ui_SetPassword .formtitle,
#ui_whitetheme #signup .formtitle,
#ui_whitetheme #ui_login .formtitle,
#ui_whitetheme #ui_resetPassword .formtitle,
#ui_whitetheme #ui_activateLearner .formtitle,
#ui_whitetheme #ui_login .leftWrapper .title,
#ui_whitetheme #ui_login .lowerBtnWrap .antBlock .title,
#ui_whitetheme #signup .formtitle,
#ui_whitetheme #ui_login .formtitle,
#ui_whitetheme #ui_resetPassword .formtitle,
#ui_whitetheme #ui_activateLearner .formtitle,
#ui_whitetheme .modal .modal-body .subHeading,
#ui_whitetheme .queryWrapper .messageBlock .card h5.name,
#ui_whitetheme .messageWrapper .messageBlock .card h5.name,
#ui_whitetheme .modal .modal-body .mainHeading,
#ui_whitetheme  .currentStatus{
    color: #333333}
#ui_whitetheme .btn-filter,
#ui_whitetheme .report-search-wrapper .reportSelection-dropdown-btn,
#ui_whitetheme .request-dropdown-btn,
#ui_whitetheme .dataTables_paginate .paginate_button a,
#ui_whitetheme .dataTables_paginate  a,
#ui_whitetheme .modal .modal-body div.dataTables_wrapper div.dataTables_info,
#ui_whitetheme #specificteam_form .dataTables_length label, 
#ui_whitetheme #linemanagers_form .dataTables_length label, 
#ui_whitetheme #instructors_form .dataTables_length label,
#ui_whitetheme #masteradmin_form .dataTables_length label,
#ui_whitetheme .modal .modal-body .basicInfo{
  color: #666666;
}
#ui_whitetheme .certificate-wrapper .btn-ext-certificate,
#ui_whitetheme .certificate-wrapper .btn-filter,
#ui_whitetheme .certificate-wrapper .status-dropdown-btn{
 color: #666666!important; 
}

#ui_whitetheme #dashboard #marketingCalender .ei-nav-container i, 
#ui_whitetheme #dashboard #marketingCalender .ei-nav-container-2 i,
#ui_whitetheme #dashboard #marketingCalender .ei-nav-container-mcal i{
  background: #fd0c0c;
}
#ui_whitetheme .calenderWrapper .fc-day-header{
  background: #e5e5e5 !important;
  color:#666666;
}

#ui_whitetheme .inner-content .table.certificate-table thead th,
#ui_whitetheme  .inner-content .table.certificate-table td, #ui_whitetheme .commonAccordion .card-header,
#ui_whitetheme .tab-pane .absolute .inner-content .table th, 
#ui_whitetheme .inner-content .table th, #ui_whitetheme .tab-pane .absolute .inner-content .table td,
#ui_whitetheme .inner-content .table td
{
  border-bottom: 1px solid #ddd;
}

#ui_whitetheme .modal .modal-body .table td{
    border-top: 1px solid #ddd;
}

#ui_whitetheme .checkmark{
    border: 1px solid #ddd;
}

#ui_whitetheme #knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-body ul.card-icons-wrapper li.card-icon.box-wrap{
    background: #3e3e3e;
}

#ui_whitetheme .messageWrapper .messageBlock .icon{
  color: #3e3e3e;
}
 
 #ui_whitetheme .radio-custom:checked + .radio-custom-label:before,
 #ui_whitetheme .instantActionBtn .custom-control-input:checked ~ .custom-control-label::before {
    background: #fd0c0c !important;
}

/*White Theme**************************************************************************************************/

/*Start Of Common Css*******************************************************************************************************/
body{
  background: #eeeeee;
  color: #666666 !important;
}

.border-right {
  border-right: 1px solid #ddd !important;
}

.dropdown-item:focus,
 .dropdown-item:hover,
.dropdown-item.active,
.dropdown-item:active {
    color: #ffffff !important;
    text-decoration: none;
    background-color: #fd0c0c !important;
}
.simplebar-scrollbar {
  background: #fd0c0c;
}


input[type=email]::-webkit-input-placeholder,
input[type=text]::-webkit-input-placeholder {
  /* Chrome/Opera/Safari */
  color: #666666;
}

input[type=email]::-moz-placeholder,
input[type=text]::-moz-placeholder {
  /* Firefox 19+ */
  color: #666666;
}

input[type=email]:-ms-input-placeholder,
input[type=text]:-ms-input-placeholder {
  /* IE 10+ */
  color: #666666;
}

input[type=email]:-moz-placeholder,
input[type=text]:-moz-placeholder {
  /* Firefox 18- */
  color: #666666;
}

.orSeprator:after {
  border-top: 1px solid #ddd;
}
.orSeprator:before {
  border-top: 1px solid #ddd;
}

.dropBtn span {
  color: #fd0c0c;
}

.dropState span {
  background: #ffffff;
}

.hr {
  border-top: 1px solid #ddd;
}

.custmBtnDefault {
  background: #3e3e3e !important;
  color: #ffffff;
}

.custmBtnPrimary {
  background: #f15a24 !important; 
  color: #ffffff !important;
}

.custmBtnSecondary {
  background: #212121;
  color: #ffffff;
}

.bg-primaryColor {
  background: #fd0c0c;
}

.bg-lightBlack {
  background: #212121;
}

.btn-black:hover {
  color: #ffffff;
}

#style-3::-moz-scrollbar-track {
  background-color: #212121;
}

#style-3::-webkit-scrollbar-thumb {
  background-color: #000000;
}

.pagination .page-item.active .page-link {
  color: #fd0c0c;
}
.pagination .page-item .page-link {
  color: #ffffff;
}

.orgbg {
  background: #fd0c0c !important;
}

.list li span.fa {
  color: #fd0c0c;
}

.cardContent .form-group label {
  color: #666666;
}
/*need to keep */
.cardContent .form-group .form-control {
  background-color: #ddd !important;
  color: #666666;
  border-color: #ddd;
}
/*need to keep */

.subHeading h5, .cardContent h5 {
  color:#333333}

.cardBg {
  background: #eeeeee;
}
/* no need to add this*/
/*main {
  margin-top: 75px;
}*/

.orange {
  color: #fd0c0c !important;
}
.red {
  color: #ff0000;
}
.yellow {
  color: #fab51c;
}
.grey {
  color: #808080;
}

.green {
  color: #008000;
}
.white{
  color: #ffffff;
}
.searchClose {
  color: #eee;
}

nav.navbar ul.navbar-nav .popover-region .popover-region-toggle i.fa {
	font-family: "simple-line-icons";
}

/* Create a custom checkbox */
.checkmark {
       background-color: #eeeeee !important;
    border: 1px solid #ddd;
}
/* On mouse-over, add a grey background color */
.customCheckBoxWrap:hover input ~ .checkmark {
  background-color: #eeeeee  !important;
}

/* Style the checkmark/indicator */
.customCheckBoxWrap .checkmark:after {    border: solid #fd0c0c;}
/*End****************************************************************************************************************************/

/*Form Common CSS****************************************************************************************************************/
.customForm .form-group label {
  color: #666666 !important;
}
.customForm .form-group .form-control, .customForm .form-group .input-group .input-group-text {
  color: #666666;
  border-color: #ddd;
}

.customForm .form-group .input-group .search-arrow{
    border-right: 1px solid #ddd;
}

.customForm .form-group .form-control::-webkit-input-placeholder {
  color: #666666;
}

select.language {
  background-color: #fd0c0c !important;
}

.modal .modal-header .modal-title {  color: #333333}
.modal .modal-header button.close {
  color: #ffffff;
  background: #3e3e3e;
}

.modal .modal-body .basicInfo {  color: #ffffff;}

.modal .modal-body .basicInfo p.c_course_sts,
.modal .modal-body .basicInfo p:nth-child(2){    border-left: 1px solid #ddd;}
.modal .modal-body .subHeading {  color:  #333333;}
.modal .modal-body .mainHeading {  color: #333333;}
.modal .modal-body .custom-checkbox .custom-control-label {  color: #333333}
.modal .modal-body .custom-checkbox .custom-control-label:before {
  background: #eeeeee;}

.modal .modal-body .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
   border: solid #fd0c0c;
}
.modal .modal-body .custom-control-input:checked ~ .custom-control-label::before {
  color: #ffffff;
  background-color: #eeeeee;
}
.modal .modal-body .table td {
  color: #666666;
}
.modal .modal-body .table thead th {
  color: #333333}
.modal .modal-body .threeGriedTier {
  border-top: 1px solid #ddd;
}

.wrapBlock{
    border-bottom: 1px solid #ddd;
}

.cartWrapper .cartItem .cartItemDetail .title{
  color: #333333}

.cartWrapper .cartTotal .subTotal{
    color:  #333333}
.cartWrapper .cartTotal .totalPrice{
    color: #fd0c0c;
}

.cartWrapper .qty .count {
    color:  #333333    border: 1px solid #ddd;
}
.cartWrapper .qty .plus {
    background: #3e3e3e;
    }
.cartWrapper .qty .minus {
    background: #3e3e3e;
}
.cartWrapper .qty .minus:hover{
    background-color: #fd0c0c !important;
}
.cartWrapper .qty .plus:hover{
    background-color: #fd0c0c !important;
}
/*Prevent text selection*/

.cartWrapper .cartItemDetail .price p{
    color: #fd0c0c;
}

.cartWrapper .couponWrap .nextArrow{
    background: #fd0c0c !important;
  
 }
 .cartWrapper .couponWrap .percentIcon{
     color: #666666;
 }
 .cartWrapper .couponWrap .nextArrow .input-group-text{
   
    color:  #333333 }
/*End of the MOdal Css***********************************************************************************************************/

/*Start of the Topheader******************************************************************************************************/

#topHeader #searchBar {

  background: #ffffff;
 
  color: #333333;
 
}

#topHeader nav.navbar {
  background: #ffffff !important;
}
#topHeader .navbar-left-panel .nav-item.active > a span {
  color: #fd0c0c;
}
#topHeader .navbar-left-panel .nav-item a {
  color: #333333}
/*need to keep this*/
#topHeader .navbar-left-panel .nav-item a:hover {
   color: #ffffff ;
}

#topHeader .navbar-right-panel .nav-item .nav-link span.fa {
  color: #333333}

#topHeader .navbar-right-panel .nav-item .dropdown-toggle {
  background: #3e3e3e;
 
}

#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu {
  background: #3e3e3e;
  color: #666666;
  
}
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item {
 
  color: #666666;
 
}
/*need to keep this*/
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item:hover {
  background: #fd0c0c;
}
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item span {
  color: #eee;

}

#topHeader .navbar-toggler-icon {

    color: #ffffff;

}
/*End of the topHeader********************************************************************************************************/

/*Start of the pageheader***************************************************************************************************/

.pageHeader .filter .fa,
.quickAction li a span.fa{
  color: #333333;
  
}

.quickAction li.search{
  border-right: 1px solid #ddd;
}
.quickAction li.sort .dropdown-item{
    color: #666666;
   
}
.quickAction li.sort .dropdown-menu {
   
    background: #3e3e3e;
    color: #666666;
  
}
.quickAction li.search .form-group{
   
    background: #3e3e3e;
   
}
.quickAction li.search .fa-question-circle{

    color: #fd0c0c;
}
.quickAction li.search .form-group .input-group-text{
    background: #3e3e3e;
    border-color: transparent;
    color:  #333333}
.quickAction li.search .form-group .form-control{
    background: #3e3e3e;
    border-color: transparent;

}

  
.rightModalHeader{
    background: #3e3e3e;
}
.rightModalHeader h4{
    color: #333333;
}
    

.filter-Wrap .form-control{
  
  color: #666666;
}
.pageHeader h3 {
  color: #333333}

/*End of the page header**************************************************************************************************/

#myProfile .profileInfo {
  color:  #ffffff;
}

#myProfile .profilePic .fa{
    color: #fd0c0c;
    background: #ffffff;
}

/*End of theMy Profile ********************************************************************************************************/

/*login,signup,activate learner, forget password******************************************************************************/

#ui_SetPassword .form-group.input-group .input-group-prepend,
#ui_resetPassword .form-group.input-group .input-group-prepend,
#ui_activateLearner .form-group.input-group .input-group-prepend {
  background: #212121 !important;
}
#ui_SetPassword .form-group.input-group .input-group-prepend .input-group-text,
#ui_resetPassword .form-group.input-group .input-group-prepend .input-group-text, 
#ui_activateLearner .form-group.input-group .input-group-prepend .input-group-text {
  border-color: #ddd;
}

#ui_login .aboutContent {
    color: #666666;
}

#ui_login .leftWrapper .login_info_content_link .arrowbtn {
  color: #fd0c0c;
}

#ui_login .leftWrapper .login_info_content_link .bordclass {
  border-top: 1px solid #ddd;
}
#ui_login .leftWrapper .login_info_content_link .fa {
    color: #ffffff;
    background: #fd0c0c;
}
#ui_login .leftWrapper .title {
    color: #ffffff;
   }
#ui_login .leftWrapper .title .secondPart {
  color:  #ffffff;
}
#ui_login .leftWrapper .title .firstPart {
  color:  #ffffff;
}

#ui_login .lowerWrapper p {
  color: #666666;
}
#ui_login .lowerWrapper a {
  color: #fd0c0c;
}
#ui_login .lwbtnSection .custom-control-label, #ui_login .lwbtnSection a {
  color: #666666 !important;
}

#ui_login .lowerBtnWrap .antBlock .title {
  color:  #ffffff;
  background: #212121;
}

#ui_login .lowerBtnWrap .counter {
  background: #fd0c0c;
  color:  #ffffff;
}

#ui_login .lowerBtnWrap ul li span.date {
  color: #fd0c0c;
}
#ui_login .lowerBtnWrap ul li p {
  color: #666666;
}

#ui_login .form-group.input-group .input-group-prepend {
  background: #212121 !important;
}
#ui_login .form-group.input-group .input-group-prepend .input-group-text {
  border-color: #ddd;
}

#signup #language, #ui_login #language, #ui_resetPassword #language, #ui_activateLearner #language {
  color:  #ffffff;
}

#signup .socialMedia p, #ui_login .socialMedia p, #ui_resetPassword .socialMedia p, #ui_activateLearner .socialMedia p {
  color: #666666;
}
#ui_SetPassword .formtitle,
#signup .formtitle, #ui_login .formtitle,
#ui_resetPassword .formtitle, #ui_activateLearner .formtitle {
  color:  #ffffff;
}
#signup .customForm .form-group .form-control, #ui_login .customForm .form-group .form-control, #ui_resetPassword .customForm .form-group .form-control, #ui_activateLearner .customForm .form-group .form-control, #ui_SetPassword .customForm .form-group .form-control  {
  background: #212121 !important;}

#signup .simplebar-scrollbar {
  background: #fd0c0c;
}

.attachment .card {
  background: #525252;
}

.settingWrapper #splashPage .card-body .fa.fa-file-text {
  color:  #ffffff;
}
.settingWrapper #splashPage .card-body h5 {
   color: #ffffff;
}
.settingWrapper #systemDiagnostics h5 {
  color: #333333;
}

.settingWrapper #systemDiagnostics .card-body .fa.fa-file-text {
  color: #333333;
}
.settingWrapper #systemDiagnostics .card-body .cardWrap {
  background: #eeeeee;
  color: #666666;
}

#dateFormat .cardContent .custom-control {
  background: #eeeeee;
  color: #666666;
}

#ui_login .custom-control-input:checked ~ .custom-control-label::before,
#dashboardWig .widget .custom-control-input:checked ~ .custom-control-label::before {
  border-color: #ddd !important;
  background-color: #eeeeee !important;
}
/*need to keep this*/
#ui_login .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
  border: solid #fd0c0c;
}
/*need to keep this*/
/*need to keep this*/
#dashboardWig .widget .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
 border: solid #fd0c0c;
}
/*need to keep this*/

.instantActionBtn .custom-radio .custom-control-input:checked ~ .custom-control-label::after {
  border: solid #ffffff;
}

.instantActionBtn .custom-control-input:checked ~ .custom-control-label::before,
{  background: #3e3e3e !important;
}

#ui_login .custom-control {
  background: #3e3e3e;
}

#dashboardWig .custom-control .custom-control-label, #ui_login .custom-control .custom-control-label {
  color: #333333}

 #ui_login .custom-control .custom-control-label:before {
  background: #eeeeee;
}
/*Need to keep this change*/
/*Need to keep this change*/
#dashboardWig .custom-control .custom-control-label:before{
    background: #eeeeee;
    border-color: #ddd;
}

/*End of the Setting  **************************************************************************/
.accouncementWrapper .announcementTitle {
  color: #fd0c0c;
}

.accouncementWrapper .custom-file .custom-file-label { color: #666666;}

.messageWrapper .message .attachment .fileImgField .input-group span.fa {  color: #fd0c0c;}
.messageWrapper .message .messageBody a {  color: #666666;}
.messageWrapper .message .messageHeader .user-name {  color: #333333}
.messageWrapper .message .messageHeader .userProfile span.initialLetter {
  background: #fd0c0c;
  color: #ffffff;
}

.messageWrapper .composeMail .dropData {
  background: #3e3e3e;
  color: #666666;
}

.queryWrapper .messageBlock .custom-control-input:checked ~ .custom-control-label::before,
.messageWrapper .messageBlock .custom-control-input:checked ~ .custom-control-label::before {
  color:  #ffffff;
  border-color: #fd0c0c;
  background-color: #fd0c0c;
}
.queryWrapper .messageBlock .custom-checkbox .custom-control-label::before,
.messageWrapper .messageBlock .custom-checkbox .custom-control-label::before {
  background: #212121;
  color: #eee;
}

.messageWrapper .messageBlock .icon {
  color: #eee;
}
.queryWrapper .messageBlock .icon{
  color: #eee;
}

.queryWrapper .messageBlock .card.active ,
.messageWrapper .messageBlock .card.active {
  background: #ffffff;
}
.queryWrapper .messageBlock .card.active .card-body span img{
    color: #ffffff;
}

.queryWrapper .messageBlock .card.active:after,
.messageWrapper .messageBlock .card.active:after {
  background: #fd0c0c;
}
.queryWrapper .messageBlock .card.active:before,
.messageWrapper .messageBlock .card.active:before {
  color: #eee;
  background: #fd0c0c;

}

.queryWrapper .messageBlock .card h5.name,
.messageWrapper .messageBlock .card h5.name {
  color: #fd0c0c !important;
}
.messageWrapper .messageBlock .visited h5.name {
    color: #333333 !important;    
}
.messageWrapper .sendBoxMsg  .card h5.name{
    color: #333333}
.queryWrapper  .messageBlock .card .card-body span img,
.messageWrapper .messageBlock .card .card-body span img,
.queryWrapper  .messageBlock .card .card-body .fa.fa-chevron-right,
.messageWrapper .messageBlock .card .card-body .fa.fa-chevron-right {
  color: #ffffff;
}
.messageWrapper .messageBlock .card .nameInitialLetter {
  background: #fd0c0c;
  color: #ffffff;
}
.queryWrapper .moreShortInfo{
    border-top: 1px solid #ddd;
}
.queryWrapper .messageBlock .card .card-body .title{
    color:  #333333}

.queryWrapper .msgContent .subHeading .title .viewAll{
    border-left: 1px solid #ffffff;
}
.queryWrapper .msgContent .type_msg .input-group .write_msg{
  border-color: #ddd;
  color: #333333}
.queryWrapper .msgContent .type_msg .input-group-prepend .input-group-text{
    border-color: #ddd;
}
.queryWrapper .msgContent .type_msg .msg_send_btn{
    background: #fd0c0c;
    border-color: transparent;/*add this*/
    color: #ffffff;
}
.queryWrapper .msgContent .msg_history .received_withd_msg  {
    background: #212121;
    color: #666666;
}
.queryWrapper .msgContent .msg_history .sent_msg {
    background: #212121;
}
.queryWrapper .msgContent .msg_history .received_msg .name,
.queryWrapper .msgContent .msg_history .incoming_msg .name,
.queryWrapper .msgContent .msg_history .outgoing_msg .name{
    color: #333333}

.currentStatus .status{
    color: #ffffff;
}
.commonThumbnailSlider .carousel-item.active .panel-thumbnail {
  border: 3px solid #fd0c0c;
}
.commonThumbnailSlider .carousel-control-prev-icon {
    background: #fd0c0c;
        color: #ffffff;
}
.commonThumbnailSlider .carousel-control-next-icon {
  background: #fd0c0c;
}
.commonThumbnailSlider .carousel-item .panel-info .generalInfo {
  border-right: 1px solid #ddd;
}
.commonThumbnailSlider .carousel-item .panel-info .generalInfo .title {
  color: #ffffff;
}

.commonThumbnailSlider .carousel-item .panel-info .moreInfo span.fa {
  color: #fd0c0c;
}
.commonThumbnailSlider .carousel-item .panel-info .description .title {
  color: #666666;
}
.commonThumbnailSlider .carousel-item .panel-info .description .attempt {
  color: #ffffff;
}

#learningObjCarousel {
  border-top: 1px solid #ddd;
}

.modalCarousel {
  background-color: #ffffff;
  border-top: 1px solid #ddd;
}

.popContent .mainContent {
    background: #eeeeee !important;
}
.popContent .header {
  color:  #333333;
  background: #ffffff;
}
.popContent .header .leftPartheader .toggleBar {
  background: #fd0c0c;
  border-color: transparent;
  color:  #ffffff;
}
.popContent .header .popUpClose {
  color:  #ffffff;
  background: #fd0c0c;
}

#dashboard #profile .profileInfo h5{
  color:  #ffffff !important;
}
#dashboard #profile .quickLinks {
  background: #e5e5e5;
}

#dashboard .cardContent {
  background: #fcfcfc;
}
#dashboard .cardContent .viewAll {
  border-left: 1px solid  #ffffff;
  color: #333333}

#dashboard #upcomingCourse .overlay {
  color: #ffffff;
}

#dashboard #leaderBoard .leaderProfile .imgWrapper .countNo {
  background: #000000;
  color: #ffffff;
}
#dashboard #leaderBoard .leaderProfile .info .name {
  color: #ffffff;
}

.sliderWrapper .title {
  color: #eee;
}
.sliderWrapper .title .viewAll {
  border-left: 1px solid  #ffffff;
}
#announcementWrapper .announcement li {
  background: #e5e5e5;
}

.dashboardfull .scroller, .learningpathui .scroller{
  background-color: #ffffff;
}

.dashboardfull .box h2, .dashboardfull .inner-content h2, .learningpathui .inner-content h2, .learningpathui .box h2 {
  color:  #333333}
.dashboardfull .carousel-control-next, .dashboardfull .carousel-control-prev {
  background: #fd0c0c;
}
.dashboardfull .carousel-control-prev {
  background: #525252;
}
.carouseltab .nav-tabs .nav-link.active:after {
  border-bottom: 10px solid #fd0c0c;
}
#dashboard #profile .quickLinks {
  background: #e5e5e5;
}

#dashboard #upcomingCourse .overlay {
  color: #ffffff;
}

#dashboard #leaderBoard .leaderProfile .imgWrapper .countNo {
  background: #000000;
  color: #ffffff;
}
#dashboard #leaderBoard .leaderProfile .info .name {
  color: #ffffff;
}
.sliderWrapper .title {
  color: #eee;  
}
.sliderWrapper .title .viewAll {
  border-left: 1px solid  #ffffff;
}
#announcementWrapper .announcement li {
  background: #e5e5e5;
}

.dashboardfull .scroller, .learningpathui .scroller{
  background-color: #ffffff;
}

.dashboardfull .box h2, .dashboardfull .inner-content h2, .learningpathui .inner-content h2, .learningpathui .box h2 {
  color:  #333333}
.dashboardfull .carousel-control-next, .dashboardfull .carousel-control-prev {
  background: #fd0c0c;
}
.dashboardfull .carousel-control-prev {
  background: #525252;
}
.carouseltab .nav-tabs .nav-link.active:after {
  border-bottom: 10px solid #fd0c0c;
}

.keywords .btn-outline-secondary, .lpathbox .btn-outline-secondary {
  color: #666666;
  border-color: #ddd;
}
.keywords .btn-outline-secondary:hover, .lpathbox .btn-outline-secondary:hover, .lpathbox .btn-outline-secondary:active:focus,
.modal .btn-outline-secondary, .modal .btn-outline-secondary:hover
{
  color: #666666 !important;
  border-color: #ddd !important;
}

.progress {
  background-color: #525252;
}
.progress-bar {
  background-color:#fd0c0c;
}

.carouseltab .nav-tabs {
  border-top: 1px solid #ddd;
  background: #525252;
  border-bottom: 1px solid #ddd;
}
.carouseltab .nav-link {
  color: #666666;
}
.carouseltab .nav-link:hover, .carouseltab .nav-link.active {
  border-color: transparent;
  background:#fd0c0c;
   color: #ffffff;
}

/*Need to keep this*/
.tab-pane .absolute .inner-content h5,
.inner-content h5 {
  color: #ffffff;
}

.inner-content .table,
.tab-pane .absolute .inner-content .table {
  color: #666666;
}
.inner-content .table thead th,
.tab-pane .absolute .inner-content .table thead th {
  border-bottom: 1px solid #3e3e3e;
  color: #333333;
}
.tab-pane .absolute .inner-content .table th,
.inner-content .table th,
.tab-pane .absolute .inner-content .table td,
.inner-content .table td{
  border-bottom: 1px solid #3e3e3e;
}
.inner-content .form-control,
.tab-pane .absolute .inner-content .form-control, .input-group-text {
  background: #525252;
  border: 1px solid #3e3e3e;
  color: #666666;
}
/*End Need to keep this*/
.calenderWrapper .nav-tabs .nav-link.active{
  color: #ffffff;
  background-color: #fd0c0c;
  border-color: #fd0c0c; 
}
.calenderWrapper .nav-link{
background-color: #3e3e3e;
color: #ffffff;
}
.calenderWrapper{
background: #fcfcfc;
 }

#announcementWrapper .owl-theme .owl-nav button{
        background: #fd0c0c;
       
}   
      .carousel-lo .item:hover .panel-thumbnail,  .carousel-lo .item.active .panel-thumbnail, #catlog .upcomingcourseui .carousel-lo .item:hover .panel-thumbnail {
          border: 3px solid #fd0c0c;
      }
     
      .carousel-lo .item .panel-info .generalInfo .title {
          color: #ffffff;
      }
      .carousel-lo .item .panel-info .generalInfo {
            border-right: 1px solid #ddd  !important;
      }
      .carousel-lo .item .panel-info .moreInfo span.fa {
          color: #fd0c0c;
      }
     .carousel-lo .item .panel-info .description .title {
          color: #666666;
      }
      .carousel-lo .item .panel-info .description .attempt {
          color: #333333      }

.slick-slide .fa-info, .gridLayout .video-slide-image .fa-info{ 
    color:#ffffff;
  }
.cardbadge{color:#ffffff; }
.wpvs-close-video-drop {
 background: #fd0c0c;
  color: #ffffff;
  }
  
        .sessionWrap .backSession h6{
          color:  #333333        }
        .sessionWrap .backSession p{
          border-right: 1px solid #ddd;
        }
        
         .filterWrap .filter-card .input-group-text,
        .filter-Wrap .filterCalenderWrap .input-group-text {
          border: 1px solid #ddd;
        }
        

      #searchBar1 {
           background: #ffffff;
      }
      .searchClose1{
          color: #eee;
          color: #ffffff;
      }

/*.ctype{ display: none;}*/

.wpvs-close-video-drop {
  background: #fd0c0c;
  color: #ffffff;
 }
   
.commonStyleitemCard .fa-info{
    color: #ffffff;}

.commonStyleitemWrapper.active-slide {
    border: 2px solid #fd0c0c;
}

.video-item-grid .active-slide:after, .video-list-slider .active-slide:after{
    border-top: solid 15px #fd0c0c;
}
.slide-category.active-slide{
   border: 2px solid transparent;
}
.commonAccordionContent .subscriptionCard{
          border: 1px solid #ddd;
      }
  
      .commonAccordionContent .tabHeading .title{
        color: #333333        border-bottom: 1px solid #ddd;
      }
      .commonAccordionContent .tabHeading .title span{ background: #000000; }

        .commonAccordion{
          background: #eeeeee;
        }
        
        .commonAccordionContent .nav-tabs .nav-link {
          background-color: #3e3e3e;
          color: #ffffff;

      }

      .commonAccordionContent .nav-tabs .nav-link.active{
          color: #ffffff;
          background-color: #fd0c0c;
          border-color: #fd0c0c;
        }
        .commonAccordion .card,
        .commonAccordion .card-header{ 
          background:#eeeeee;
        }
        .commonAccordion .card-body .card .card-header a{
          color: #666666 !important;
          background: #3e3e3e;
        }
         .commonAccordion .card-body .card .card-header .active{
          color:  #ffffff !important;
          background: #fd0c0c !important;
        }
        .commonAccordion .title > a {
          color: #666666 !important;
      }
    
      .commonAccordionContent .subscriptionCard, .timeline-icon{
           background: #eeeeee !important;
      }
     
      .commonAccordion .card-body .card .card-header .title > a:after{
        display: none;
      }
      .commonAccordion .title > a:after {
        background: #3e3e3e;
        color: #ffffff;
      }
      .commonAccordion .title >  a[aria-expanded="true"].show:after {
          background: #fd0c0c;
          color: #ffffff;
      }
      .commonAccordion .card-header{
        border-bottom: 1px solid #000000;
      }
      .commonAccordion .card{
        background: #eeeeee;
      }
      
      #timeline{
        background-color: #ddd;
      }

       .marker{
          border: 1px solid #ddd;
          color: #666666;
          background-color: #666666;
      }
   
#footer {
  color: #666666;
}

.bootstrap-tagsinput .tag{
    color: #666666 !important
}
.bootstrap-tagsinput{
    background-color: transparent !important;
    border-color: #ddd !important;
    color: #666666 !important;
}

@media  screen and (max-width: 767px) {
  

  #topHeader .navbar-dark .navbar-toggler {
    background-color: #fd0c0c;
  }
 
  #topHeader .navbar-nav.navbar-right-panel .nav-item .dropdown-toggle {
    background: #3e3e3e;
  }

}

@media  screen and (min-width: 768px) and (max-width: 1024px) {
 
  #dateFormat .cardContent .custom-control {
       color: #666666;
  }

  #systemDiagnostics .fa.fa-file-text {
    color: #ffffff;
  }
   #splashPage .card-body .fa.fa-file-text {
    color: #ffffff;
  }
}

      
.lpathbox {      
  border-left: 1px solid #ddd;      
}   
.lpathbox .progressdiv {    

  background: #3e3e3e;    
  color: #3e3e3e;   
}   
.lpathbox .complete {   
  background:#fd0c0c;    
  color:#fd0c0c;   
}   
  
.lpathbox .boxbtn{  border-left: 2px solid #eeeeee; }    
.lpathbox .width-set-list {   
  background:#525252;       
}   
  
.lpathbox .coursetype{    
  border-right: 1px solid #ddd;    
}   
.align-self-center .active-slide{ border-left: 2px solid #eeeeee;}


/*--------------------------Dev css-----------------------------*/

.topheader-itemCount{
    background: #fd0c0c;
    color: #ffffff;
}
.languageWrapper .language .btn{        
    background: #fd0c0c !important;        
    border-color: #fd0c0c !important;              
    color: #ffffff;     
}       
.languageWrapper .language .dropdown-menu{
    background: #3e3e3e;        
    color: #666666;         
}      
.languageWrapper .language .dropdown-menu .dropdown-item{              
    color: #666666;           
}
.switchContainer .nav-link.active{      
    background: #fd0c0c;       
    color: #ffffff;          

}            
.switchContainer .nav-link{          
    background: #ddd;     
    color: #ffffff;             
}
#popup_update_password .customForm .form-group .input-group-prepend .input-group-text,
#myProfile .form-group.input-group .input-group-prepend .input-group-text,
#signup .form-group.input-group .input-group-prepend .input-group-text{
    background: #ddd !important;
    border-color: #ddd; 
}
        
#ui_login .form-group.input-group  div.error{
    color: #ff0000;
}

/*keep this*/
.error{
    color: #ff0000;
}

#linemanagers_form .dataTables_filter input,
#instructors_form .dataTables_filter input,
#masteradmin_form .dataTables_filter input{
    background: #3e3e3e;
}
#linemanagers_form .dataTables_length select,
#instructors_form .dataTables_length select,
#masteradmin_form .dataTables_length select{
    color: #666666;
    background: #3e3e3e !important;
}
#linemanagers_form .dataTables_length label,
#instructors_form .dataTables_length label,
#masteradmin_form .dataTables_length label{
    color: #ffffff;
}

#cke_reply_body .cke_top,#cke_reply_body  .cke_bottom,
#cke_message_text .cke_top,#cke_message_text .cke_bottom{
    background: #3e3e3e !important;
    border: 0px solid #ddd !important;
}
.dataTables_paginate .paginate_button a{
    color: #ffffff;
}
.dataTables_paginate .paginate_button.active a{
    color: #fd0c0c;
}
.modal .modal-body div.dataTables_wrapper div.dataTables_info{
    color: #ffffff;
}
.message .messageFooter .fileImgField .input-group-prepend .input-group-text{
    border: 1px solid #ddd;
}
.messageWrapper .message .attachment .fileImgField .filedata{
    border: 1px solid #ddd;
}
.messageWrapper .message .attachment .fileImgField .thumb-icons span:before{
    color: #ffffff !important;
}
.messageWrapper .message .attachment .fileImgField .input-group span.fa{
    border: 1px solid #ddd;
}


#specificteam_form .dataTables_filter input,
#linemanagers_form .dataTables_filter input,
#instructors_form .dataTables_filter input,
#masteradmin_form .dataTables_filter input{
    background: #3e3e3e;
}
#specificteam_form .dataTables_length select,
#linemanagers_form .dataTables_length select,
#instructors_form .dataTables_length select,
#masteradmin_form .dataTables_length select{
    color: #666666;
    background: #3e3e3e !important;
}
#specificteam_form .dataTables_length label,
#linemanagers_form .dataTables_length label,
#instructors_form .dataTables_length label,
#masteradmin_form .dataTables_length label{
    color: #ffffff;
}

#cke_reply_body .cke_top,#cke_reply_body  .cke_bottom,
#cke_message_text .cke_top,#cke_message_text .cke_bottom{
    background: #3e3e3e !important;
    border: 0px solid #ddd !important;
}

.dataTables_filter input {
    color: #ffffff;
}
.dataTables_paginate  a,
.dataTables_paginate .paginate_button a{
    color: #ffffff;
}
.dataTables_paginate .paginate_button.active a{
    color: #fd0c0c !important;
}
.modal .modal-body div.dataTables_wrapper div.dataTables_info{
    color: #ffffff;
}

.message .messageFooter .fileImgField .input-group-prepend .input-group-text{
    border: 1px solid #ddd;
}
.messageWrapper .message .attachment .fileImgField .filedata{
    border: 1px solid #ddd;
}
.messageWrapper .message .attachment .fileImgField .thumb-icons span:before{
    color: #ffffff !important;
}
.messageWrapper .message .attachment .fileImgField .input-group span.fa{
    border: 1px solid #ddd;
}
.calenderWrapper .fc-day.fc-future{
    background: #e5e5e5;
}

.calenderWrapper .fc-today{
    background-color: #000000 !important;
}
.calenderWrapper   .fc-state-default{
    background: #3e3e3e !important;
    color: #666666 !important;
}
.fc-toolbar .fc-state-active{
    color: #ffffff  !important;
}

.fc-state-highlight > div > div.fc-day-number{
    background-color: #fd0c0c !important;
}
.calenderWrapper  .fc-state-active{
    background: #fd0c0c !important;
    color: #666666;
}
td.fc-today {
    background: #ffffff !important;
}
.fc-first th{  
    color: #ffffff;
}
.fc-event-inner { 
    color: #ffffff !important;
}
.calenderWrapper .fc-toolbar h2 {
    color: #333333 !important;
    background: #fcfcfc;
}
.fc-border-separate tr.fc-last th{
    border-color: #ddd;
}

.fc-day-header{
    color: #ffffff;
}

#dashboard #profile .profileInfo .name{
    color: #ffffff;
}
#dashboard #profile .profileInfo .designation{
    color: #ffffff;
}
#announcementWrapper .announcement li .annTitle{
    color: #333333}
.chartLabelWrap p a{
    color: #333333}

#marketingCalender .nav-tabs .nav-link,
#expiringCertificateWrapper .nav-tabs .nav-link,
#myprogress .nav-tabs .nav-link{
    background-color: #3e3e3e;
    color: #ffffff;
}
#marketingCalender .nav-tabs .nav-link.active,
#expiringCertificateWrapper .nav-tabs .nav-link.active,
#myprogress .nav-tabs .nav-link.active{
    color: #ffffff;
    background-color: #fd0c0c;
    border-color: #fd0c0c;
}
.link{
    color:#333333    border-right:1px solid #ffffff;
}
#dashboard #marketingCalender .ei-name{
    color: #333333}
#dashboard #marketingCalender  .ei-event{
    background: #000000;
}

#dashboard #marketingCalender .marketing-calendar-info-icon{
    color: #fd0c0c;
}
#dashboard #marketingCalender .ei-event .ei-date .ei-day, 
#dashboard #marketingCalender .ei-event2 .ei-date .ei-day{
    color: #333333    background: #3e3e3e;
}

#dashboard #marketingCalender #ei-event-2 h2,
#dashboard #marketingCalender #ei-event h2,
#dashboard #marketingCalender #marketing-calendar-events h2{
    color: #fd0c0c;
}
#dashboard #marketingCalender  .ei-nav-container i,
#dashboard #marketingCalender  .ei-nav-container-2 i,
#dashboard #marketingCalender .ei-nav-container-mcal i{
    background: #3e3e3e;
}
#dashboard  #leaderBoard #leader_board_dropdown{
    border: 1px solid #ddd;
    color: #666666;
}
#prev_next_team_completion .nextarrow,
#prev_next_leaderboard .nextarrow{
    background:#fd0c0c;
    color: #ffffff;
}
#prev_next_team_completion .pervarrow,
#prev_next_leaderboard .pervarrow{
    background: #fd0c0c;
    color: #ffffff;
}
.carousel-control-next-icon{
    color: #ffffff !important;
}
.calenderWrapper .fc-next-button .fc-icon,
.calenderWrapper .fc-prev-button .fc-icon{
    color: #333333}
.calenderWrapper .fc-day-grid-event .fc-time {
    color: #ffffff;
}
.sliderWrapper .title{
    color: #333333}
.sliderWrapper .title .viewAll{
    color: #333333}

.categorybg {
    border: 1px solid #ddd !important;
}
.categorybg i {
    color: #ffffff !important;
    background: #3e3e3e !important;
}
.sliderWrapper .slick-slide.slick-active.active-slide{
    border: 2px solid #fd0c0c;
}
#slider-animation .carousel-control-prev{
    background: #3e3e3e;
} 
#kr-modal .modal-dialog .modal-header .string-limit-name{
    color: #333333}
#kr-modal .modal-dialog .modal-body .string-limit-name{
    color: #ffffff;
}
#kr-modal .modal-dialog .modal-body .date-time-wrap-2{
    border-top: 1px solid #ddd;
}

@media (max-width: 767px){
     #dashboard #profile .profileInfo{
        background: #fd0c0c;
    }
}
/*End 27-11-2019 Dashboard UI Issue****************************************************************************************/

/*start 28-11-2019 Dashboard UI Issue****************************************************************************************/
#kr-modal .datewrapbox{
    border-right: 1px solid #ddd;
}

.custmBtnPrimary:hover{
    color: #ffffff !important;
}
.custmBtnDefault{
    color: #ffffff !important;
}
.slick-next:before{
    color: #ffffff !important;
}
.slick-prev:before{
    color: #ffffff !important;
}
.prev_nextArrow .pervarrow{
    background: #fd0c0c;
    color: #ffffff;
}
.prev_nextArrow .nextarrow {
    background: #fd0c0c;
    color: #ffffff;
}
@media (max-width:576px){
    .calenderWrapper .fc-agendaDay-button.fc-button.fc-state-default.fc-corner-right{
        color: #ffffff !important;
    }
  
}

#knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-body ul.card-icons-wrapper li.card-icon.box-wrap{
    background: #3e3e3e;
}

#knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-border{border:5px solid #ddd;}
#knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-border.borderR_none{border-right:none;}

#knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-body p{color: #333333;}

#my-passport-ui .cardContent .form-group .form-control{
    background: #3e3e3e;
    color: #333333    border: 1px solid #ddd;
}

.certificate-wrapper .certificate-tabs .nav-link.active:after{border-top: 12px solid #fd0c0c;}
#badges-leaderboard .badges-wrapper .badges-header-wrapper .badges-title,
#badges-leaderboard .leaderBoard-wrapper .leaderBoard-header-wrapper .leaderBoard-title {color:#333333;}
#badges-leaderboard .badges-wrapper .badges-header-wrapper .badges-status-box ul.badges-status-list{
    color: #333333;
}
#badges-leaderboard .badges-wrapper .badges-header-wrapper .badges-status-box ul.badges-status-list li:not(:last-child):after {border-right: 1px solid #ddd;}
#badges-leaderboard .badges-wrapper .badge-card-wrapper{border-top: 1px solid #ddd;}
#badges-leaderboard .badges-wrapper .badge-card-wrapper:last-child{border-bottom: 1px solid #ddd;}
#badges-leaderboard .badges-wrapper .badge-card-wrapper .v-line-right:after{    
    border-right: 1px solid #ddd;
}

#badges-leaderboard .badges-wrapper .badge-card-wrapper .badge-details .badge-details-title{color:#333333}
// .fa-star{color: ;}
#badges-leaderboard .badges-wrapper .badge-card-wrapper .badge-desc-title{color:#333333}
// #badges-leaderboard .badges-wrapper .badge-card-wrapper .badge-desc-wrapper p{color:;}

#badges-leaderboard .leaderBoard-wrapper .leader-card-row .leader-card .leader-rank{background:#000000;color:#ffffff;}
p .read-more, p .read-less{color:#fd0c0c;}

.externalCert-box-wrapper .externalCert-box-title .externalCert-box-header-text{
    color: #333333    color: #333333}


.externalCert-box-wrapper .externalCert-box-content .form-control{border:1px solid #ddd;}
.externalCert-box-wrapper .externalCert-box-content .form-control,
.externalCert-box-wrapper .externalCert-box-content label{color:#666666;}
.externalCert-box-wrapper .externalCert-box-content .submit-btn{background:#fd0c0c;color:#ffffff;}
.externalCert-box-wrapper .externalCert-box-content .cancel-btn{background:#525252;color:#ffffff;}

.certificate-right-button-wrapper .transcripts-dropdown .selection-dropdown #close_chooser,
.certificate-wrapper .transcripts-dropdown .close_chooser{
    background: #525252;
}
#my-passport-ui .leaderBCardContent,
#my-passport-ui .badgesCardcontent,
#my-passport-ui .cardContent{
    background: #fcfcfc;
}
#badges-leaderboard .badges-wrapper .badges-header-wrapper .badges-status-box ul.badges-status-list li .fa{
    color: #fd0c0c;
}
#my-badges .badge-card-body .badge-details i {
    color: #fd0c0c;
}
#my-badges .badge-counts .badge-count-card i {
    color: #fd0c0c;               
}
.certificate-wrapper .btn-clear-filter {
    background: #525252;          
}

.dataTables_filter input,
.certificate-wrapper .dataTables_filter input{
    color: #333333 !important;
}

/*need to add this*/
.certificate-wrapper .dataTables_length,
.dataTables_wrapper .dataTables_length{
    color: #333333;
} 

// .certificate-wrapper select {
//     color: ;
// }
.paginate_button.current {
    color: #fd0c0c !important;
}
.certificate-wrapper .certificate-tabs .nav-link.active {
    background: #fd0c0c ;position: relative;
    color: #ffffff !important;
} 

.certificate-wrapper .status-dropdown-btn:focus, .certificate-wrapper .status-dropdown-btn:hover,
#leaderBoard .leaderboard-dropdown-btn:hover{
    background: #fd0c0c;
    color: #ffffff !important;
}
.certificate-wrapper .dropdown-menu .dropdown-item,
#leaderBoard .dropdown-menu .dropdown-item{
    color: #333333;
}
.certificate-wrapper .dropdown-item.active, 
.certificate-wrapper .dropdown-menu .dropdown-item:hover,
#leaderBoard .dropdown-menu .dropdown-item:hover,
#leaderBoard .dropdown-menu .dropdown-item.active
{
    background: #fd0c0c !important;
}

.certificate-wrapper .selection-dropdown .custom-control {
    color: #333333;
}
.user-details-wrapper .edit-icon {
    background: #fd0c0c;
}
.user-details-wrapper .edit-icon i{
    color: #ffffff;
}
.user-details .user-detail {
    color: #333333;
}
.filter-Wrap .filter-card .input-group .input-group-prepend .input-group-text,
.filterWrap .filter-card .input-group .input-group-prepend .input-group-text{
    color: #333333;
    background: #ddd !important;
    border: 1px solid #ddd;
}
#my-passport-ui #date-dropdown .form-group .input-group-prepend .input-group-text{
    background: #525252;
    color: #333333;  
}
#my-passport-ui .courseGraph .form-group .input-group-prepend .input-group-text {
    color: #333333;
    background: #3e3e3e;
    border: 1px solid #ddd;
}
#my-passport-ui .courseGraph .calenderInputWrap .form-group .input-group-prepend .input-group-text {
    color: #333333;
    border: 1px solid #ddd;
}


.leaderBWrapper .leaderboard-details .leader-rank{
    background: #000000;
    color: #ffffff;
}
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item.active span,
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item:hover span{
    color: #ffffff !important;
}

.calenderWrapper .singleCalender h6{
    color: #333333}

#myProfile .fileUpload #btn_change_picture{
    background: #ffffff;
    color: #fd0c0c;
}

html  #topHeader.sticky nav.navbar{
    background:#ffffff !important;
}

#dashboardWig .custom-control{
    background: #3e3e3e;
}

#linemanager_myteam_ui #linemanagermyteam_wrapper .card{
    background:#525252;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-name{
    color:#333333;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status{
    border-top:1px solid #ddd;
    border-bottom:1px solid #ddd;
}

#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .container .status-box{
    color:#808080;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status  .status-circle{
    color:#ffffff;

}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .not-started .status-circle{
    background:#ff0000;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .in-progress .status-circle{
    background:#fab51c;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .completed .status-circle{
    background:#008000;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .not-completed .status-circle{
    background:#808080;
}

#linemanager_myteam_ui #linemanagermyteam_wrapper .card.member-card .message-wrapper{
    background: #fd0c0c;
}
.btn-details, .go-btn, .download-csv-btn{
    background:#fd0c0c;
    color: #ffffff;
}
.filter .fa-filter{
    color:#333333;
}
.filter-card .titleFilter{
    color: #333333;
}

.lds-ellipsis div {
    background: #fd0c0c;
}
.brdcrumblink a{
    color: #333333;
}

#ui_whitetheme .inner-content .dataTables_wrapper .table thead th,
#ui_whitetheme .inner-content .dataTables_wrapper .table td{
    border-bottom: 1px solid #ddd;  
}


ul.tagit input[type="text"] {
    color: #333333 !important;
}

#topHeader .navbar-nav.navbar-left-panel .nav-item.active .nav-link:before{
    border-bottom: 5px solid #ffffff ;
}

#catlog .boxsavedfilter, #myLearning .boxsavedfilter, #ui_recommendations .boxsavedfilter{ border: 1px solid #ddd; }
.morelink{ color: #fd0c0c;}
.iframelaunch .session-section {
    background: #eeeeee;
}
.iframelaunch .titleforsession, #ui_whitetheme .iframelaunch .titleforsession {background: #eeeeee!important;}

.iframelaunch .commonAccordionContent .subscriptionCard, .iframelaunch .timeline-icon {
    background: #ffffff !important;
}

.iframelaunch .boxfiles{       border: 2px dotted #ddd;
                               background: #ffffff;
                              }


.carousel-lo .item .panel-info .generalInfo .title{  color:#333333 }
.titleforsession{ background:#000000; }
.rightborder{ border-right:1px solid #ddd; }
.boxincont a{ color: #333333 }

#catlog svg, #myLearning svg, #ui_recommendations svg {
    fill: #ffffff;
    stroke:#ffffff;
}
.module-img svg {
    fill: #333333 !important;
    stroke:#333333!important;
}   
   
#catlog .carousel-lo .sessionItem:hover .panel-thumbnail, #catlog .carousel-lo .sessionItem.active .panel-thumbnail, 
#myLearning .carousel-lo .sessionItem:hover .panel-thumbnail, #myLearning .carousel-lo .sessionItem.active .panel-thumbnail,
#ui_recommendations .carousel-lo .sessionItem:hover .panel-thumbnail, #ui_recommendations .carousel-lo .sessionItem.active .panel-thumbnail
{border: 3px solid #fd0c0c;}

#couponInformationModal .bg-white{ background: #eeeeee;}

@media  screen and (max-width: 812px) {
     .learningpathui .custmBtnPrimary {    color: #ffffff!important;} } 


@media  only screen 
and (min-device-width: 768px) 
and (max-device-width: 1024px) 
and (orientation: landscape) 
and (-webkit-min-device-pixel-ratio: 1) {
    #my-passport-ui .leaderBCardContent, #my-passport-ui .badgesCardcontent{
        background: #fcfcfc !important;
    }
    
    #my-passport-ui .leaderBCardContent, #my-passport-ui .badgesCardcontent,
    #my-passport-ui .cardContent {
        background: #fcfcfc;
    }
    }

#myModal .commonAccordion .card-body .card .card-header a{ background: #eeeeee;}

#catlog .commonAccordion .card {background: #eeeeee !important;}
#ui_whitetheme #new_report_render .filter-card select{
    border:1px solid #ddd !important;
}


/*Start 26-12-2019**************************************************************/
#ui_whitetheme .modal .modal-body .custom-checkbox .custom-control-label:before{
    border: 1px solid #ddd;
}
#ui_whitetheme .dataTables_filter label input{
    border: 1px solid #ddd;
}
#course_requests_ui .table .custom-control-label::before{
    border-color: #ddd;
}

#course_requests_ui .custom-checkbox .custom-control-input:checked~.custom-control-label::after,
#course_requests_ui .table .custom-checkbox .custom-control-input:checked~.custom-control-label::after{
    border: solid #fd0c0c;
}


.messageWrapper .composeMail .bootstrap-tagsinput .tag{
    background-color: #3e3e3e;
    border: 1px solid #ddd;   
}

.messageWrapper .messageBlock .custom-checkbox .custom-control-input:checked~.custom-control-label::after{
    border: solid #ffffff;
}
.custom-control-label::before{
    background: #3e3e3e;
    border: #ddd solid 1px;
}
.custom-control-input:checked~.custom-control-label::before{
    background-color: #3e3e3e;
    border: #ddd solid 1px;

}

.filterWrap .filter-card .custom-checkbox .custom-control-input:checked~.custom-control-label::after{
    border: solid #fd0c0c;
}

.filterWrap .filter-card .custom-checkbox .custom-control-input:disabled:checked~.custom-control-label::before {
    background-color: #3e3e3e;
}
.paginate_button.active a{
    color: #fd0c0c;
}
.btn:hover{
    color: #ffffff;
}
#reports_ui #report_name{
    background-color: #3e3e3e !important;
}
#topHeader .navbar-collapse{
    background: #fd0c0c;
}
#topHeader .navbar-left-panel .nav-item a{
    color: #ffffff ;
}
#topHeader .navbar-left-panel .nav-item.active > a span{
    color: #ffffff ;
}

.bg-black{background:#ddd;}

.info-box-wrapper .info-box-title .info-header-text{color: #ffffff;}
.input-search{
    background: #ddd;
    color: #ffffff;
}

.input-search:focus{background-color:#ddd;color:#ffffff;}

.question-mark-icon{
    background:#fd0c0c;
    color:#000000;
}

.v-line{border-right: 1px solid #ddd;}
.h-line:after{background: #ddd;}
.filter-card{
    background:#525252;
}
.filter-card input[type="text"],.filter-card input[type="email"],
.filter-card select{background-color:#ddd !important;border:none;}
.filter-card input[type="text"],
.filter-card input[type="email"], 
.filter-card select,
input::-webkit-input-placeholder, 
input::placeholder,
// .filter-card select:focus{color: !important;} 
.filter-card label{
    color:#ffffff;
}
// .or-text{color:;}
.standard-reports-btn{color: #ffffff;}
.standard-reports-btn:hover{color:#ffffff;}
.reports-subheading{color:#fd0c0c;}

.dataTables_wrapper .dataTables_length select,
#courses-report-table-wrapper select {
   background-color: #3e3e3e !important;
    // color: ;
}

.inner-content h5 {
    color: #333333;
}
.certificate-wrapper .leaderBoard-wrapper select{
    background-color: #3e3e3e !important;
}

.btn-apply-filter,.btn-clear-filter,.btn-apply-filter:hover,.btn-clear-filter:hover{color: #ffffff;}
.btn-apply-filter {background: #fd0c0c;}
.radio-custom, .radio-custom-label {color: #666666;}
.radio-custom:checked + .radio-custom-label:before{background:#fd0c0c;}
.radio-custom + .radio-custom-label:before {
    background: #3e3e3e;
    border: 1px solid #ddd;
}

.radio-custom:checked + .radio-custom-label:before {
    color: #ffffff;
}

#reports_ui .dropdown-item, #course_requests_ui .dropdown-item{color:#ffffff !important}


/*Start:- Apsara 31-12-2019**************************************************************/
#topHeader .navbar-collapse.rmBgClrHeader .navbar-nav.navbar-left-panel .nav-item.active .nav-link:before{
    border-bottom: 5px solid #fd0c0c;
}
#topHeader .navbar-collapse.rmBgClrHeader{
    background: #ffffff !important;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}
#topHeader  .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item.active a{
    color: #fd0c0c;
}
#topHeader  .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item a{
    color:#333333;
}
#topHeader  .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item a:hover {
    color: #fd0c0c;
}

#topHeader  .navbar-collapse.rmBgClrHeader  .navbar-left-panel .nav-item.active > a span {
    color: #fd0c0c; 
}
.filter-card h4{
    color: #333333;
}


#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .switchContainer .nav-link{
    background: #212121;
}
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .switchContainer .nav-link.active{
    background:  #fd0c0c}

.messageWrapper  .composeMail .bootstrap-tagsinput input{
    color: #333333;
} 

/*start:- 1-12-2019 Internal css of myLearning**************************************************************/
#catalogSort .active{
    background-color: #fd0c0c;
    color: #ffffff !important;
}

#catlog .table-bordered td, #catlog .table-bordered th{
    color: #333333}

#searchpop .search_helper_global{
    color: #fd0c0c;
}
#search_table_wrapper .table-bordered td,#search_table_wrapper .table-bordered th,
#search_table_wrapper .table td {
    border-bottom: 1px solid #ddd !important;
}
#search_table_wrapper #search_table a{
    color: #333333;
}
#search_table_wrapper #search_table td{
    color:#666666;
}

.dataTables_filter input{
    color: #666666;
}

#searchpop ul.tagit{
    background: #3e3e3e;
}

#searchpop  .searchBtnWrap button.close{
    background: #3e3e3e;
}

#reports_ui #new_report_render .select2-container--default .select2-selection--multiple,
#reports_ui #new_report_render .select2-container--default.select2-container--focus .select2-selection--multiple{
    border-color:#ddd;
}
#reports_ui select#report_name,
.course_requestsform select#action{
    background-color: #3e3e3e !important;
    color: #333333;
}

.forumWrapper .write_msg .input_msg_write {
    background: #525252;
}
.forumWrapper .type_msg{
    background: #525252;
}

.forumWrapper .iconList a{
    color: #666666;
}

.forumWrapper .outgoing_msg .name,
.forumWrapper .incoming_msg .name{
    color: #333333;
}

.forumWrapper  .outgoing_msg{
    border-bottom: 1px solid #ddd;
}
.forumWrapper  .outgoing_msg:last-child{
    border-bottom: 0px solid #ddd;
} 
#forum input.write_msg{
    color: #333333 !important;
}


@media  screen and (max-width: 767px){
    .dashboardfull .box h2, 
    .dashboardfull .inner-content h2,
    .learningpathui .inner-content h2,
    .learningpathui .box h2{
        color: #333333 !important;
    }
}

.form-control.error{
    color: #666666;
}


/*added to remove the scroll of the body*/
#view-detail-feedback .fname,
#view-detail-feedback .fdate{
    border-left: 2px solid #fd0c0c;
}


.accouncementWrapper .btnclose  span{
    background: #fd0c0c;
    color: #ffffff;
}


/*18-3-2020
 ie specific css*/
@media  screen and (-ms-high-contrast: active), (-ms-high-contrast: none){

    .messageWrapper .messageBlock .card .card-body .fa.fa-chevron-right{
        background: #fd0c0c;
    }
  
    #topHeader .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item a{
        color:#333333 !important;
    }
    #topHeader .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item.active a,
    #topHeader  .navbar-collapse.rmBgClrHeader  .navbar-left-panel .nav-item.active > a span {
        color: #fd0c0c !important; 
    }

    #topHeader .navbar-left-panel .nav-item a{
        color: #ffffff !important; }
    .queryWrapper .messageBlock :checked.custom-control-input ~ .custom-control-label::before,
    #email_view_form .messageWrapper .messageBlock :checked.custom-control-input ~ .custom-control-label::before{
        background-color: #fd0c0c;
        border-color: #fd0c0c;
    }

    .slick-prev,.slick-next{
        background:#fd0c0c !important;
    }

    .checkmark::after{
        color: #fd0c0c !important;
    }
    .checkmark{
        border:1px solid #ddd !important;
    }
    #my-passport-ui .custom-control-label::before{
        border:1px solid #ddd !important;
    }
    #course_requests_ui .custom-checkbox .custom-control-label{
        color: #fd0c0c !important;
    }
    #course_requests_ui .table .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    #course_requests_ui .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{

        color: #fd0c0c !important;
    }
 
    #course_requests_ui .table .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    #course_requests_ui .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    .modal .modal-body .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    #dashboardWig .widget .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    .instantActionBtn .custom-radio .custom-control-input:checked ~ .custom-control-label::after,
    .filterWrap .filter-card .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
     color: #fd0c0c !important;
    }


    .messageWrapper .messageBlock :checked.custom-control.custom-input ~ .custom-control-label::before{
        background-color: #fd0c0c;
    }
    .messageWrapper .messageBlock :checked.custom-control.custom-input~.custom-control-label::before{
        background-color: #fd0c0c !important;
    }
    
      .modal .modal-body .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
        color:#fd0c0c !important;
    }

    .modal .modal-body .custom-checkbox .custom-control-label{
        color:#fd0c0c !important;
    }

    #timezone_form #dashboardWig .widget .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
        color: #fd0c0c !important;
    }
  
    #timezone_form #dashboardWig .custom-control .custom-control-label{
        color: #333333 !important;
    }
    #timezone_form #dashboardWig .custom-control .custom-control-label span{
        color: #333333 !important;
    }
    
    .instantActionBtn :checked.custom-control-input ~ .custom-control-label::before{
        background: #fd0c0c !important;
    }
    
    .commonStyleitemWrapper.active-slide{
        border:  2px solid #fd0c0c !important;
    }
}

/*18-3-2020 ie specific css*/


.anndescriptionWrapper img{
    border: 1px solid #fcfcfc;
}
.vs-video-description-drop .fa-times::before { color:#ffffff !important; }

/* 16-june*/
.assessment .btn-orange, .assessment .btnReset, .assessment .uploadBtn, 
.assessment-instruction-page .btn-save, .survey .btnReset{
    background-color: #fd0c0c !important;
    border-color: #fd0c0c !important;
    color: #ffffff !important;
}
.assessment .btn-orange:hover, .assessment .btnReset:hover, .assessment .uploadBtn:hover,
.assessment-instruction-page .btn-save:hover, .survey .btnReset:hover{
    background-color: #fd0c0c !important;
    border-color: #fd0c0c !important;
    color: #ffffff !important;
}
.survey .btn-transperant-blue, .survey .btn-white, .survey .btn-transperant, 
.survey .btn-file, .survey .btn-white-catalog, .survey .btn-default, 
.survey .btn-tran, .assessment .btn-transperant-blue, .assessment .btn-white, .assessment .btn-transperant, 
.assessment .btn-file, .assessment .btn-white-catalog, .assessment .btn-default, .assessment .btn-tran{
    background-color: #ffffff !important;
    border-color: #fd0c0c !important;
    color: #fd0c0c !important;
}
.survey .btn-transperant-blue:hover, .survey .btn-white:hover, .survey .btn-transperant:hover, 
.survey .btn-file:hover, .survey .btn-white-catalog:hover, .survey .btn-default:hover, 
.survey .btn-tran:hover, .assessment .btn-transperant-blue:hover, .assessment .btn-white:hover, .assessment .btn-transperant:hover, 
.assessment .btn-file:hover, .assessment .btn-white-catalog:hover, .assessment .btn-default:hover, .assessment .btn-tran:hover{
    background-color: #fd0c0c !important;
    border-color: #fd0c0c !important;
    color: #ffffff !important;
}

       #dashboard #announcementWrapper .alert-danger, #dashboard #expiringCertificateWrapper .alert-danger,
       #dashboard #marketingCalender .ei-events-container-mcal .alert-danger, #dashboard .chartWrap .alert-danger, 
       #dashboard .leaderBWrapper .alert-danger, #dashboad #leaderBoard .alert-danger, #dashboard .cardContent.equalHeightBlock .alert-danger,
       #dashboard #marketingCalender .alert-danger, #dashboard #myprogress .alert-danger{
        background: #fd0c0c;
        color: #ffffff;
        border-color: #fd0c0c;
    }
 
  
    
.redstatus .progress-bar{background: #ff0000;}
.greenstatus .progress-bar{background: #008000;}
.yellowstatus .progress-bar{background: #fab51c;}
.greystatus .progress-bar{background: #808080;}
/*End css for ticket-4771 added by Prashant*/

</style>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
 <link href="css/lity2.css" rel="stylesheet"/>
<script src="js/lity2.js"></script>

        <link rel="stylesheet" type="text/css" href="./Course Details - Abara LMS_files/main.css">
        <link rel="stylesheet" type="text/css" href="./Course Details - Abara LMS_files/development.css">
        <!--font awesome -->
        
        <script type="text/javascript" async="" src="./Course Details - Abara LMS_files/analytics.js.download"></script>
		<script type="text/javascript" src="./Course Details - Abara LMS_files/jQuery-3.3.1.min.js.download"></script>
<script type="text/javascript" src="./Course Details - Abara LMS_files/bootstrap.min.js.download"></script>
<script type="text/javascript" src="./Course Details - Abara LMS_files/simplebar.js.download"></script>
	
   
    <!-- Here include only branding_color file for add branding added by Navnath B. Kamthane in branch mantis-4942 (8th October -->
    <style>
#ui_whitetheme  .commonAccordion .card-body .card .card-header a{
      background: #ffffff;
}
#ui_whitetheme  #my-passport-ui #date-dropdown .form-group .input-group-prepend .input-group-text,
#ui_whitetheme .filterWrap .filter-card .form-control,
#ui_whitetheme .certificates-dropdown .dropdown-menu input,
#ui_whitetheme .inner-content .form-control, #ui_whitetheme .tab-pane .absolute .inner-content .form-control
{
  border: 1px solid #ddd;
}

#ui_whitetheme .vs-drop-details .input-group-text{  border-color: #ddd !important;}

 html .dashboardfull #topHeader nav.navbar{
  background-color: #ffffff !important;
}
 
#ui_whitetheme #cke_reply_body .cke_top,
#ui_whitetheme #cke_reply_body .cke_bottom,
#ui_whitetheme #cke_message_text .cke_top,
#ui_whitetheme #cke_message_text .cke_bottom{
  background-color: #eeeeee !important;
}
/*need to keep this*/

#ui_whitetheme #ui_login .lowerBtnWrap ul li p{
  color: #666666 !important;
}

#ui_whitetheme .messageWrapper .composeMail .dropData,
#ui_whitetheme .calenderWrapper .fc-ltr .fc-axis,
#ui_whitetheme #dashboard #marketingCalender .ei-event{
    background: #e5e5e5;
}

#ui_whitetheme #myModal hr {border-color: #ddd;}

#ui_whitetheme #kr-modal .modal-dialog .modal-body .string-limit-name,
#ui_whitetheme #ui_SetPassword .formtitle,
#ui_whitetheme #signup .formtitle,
#ui_whitetheme #ui_login .formtitle,
#ui_whitetheme #ui_resetPassword .formtitle,
#ui_whitetheme #ui_activateLearner .formtitle,
#ui_whitetheme #ui_login .leftWrapper .title,
#ui_whitetheme #ui_login .lowerBtnWrap .antBlock .title,
#ui_whitetheme #signup .formtitle,
#ui_whitetheme #ui_login .formtitle,
#ui_whitetheme #ui_resetPassword .formtitle,
#ui_whitetheme #ui_activateLearner .formtitle,
#ui_whitetheme .modal .modal-body .subHeading,
#ui_whitetheme .queryWrapper .messageBlock .card h5.name,
#ui_whitetheme .messageWrapper .messageBlock .card h5.name,
#ui_whitetheme .modal .modal-body .mainHeading,
#ui_whitetheme  .currentStatus{
    color: #333333}
#ui_whitetheme .btn-filter,
#ui_whitetheme .report-search-wrapper .reportSelection-dropdown-btn,
#ui_whitetheme .request-dropdown-btn,
#ui_whitetheme .dataTables_paginate .paginate_button a,
#ui_whitetheme .dataTables_paginate  a,
#ui_whitetheme .modal .modal-body div.dataTables_wrapper div.dataTables_info,
#ui_whitetheme #specificteam_form .dataTables_length label, 
#ui_whitetheme #linemanagers_form .dataTables_length label, 
#ui_whitetheme #instructors_form .dataTables_length label,
#ui_whitetheme #masteradmin_form .dataTables_length label,
#ui_whitetheme .modal .modal-body .basicInfo{
  color: #666666;
}
#ui_whitetheme .certificate-wrapper .btn-ext-certificate,
#ui_whitetheme .certificate-wrapper .btn-filter,
#ui_whitetheme .certificate-wrapper .status-dropdown-btn{
 color: #666666!important; 
}

#ui_whitetheme #dashboard #marketingCalender .ei-nav-container i, 
#ui_whitetheme #dashboard #marketingCalender .ei-nav-container-2 i,
#ui_whitetheme #dashboard #marketingCalender .ei-nav-container-mcal i{
  background: #fd0c0c;
}
#ui_whitetheme .calenderWrapper .fc-day-header{
  background: #e5e5e5 !important;
  color:#666666;
}

#ui_whitetheme .inner-content .table.certificate-table thead th,
#ui_whitetheme  .inner-content .table.certificate-table td, #ui_whitetheme .commonAccordion .card-header,
#ui_whitetheme .tab-pane .absolute .inner-content .table th, 
#ui_whitetheme .inner-content .table th, #ui_whitetheme .tab-pane .absolute .inner-content .table td,
#ui_whitetheme .inner-content .table td
{
  border-bottom: 1px solid #ddd;
}

#ui_whitetheme .modal .modal-body .table td{
    border-top: 1px solid #ddd;
}

#ui_whitetheme .checkmark{
    border: 1px solid #ddd;
}

#ui_whitetheme #knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-body ul.card-icons-wrapper li.card-icon.box-wrap{
    background: #3e3e3e;
}

#ui_whitetheme .messageWrapper .messageBlock .icon{
  color: #3e3e3e;
}
 
 #ui_whitetheme .radio-custom:checked + .radio-custom-label:before,
 #ui_whitetheme .instantActionBtn .custom-control-input:checked ~ .custom-control-label::before {
    background: #fd0c0c !important;
}

/*White Theme**************************************************************************************************/

/*Start Of Common Css*******************************************************************************************************/
body{
  background: #eeeeee;
  color: #666666 !important;
}

.border-right {
  border-right: 1px solid #ddd !important;
}

.dropdown-item:focus,
 .dropdown-item:hover,
.dropdown-item.active,
.dropdown-item:active {
    color: #ffffff !important;
    text-decoration: none;
    background-color: #fd0c0c !important;
}
.simplebar-scrollbar {
  background: #fd0c0c;
}


input[type=email]::-webkit-input-placeholder,
input[type=text]::-webkit-input-placeholder {
  /* Chrome/Opera/Safari */
  color: #666666;
}

input[type=email]::-moz-placeholder,
input[type=text]::-moz-placeholder {
  /* Firefox 19+ */
  color: #666666;
}

input[type=email]:-ms-input-placeholder,
input[type=text]:-ms-input-placeholder {
  /* IE 10+ */
  color: #666666;
}

input[type=email]:-moz-placeholder,
input[type=text]:-moz-placeholder {
  /* Firefox 18- */
  color: #666666;
}

.orSeprator:after {
  border-top: 1px solid #ddd;
}
.orSeprator:before {
  border-top: 1px solid #ddd;
}

.dropBtn span {
  color: #fd0c0c;
}

.dropState span {
  background: #ffffff;
}

.hr {
  border-top: 1px solid #ddd;
}

.custmBtnDefault {
  background: #3e3e3e !important;
  color: #ffffff;
}

.custmBtnPrimary {
  background: #f15a24 !important;
  color: #ffffff !important;
}

.custmBtnSecondary {
  background: #212121;
  color: #ffffff;
}

.bg-primaryColor {
  background: #fd0c0c;
}

.bg-lightBlack {
  background: #212121;
}

.btn-black:hover {
  color: #ffffff;
}

#style-3::-moz-scrollbar-track {
  background-color: #212121;
}

#style-3::-webkit-scrollbar-thumb {
  background-color: #000000;
}

.pagination .page-item.active .page-link {
  color: #fd0c0c;
}
.pagination .page-item .page-link {
  color: #ffffff;
}

.orgbg {
  background: #fd0c0c !important;
}

.list li span.fa {
  color: #fd0c0c;
}

.cardContent .form-group label {
  color: #666666;
}
/*need to keep */
.cardContent .form-group .form-control {
  background-color: #ddd !important;
  color: #666666;
  border-color: #ddd;
}
/*need to keep */

.subHeading h5, .cardContent h5 {
  color:#333333}

.cardBg {
  background: #eeeeee;
}
/* no need to add this*/
/*main {
  margin-top: 75px;
}*/

.orange {
  color: #fd0c0c !important;
}
.red {
  color: #ff0000;
}
.yellow {
  color: #fab51c;
}
.grey {
  color: #808080;
}

.green {
  color: #008000;
}
.white{
  color: #ffffff;
}
.searchClose {
  color: #eee;
}

/* Create a custom checkbox */
.checkmark {
       background-color: #eeeeee !important;
    border: 1px solid #ddd;
}
/* On mouse-over, add a grey background color */
.customCheckBoxWrap:hover input ~ .checkmark {
  background-color: #eeeeee  !important;
}

/* Style the checkmark/indicator */
.customCheckBoxWrap .checkmark:after {    border: solid #fd0c0c;}
/*End****************************************************************************************************************************/

/*Form Common CSS****************************************************************************************************************/
.customForm .form-group label {
  color: #666666 !important;
}
.customForm .form-group .form-control, .customForm .form-group .input-group .input-group-text {
  color: #666666;
  border-color: #ddd;
}

.customForm .form-group .input-group .search-arrow{
    border-right: 1px solid #ddd;
}

.customForm .form-group .form-control::-webkit-input-placeholder {
  color: #666666;
}

select.language {
  background-color: #fd0c0c !important;
}

.modal .modal-header .modal-title {  color: #333333}
.modal .modal-header button.close {
  color: #ffffff;
  background: #3e3e3e;
}

.modal .modal-body .basicInfo {  color: #ffffff;}

.modal .modal-body .basicInfo p.c_course_sts,
.modal .modal-body .basicInfo p:nth-child(2){    border-left: 1px solid #ddd;}
.modal .modal-body .subHeading {  color:  #333333;}
.modal .modal-body .mainHeading {  color: #333333;}
.modal .modal-body .custom-checkbox .custom-control-label {  color: #333333}
.modal .modal-body .custom-checkbox .custom-control-label:before {
  background: #eeeeee;}

.modal .modal-body .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after {
   border: solid #fd0c0c;
}
.modal .modal-body .custom-control-input:checked ~ .custom-control-label::before {
  color: #ffffff;
  background-color: #eeeeee;
}
.modal .modal-body .table td {
  color: #666666;
}
.modal .modal-body .table thead th {
  color: #333333}
.modal .modal-body .threeGriedTier {
  border-top: 1px solid #ddd;
}

.wrapBlock{
    border-bottom: 1px solid #ddd;
}

.cartWrapper .cartItem .cartItemDetail .title{
  color: #333333}

.cartWrapper .cartTotal .subTotal{
    color:  #333333}
.cartWrapper .cartTotal .totalPrice{
    color: #fd0c0c;
}

.cartWrapper .qty .count {
    color:  #333333    border: 1px solid #ddd;
}
.cartWrapper .qty .plus {
    background: #3e3e3e;
    }
.cartWrapper .qty .minus {
    background: #3e3e3e;
}
.cartWrapper .qty .minus:hover{
    background-color: #fd0c0c !important;
}
.cartWrapper .qty .plus:hover{
    background-color: #fd0c0c !important;
}
/*Prevent text selection*/

.cartWrapper .cartItemDetail .price p{
    color: #fd0c0c;
}

.cartWrapper .couponWrap .nextArrow{
    background: #fd0c0c !important;
  
 }
 .cartWrapper .couponWrap .percentIcon{
     color: #666666;
 }
 .cartWrapper .couponWrap .nextArrow .input-group-text{
   
    color:  #333333 }
/*End of the MOdal Css***********************************************************************************************************/

/*Start of the Topheader******************************************************************************************************/

#topHeader #searchBar {

  background: #ffffff;
 
  color: #333333;
 
}

#topHeader nav.navbar {
  background: #ffffff !important;
}
#topHeader .navbar-left-panel .nav-item.active > a span {
  color: #fd0c0c;
}
#topHeader .navbar-left-panel .nav-item a {
  color: #333333}
/*need to keep this*/
#topHeader .navbar-left-panel .nav-item a:hover {
   color: #ffffff ;
}

#topHeader .navbar-right-panel .nav-item .nav-link span.fa {
  color: #333333}

#topHeader .navbar-right-panel .nav-item .dropdown-toggle {
  background: #3e3e3e;
 
}

#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu {
  background: #3e3e3e;
  color: #666666;
  
}
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item {
 
  color: #666666;
 
}
/*need to keep this*/
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item:hover {
  background: #fd0c0c;
}
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item span {
  color: #eee;

}

#topHeader .navbar-toggler-icon {

    color: #ffffff;

}
/*End of the topHeader********************************************************************************************************/

/*Start of the pageheader***************************************************************************************************/

.pageHeader .filter .fa,
.quickAction li a span.fa{
  color: #333333;
  
}

.quickAction li.search{
  border-right: 1px solid #ddd;
}
.quickAction li.sort .dropdown-item{
    color: #666666;
   
}
.quickAction li.sort .dropdown-menu {
   
    background: #3e3e3e;
    color: #666666;
  
}
.quickAction li.search .form-group{
   
    background: #3e3e3e;
   
}
.quickAction li.search .fa-question-circle{

    color: #fd0c0c;
}
.quickAction li.search .form-group .input-group-text{
    background: #3e3e3e;
    border-color: transparent;
    color:  #333333}
.quickAction li.search .form-group .form-control{
    background: #3e3e3e;
    border-color: transparent;

}

  
.rightModalHeader{
    background: #3e3e3e;
}
.rightModalHeader h4{
    color: #333333;
}
    

.filter-Wrap .form-control{
  
  color: #666666;
}
.pageHeader h3 {
  color: #333333}

/*End of the page header**************************************************************************************************/

#myProfile .profileInfo {
  color:  #ffffff;
}

#myProfile .profilePic .fa{
    color: #fd0c0c;
    background: #ffffff;
}

/*End of theMy Profile ********************************************************************************************************/

/*login,signup,activate learner, forget password******************************************************************************/

#ui_SetPassword .form-group.input-group .input-group-prepend,
#ui_resetPassword .form-group.input-group .input-group-prepend,
#ui_activateLearner .form-group.input-group .input-group-prepend {
  background: #212121 !important;
}
#ui_SetPassword .form-group.input-group .input-group-prepend .input-group-text,
#ui_resetPassword .form-group.input-group .input-group-prepend .input-group-text, 
#ui_activateLearner .form-group.input-group .input-group-prepend .input-group-text {
  border-color: #ddd;
}

#ui_login .aboutContent {
    color: #666666;
}

#ui_login .leftWrapper .login_info_content_link .arrowbtn {
  color: #fd0c0c;
}

#ui_login .leftWrapper .login_info_content_link .bordclass {
  border-top: 1px solid #ddd;
}
#ui_login .leftWrapper .login_info_content_link .fa {
    color: #ffffff;
    background: #fd0c0c;
}
#ui_login .leftWrapper .title {
    color: #ffffff;
   }
#ui_login .leftWrapper .title .secondPart {
  color:  #ffffff;
}
#ui_login .leftWrapper .title .firstPart {
  color:  #ffffff;
}

#ui_login .lowerWrapper p {
  color: #666666;
}
#ui_login .lowerWrapper a {
  color: #fd0c0c;
}
#ui_login .lwbtnSection .custom-control-label, #ui_login .lwbtnSection a {
  color: #666666 !important;
}

#ui_login .lowerBtnWrap .antBlock .title {
  color:  #ffffff;
  background: #212121;
}

#ui_login .lowerBtnWrap .counter {
  background: #fd0c0c;
  color:  #ffffff;
}

#ui_login .lowerBtnWrap ul li span.date {
  color: #fd0c0c;
}
#ui_login .lowerBtnWrap ul li p {
  color: #666666;
}

#ui_login .form-group.input-group .input-group-prepend {
  background: #212121 !important;
}
#ui_login .form-group.input-group .input-group-prepend .input-group-text {
  border-color: #ddd;
}

#signup #language, #ui_login #language, #ui_resetPassword #language, #ui_activateLearner #language {
  color:  #ffffff;
}

#signup .socialMedia p, #ui_login .socialMedia p, #ui_resetPassword .socialMedia p, #ui_activateLearner .socialMedia p {
  color: #666666;
}
#ui_SetPassword .formtitle,
#signup .formtitle, #ui_login .formtitle,
#ui_resetPassword .formtitle, #ui_activateLearner .formtitle {
  color:  #ffffff;
}
#signup .customForm .form-group .form-control, #ui_login .customForm .form-group .form-control, #ui_resetPassword .customForm .form-group .form-control, #ui_activateLearner .customForm .form-group .form-control, #ui_SetPassword .customForm .form-group .form-control  {
  background: #212121 !important;}

#signup .simplebar-scrollbar {
  background: #fd0c0c;
}

.attachment .card {
  background: #525252;
}

.settingWrapper #splashPage .card-body .fa.fa-file-text {
  color:  #ffffff;
}
.settingWrapper #splashPage .card-body h5 {
   color: #ffffff;
}
.settingWrapper #systemDiagnostics h5 {
  color: #333333;
}

.settingWrapper #systemDiagnostics .card-body .fa.fa-file-text {
  color: #333333;
}
.settingWrapper #systemDiagnostics .card-body .cardWrap {
  background: #eeeeee;
  color: #666666;
}

#dateFormat .cardContent .custom-control {
  background: #eeeeee;
  color: #666666;
}

#ui_login .custom-control-input:checked ~ .custom-control-label::before,
#dashboardWig .widget .custom-control-input:checked ~ .custom-control-label::before {
  border-color: #ddd !important;
  background-color: #eeeeee !important;
}
/*need to keep this*/
#ui_login .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
  border: solid #fd0c0c;
}
/*need to keep this*/
/*need to keep this*/
#dashboardWig .widget .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
 border: solid #fd0c0c;
}
/*need to keep this*/

.instantActionBtn .custom-radio .custom-control-input:checked ~ .custom-control-label::after {
  border: solid #ffffff;
}

.instantActionBtn .custom-control-input:checked ~ .custom-control-label::before,
{  background: #3e3e3e !important;
}

#ui_login .custom-control {
  background: #3e3e3e;
}

#dashboardWig .custom-control .custom-control-label, #ui_login .custom-control .custom-control-label {
  color: #333333}

 #ui_login .custom-control .custom-control-label:before {
  background: #eeeeee;
}
/*Need to keep this change*/
/*Need to keep this change*/
#dashboardWig .custom-control .custom-control-label:before{
    background: #eeeeee;
    border-color: #ddd;
}

/*End of the Setting  **************************************************************************/
.accouncementWrapper .announcementTitle {
  color: #fd0c0c;
}

.accouncementWrapper .custom-file .custom-file-label { color: #666666;}

.messageWrapper .message .attachment .fileImgField .input-group span.fa {  color: #fd0c0c;}
.messageWrapper .message .messageBody a {  color: #666666;}
.messageWrapper .message .messageHeader .user-name {  color: #333333}
.messageWrapper .message .messageHeader .userProfile span.initialLetter {
  background: #fd0c0c;
  color: #ffffff;
}

.messageWrapper .composeMail .dropData {
  background: #3e3e3e;
  color: #666666;
}

.queryWrapper .messageBlock .custom-control-input:checked ~ .custom-control-label::before,
.messageWrapper .messageBlock .custom-control-input:checked ~ .custom-control-label::before {
  color:  #ffffff;
  border-color: #fd0c0c;
  background-color: #fd0c0c;
}
.queryWrapper .messageBlock .custom-checkbox .custom-control-label::before,
.messageWrapper .messageBlock .custom-checkbox .custom-control-label::before {
  background: #212121;
  color: #eee;
}

.messageWrapper .messageBlock .icon {
  color: #eee;
}
.queryWrapper .messageBlock .icon{
  color: #eee;
}

.queryWrapper .messageBlock .card.active ,
.messageWrapper .messageBlock .card.active {
  background: #ffffff;
}
.queryWrapper .messageBlock .card.active .card-body span img{
    color: #ffffff;
}

.queryWrapper .messageBlock .card.active:after,
.messageWrapper .messageBlock .card.active:after {
  background: #fd0c0c;
}
.queryWrapper .messageBlock .card.active:before,
.messageWrapper .messageBlock .card.active:before {
  color: #eee;
  background: #fd0c0c;

}

.queryWrapper .messageBlock .card h5.name,
.messageWrapper .messageBlock .card h5.name {
  color: #fd0c0c !important;
}
.messageWrapper .messageBlock .visited h5.name {
    color: #333333 !important;    
}
.messageWrapper .sendBoxMsg  .card h5.name{
    color: #333333}
.queryWrapper  .messageBlock .card .card-body span img,
.messageWrapper .messageBlock .card .card-body span img,
.queryWrapper  .messageBlock .card .card-body .fa.fa-chevron-right,
.messageWrapper .messageBlock .card .card-body .fa.fa-chevron-right {
  color: #ffffff;
}
.messageWrapper .messageBlock .card .nameInitialLetter {
  background: #fd0c0c;
  color: #ffffff;
}
.queryWrapper .moreShortInfo{
    border-top: 1px solid #ddd;
}
.queryWrapper .messageBlock .card .card-body .title{
    color:  #333333}

.queryWrapper .msgContent .subHeading .title .viewAll{
    border-left: 1px solid #ffffff;
}
.queryWrapper .msgContent .type_msg .input-group .write_msg{
  border-color: #ddd;
  color: #333333}
.queryWrapper .msgContent .type_msg .input-group-prepend .input-group-text{
    border-color: #ddd;
}
.queryWrapper .msgContent .type_msg .msg_send_btn{
    background: #fd0c0c;
    border-color: transparent;/*add this*/
    color: #ffffff;
}
.queryWrapper .msgContent .msg_history .received_withd_msg  {
    background: #212121;
    color: #666666;
}
.queryWrapper .msgContent .msg_history .sent_msg {
    background: #212121;
}
.queryWrapper .msgContent .msg_history .received_msg .name,
.queryWrapper .msgContent .msg_history .incoming_msg .name,
.queryWrapper .msgContent .msg_history .outgoing_msg .name{
    color: #333333}

.currentStatus .status{
    color: #ffffff;
}
.commonThumbnailSlider .carousel-item.active .panel-thumbnail {
  border: 3px solid #fd0c0c;
}
.commonThumbnailSlider .carousel-control-prev-icon {
    background: #fd0c0c;
        color: #ffffff;
}
.commonThumbnailSlider .carousel-control-next-icon {
  background: #fd0c0c;
}
.commonThumbnailSlider .carousel-item .panel-info .generalInfo {
  border-right: 1px solid #ddd;
}
.commonThumbnailSlider .carousel-item .panel-info .generalInfo .title {
  color: #ffffff;
}

.commonThumbnailSlider .carousel-item .panel-info .moreInfo span.fa {
  color: #fd0c0c;
}
.commonThumbnailSlider .carousel-item .panel-info .description .title {
  color: #666666;
}
.commonThumbnailSlider .carousel-item .panel-info .description .attempt {
  color: #ffffff;
}

#learningObjCarousel {
  border-top: 1px solid #ddd;
}

.modalCarousel {
  background-color: #ffffff;
  border-top: 1px solid #ddd;
}

.popContent .mainContent {
    background: #eeeeee !important;
}
.popContent .header {
  color:  #333333;
  background: #ffffff;
}
.popContent .header .leftPartheader .toggleBar {
  background: #fd0c0c;
  border-color: transparent;
  color:  #ffffff;
}
.popContent .header .popUpClose {
  color:  #ffffff;
  background: #fd0c0c;
}

#dashboard #profile .profileInfo h5{
  color:  #ffffff !important;
}
#dashboard #profile .quickLinks {
  background: #e5e5e5;
}

#dashboard .cardContent {
  background: #fcfcfc;
}
#dashboard .cardContent .viewAll {
  border-left: 1px solid  #ffffff;
  color: #333333}

#dashboard #upcomingCourse .overlay {
  color: #ffffff;
}

#dashboard #leaderBoard .leaderProfile .imgWrapper .countNo {
  background: #000000;
  color: #ffffff;
}
#dashboard #leaderBoard .leaderProfile .info .name {
  color: #ffffff;
}

.sliderWrapper .title {
  color: #eee;
}
.sliderWrapper .title .viewAll {
  border-left: 1px solid  #ffffff;
}
#announcementWrapper .announcement li {
  background: #e5e5e5;
}

.dashboardfull .scroller, .learningpathui .scroller{
  background-color: #ffffff;
}

.dashboardfull .box h2, .dashboardfull .inner-content h2, .learningpathui .inner-content h2, .learningpathui .box h2 {
  color:  #333333}
.dashboardfull .carousel-control-next, .dashboardfull .carousel-control-prev {
  background: #fd0c0c;
}
.dashboardfull .carousel-control-prev {
  background: #525252;
}
.carouseltab .nav-tabs .nav-link.active:after {
  border-bottom: 10px solid #fd0c0c;
}
#dashboard #profile .quickLinks {
  background: #e5e5e5;
}

#dashboard #upcomingCourse .overlay {
  color: #ffffff;
}

#dashboard #leaderBoard .leaderProfile .imgWrapper .countNo {
  background: #000000;
  color: #ffffff;
}
#dashboard #leaderBoard .leaderProfile .info .name {
  color: #ffffff;
}
.sliderWrapper .title {
  color: #eee;  
}
.sliderWrapper .title .viewAll {
  border-left: 1px solid  #ffffff;
}
#announcementWrapper .announcement li {
  background: #e5e5e5;
}

.dashboardfull .scroller, .learningpathui .scroller{
  background-color: #ffffff;
}

.dashboardfull .box h2, .dashboardfull .inner-content h2, .learningpathui .inner-content h2, .learningpathui .box h2 {
  color:  #333333}
.dashboardfull .carousel-control-next, .dashboardfull .carousel-control-prev {
  background: #fd0c0c;
}
.dashboardfull .carousel-control-prev {
  background: #525252;
}
.carouseltab .nav-tabs .nav-link.active:after {
  border-bottom: 10px solid #fd0c0c;
}

.keywords .btn-outline-secondary, .lpathbox .btn-outline-secondary {
  color: #666666;
  border-color: #ddd;
}
.keywords .btn-outline-secondary:hover, .lpathbox .btn-outline-secondary:hover, .lpathbox .btn-outline-secondary:active:focus,
.modal .btn-outline-secondary, .modal .btn-outline-secondary:hover
{
  color: #666666 !important;
  border-color: #ddd !important;
}

.progress {
  background-color: #525252;
}
.progress-bar {
  background-color:#fd0c0c;
}

.carouseltab .nav-tabs {
  border-top: 1px solid #ddd;
  background: #525252;
  border-bottom: 1px solid #ddd;
}
.carouseltab .nav-link {
  color: #666666;
}
.carouseltab .nav-link:hover, .carouseltab .nav-link.active {
  border-color: transparent;
  background:#fd0c0c;
   color: #ffffff;
}

/*Need to keep this*/
.tab-pane .absolute .inner-content h5,
.inner-content h5 {
  color: #ffffff;
}

.inner-content .table,
.tab-pane .absolute .inner-content .table {
  color: #666666;
}
.inner-content .table thead th,
.tab-pane .absolute .inner-content .table thead th {
  border-bottom: 1px solid #3e3e3e;
  color: #333333;
}
.tab-pane .absolute .inner-content .table th,
.inner-content .table th,
.tab-pane .absolute .inner-content .table td,
.inner-content .table td{
  border-bottom: 1px solid #3e3e3e;
}
.inner-content .form-control,
.tab-pane .absolute .inner-content .form-control, .input-group-text {
  background: #525252;
  border: 1px solid #3e3e3e;
  color: #666666;
}
/*End Need to keep this*/
.calenderWrapper .nav-tabs .nav-link.active{
  color: #ffffff;
  background-color: #fd0c0c;
  border-color: #fd0c0c; 
}
.calenderWrapper .nav-link{
background-color: #3e3e3e;
color: #ffffff;
}
.calenderWrapper{
background: #fcfcfc;
 }

#announcementWrapper .owl-theme .owl-nav button{
        background: #fd0c0c;
       
}   
      .carousel-lo .item:hover .panel-thumbnail,  .carousel-lo .item.active .panel-thumbnail, #catlog .upcomingcourseui .carousel-lo .item:hover .panel-thumbnail {
          border: 3px solid #fd0c0c;
      }
     
      .carousel-lo .item .panel-info .generalInfo .title {
          color: #ffffff;
      }
      .carousel-lo .item .panel-info .generalInfo {
            border-right: 1px solid #ddd  !important;
      }
      .carousel-lo .item .panel-info .moreInfo span.fa {
          color: #fd0c0c;
      }
     .carousel-lo .item .panel-info .description .title {
          color: #666666;
      }
      .carousel-lo .item .panel-info .description .attempt {
          color: #333333      }

.slick-slide .fa-info, .gridLayout .video-slide-image .fa-info{ 
    color:#ffffff;
  }
.cardbadge{color:#ffffff; }
.wpvs-close-video-drop {
 background: #fd0c0c;
  color: #ffffff;
  }
  
        .sessionWrap .backSession h6{
          color:  #333333        }
        .sessionWrap .backSession p{
          border-right: 1px solid #ddd;
        }
        
         .filterWrap .filter-card .input-group-text,
        .filter-Wrap .filterCalenderWrap .input-group-text {
          border: 1px solid #ddd;
        }
        

      #searchBar1 {
           background: #ffffff;
      }
      .searchClose1{
          color: #eee;
          color: #ffffff;
      }

/*.ctype{ display: none;}*/

.wpvs-close-video-drop {
  background: #fd0c0c;
  color: #ffffff;
 }
   
.commonStyleitemCard .fa-info{
    color: #ffffff;}

.commonStyleitemWrapper.active-slide {
    border: 2px solid #fd0c0c;
}

.video-item-grid .active-slide:after, .video-list-slider .active-slide:after{
    border-top: solid 15px #fd0c0c;
}
.slide-category.active-slide{
   border: 2px solid transparent;
}
.commonAccordionContent .subscriptionCard{
          border: 1px solid #ddd;
      }
  
      .commonAccordionContent .tabHeading .title{
        color: #333333        border-bottom: 1px solid #ddd;
      }
      .commonAccordionContent .tabHeading .title span{ background: #000000; }

        .commonAccordion{
          background: #eeeeee;
        }
        
        .commonAccordionContent .nav-tabs .nav-link {
          background-color: #3e3e3e;
          color: #ffffff;

      }

      .commonAccordionContent .nav-tabs .nav-link.active{
          color: #ffffff;
          background-color: #fd0c0c;
          border-color: #fd0c0c;
        }
        .commonAccordion .card,
        .commonAccordion .card-header{ 
          background:#eeeeee;
        }
        .commonAccordion .card-body .card .card-header a{
          color: #666666 !important;
          background: #3e3e3e;
        }
         .commonAccordion .card-body .card .card-header .active{
          color:  #ffffff !important;
          background: #fd0c0c !important;
        }
        .commonAccordion .title > a {
          color: #666666 !important;
      }
    
      .commonAccordionContent .subscriptionCard, .timeline-icon{
           background: #eeeeee !important;
      }
     
      .commonAccordion .card-body .card .card-header .title > a:after{
        display: none;
      }
      .commonAccordion .title > a:after {
        background: #3e3e3e;
        color: #ffffff;
      }
      .commonAccordion .title >  a[aria-expanded="true"].show:after {
          background: #fd0c0c;
          color: #ffffff;
      }
      .commonAccordion .card-header{
        border-bottom: 1px solid #000000;
      }
      .commonAccordion .card{
        background: #eeeeee;
      }
      
      #timeline{
        background-color: #ddd;
      }

       .marker{
          border: 1px solid #ddd;
          color: #666666;
          background-color: #666666;
      }
   
#footer {
  color: #666666;
}

.bootstrap-tagsinput .tag{
    color: #666666 !important
}
.bootstrap-tagsinput{
    background-color: transparent !important;
    border-color: #ddd !important;
    color: #666666 !important;
}

@media  screen and (max-width: 767px) {
  

  #topHeader .navbar-dark .navbar-toggler {
    background-color: #fd0c0c;
  }
 
  #topHeader .navbar-nav.navbar-right-panel .nav-item .dropdown-toggle {
    background: #3e3e3e;
  }

}

@media  screen and (min-width: 768px) and (max-width: 1024px) {
 
  #dateFormat .cardContent .custom-control {
       color: #666666;
  }

  #systemDiagnostics .fa.fa-file-text {
    color: #ffffff;
  }
   #splashPage .card-body .fa.fa-file-text {
    color: #ffffff;
  }
}

      
.lpathbox {      
  border-left: 1px solid #ddd;      
}   
.lpathbox .progressdiv {    

  background: #3e3e3e;    
  color: #3e3e3e;   
}   
.lpathbox .complete {   
  background:#fd0c0c;    
  color:#fd0c0c;   
}   
  
.lpathbox .boxbtn{  border-left: 2px solid #eeeeee; }    
.lpathbox .width-set-list {   
  background:#525252;       
}   
  
.lpathbox .coursetype{    
  border-right: 1px solid #ddd;    
}   
.align-self-center .active-slide{ border-left: 2px solid #eeeeee;}


/*--------------------------Dev css-----------------------------*/

.topheader-itemCount{
    background: #fd0c0c;
    color: #ffffff;
}
.languageWrapper .language .btn{        
    background: #fd0c0c !important;        
    border-color: #fd0c0c !important;              
    color: #ffffff;     
}       
.languageWrapper .language .dropdown-menu{
    background: #3e3e3e;        
    color: #666666;         
}      
.languageWrapper .language .dropdown-menu .dropdown-item{              
    color: #666666;           
}
.switchContainer .nav-link.active{      
    background: #fd0c0c;       
    color: #ffffff;          

}            
.switchContainer .nav-link{          
    background: #ddd;     
    color: #ffffff;             
}
#popup_update_password .customForm .form-group .input-group-prepend .input-group-text,
#myProfile .form-group.input-group .input-group-prepend .input-group-text,
#signup .form-group.input-group .input-group-prepend .input-group-text{
    background: #ddd !important;
    border-color: #ddd; 
}
        
#ui_login .form-group.input-group  div.error{
    color: #ff0000;
}

/*keep this*/
.error{
    color: #ff0000;
}

#linemanagers_form .dataTables_filter input,
#instructors_form .dataTables_filter input,
#masteradmin_form .dataTables_filter input{
    background: #3e3e3e;
}
#linemanagers_form .dataTables_length select,
#instructors_form .dataTables_length select,
#masteradmin_form .dataTables_length select{
    color: #666666;
    background: #3e3e3e !important;
}
#linemanagers_form .dataTables_length label,
#instructors_form .dataTables_length label,
#masteradmin_form .dataTables_length label{
    color: #ffffff;
}

#cke_reply_body .cke_top,#cke_reply_body  .cke_bottom,
#cke_message_text .cke_top,#cke_message_text .cke_bottom{
    background: #3e3e3e !important;
    border: 0px solid #ddd !important;
}
.dataTables_paginate .paginate_button a{
    color: #ffffff;
}
.dataTables_paginate .paginate_button.active a{
    color: #fd0c0c;
}
.modal .modal-body div.dataTables_wrapper div.dataTables_info{
    color: #ffffff;
}
.message .messageFooter .fileImgField .input-group-prepend .input-group-text{
    border: 1px solid #ddd;
}
.messageWrapper .message .attachment .fileImgField .filedata{
    border: 1px solid #ddd;
}
.messageWrapper .message .attachment .fileImgField .thumb-icons span:before{
    color: #ffffff !important;
}
.messageWrapper .message .attachment .fileImgField .input-group span.fa{
    border: 1px solid #ddd;
}


#specificteam_form .dataTables_filter input,
#linemanagers_form .dataTables_filter input,
#instructors_form .dataTables_filter input,
#masteradmin_form .dataTables_filter input{
    background: #3e3e3e;
}
#specificteam_form .dataTables_length select,
#linemanagers_form .dataTables_length select,
#instructors_form .dataTables_length select,
#masteradmin_form .dataTables_length select{
    color: #666666;
    background: #3e3e3e !important;
}
#specificteam_form .dataTables_length label,
#linemanagers_form .dataTables_length label,
#instructors_form .dataTables_length label,
#masteradmin_form .dataTables_length label{
    color: #ffffff;
}

#cke_reply_body .cke_top,#cke_reply_body  .cke_bottom,
#cke_message_text .cke_top,#cke_message_text .cke_bottom{
    background: #3e3e3e !important;
    border: 0px solid #ddd !important;
}

.dataTables_filter input {
    color: #ffffff;
}
.dataTables_paginate  a,
.dataTables_paginate .paginate_button a{
    color: #ffffff;
}
.dataTables_paginate .paginate_button.active a{
    color: #fd0c0c !important;
}
.modal .modal-body div.dataTables_wrapper div.dataTables_info{
    color: #ffffff;
}

.message .messageFooter .fileImgField .input-group-prepend .input-group-text{
    border: 1px solid #ddd;
}
.messageWrapper .message .attachment .fileImgField .filedata{
    border: 1px solid #ddd;
}
.messageWrapper .message .attachment .fileImgField .thumb-icons span:before{
    color: #ffffff !important;
}
.messageWrapper .message .attachment .fileImgField .input-group span.fa{
    border: 1px solid #ddd;
}
.calenderWrapper .fc-day.fc-future{
    background: #e5e5e5;
}

.calenderWrapper .fc-today{
    background-color: #000000 !important;
}
.calenderWrapper   .fc-state-default{
    background: #3e3e3e !important;
    color: #666666 !important;
}
.fc-toolbar .fc-state-active{
    color: #ffffff  !important;
}

.fc-state-highlight > div > div.fc-day-number{
    background-color: #fd0c0c !important;
}
.calenderWrapper  .fc-state-active{
    background: #fd0c0c !important;
    color: #666666;
}
td.fc-today {
    background: #ffffff !important;
}
.fc-first th{  
    color: #ffffff;
}
.fc-event-inner { 
    color: #ffffff !important;
}
.calenderWrapper .fc-toolbar h2 {
    color: #333333 !important;
    background: #fcfcfc;
}
.fc-border-separate tr.fc-last th{
    border-color: #ddd;
}

.fc-day-header{
    color: #ffffff;
}

#dashboard #profile .profileInfo .name{
    color: #ffffff;
}
#dashboard #profile .profileInfo .designation{
    color: #ffffff;
}
#announcementWrapper .announcement li .annTitle{
    color: #333333}
.chartLabelWrap p a{
    color: #333333}

#marketingCalender .nav-tabs .nav-link,
#expiringCertificateWrapper .nav-tabs .nav-link,
#myprogress .nav-tabs .nav-link{
    background-color: #3e3e3e;
    color: #ffffff;
}
#marketingCalender .nav-tabs .nav-link.active,
#expiringCertificateWrapper .nav-tabs .nav-link.active,
#myprogress .nav-tabs .nav-link.active{
    color: #ffffff;
    background-color: #fd0c0c;
    border-color: #fd0c0c;
}
.link{
    color:#333333    border-right:1px solid #ffffff;
}
#dashboard #marketingCalender .ei-name{
    color: #333333}
#dashboard #marketingCalender  .ei-event{
    background: #000000;
}

#dashboard #marketingCalender .marketing-calendar-info-icon{
    color: #fd0c0c;
}
#dashboard #marketingCalender .ei-event .ei-date .ei-day, 
#dashboard #marketingCalender .ei-event2 .ei-date .ei-day{
    color: #333333    background: #3e3e3e;
}

#dashboard #marketingCalender #ei-event-2 h2,
#dashboard #marketingCalender #ei-event h2,
#dashboard #marketingCalender #marketing-calendar-events h2{
    color: #fd0c0c;
}
#dashboard #marketingCalender  .ei-nav-container i,
#dashboard #marketingCalender  .ei-nav-container-2 i,
#dashboard #marketingCalender .ei-nav-container-mcal i{
    background: #3e3e3e;
}
#dashboard  #leaderBoard #leader_board_dropdown{
    border: 1px solid #ddd;
    color: #666666;
}
#prev_next_team_completion .nextarrow,
#prev_next_leaderboard .nextarrow{
    background:#fd0c0c;
    color: #ffffff;
}
#prev_next_team_completion .pervarrow,
#prev_next_leaderboard .pervarrow{
    background: #fd0c0c;
    color: #ffffff;
}
.carousel-control-next-icon{
    color: #ffffff !important;
}
.calenderWrapper .fc-next-button .fc-icon,
.calenderWrapper .fc-prev-button .fc-icon{
    color: #333333}
.calenderWrapper .fc-day-grid-event .fc-time {
    color: #ffffff;
}
.sliderWrapper .title{
    color: #333333}
.sliderWrapper .title .viewAll{
    color: #333333}

.categorybg {
    border: 1px solid #ddd !important;
}
.categorybg i {
    color: #ffffff !important;
    background: #3e3e3e !important;
}
.sliderWrapper .slick-slide.slick-active.active-slide{
    border: 2px solid #fd0c0c;
}
#slider-animation .carousel-control-prev{
    background: #3e3e3e;
} 
#kr-modal .modal-dialog .modal-header .string-limit-name{
    color: #333333}
#kr-modal .modal-dialog .modal-body .string-limit-name{
    color: #ffffff;
}
#kr-modal .modal-dialog .modal-body .date-time-wrap-2{
    border-top: 1px solid #ddd;
}

@media (max-width: 767px){
     #dashboard #profile .profileInfo{
        background: #fd0c0c;
    }
}
/*End 27-11-2019 Dashboard UI Issue****************************************************************************************/

/*start 28-11-2019 Dashboard UI Issue****************************************************************************************/
#kr-modal .datewrapbox{
    border-right: 1px solid #ddd;
}

.custmBtnPrimary:hover{
    color: #ffffff !important;
}
.custmBtnDefault{
    color: #ffffff !important;
}
.slick-next:before{
    color: #ffffff !important;
}
.slick-prev:before{
    color: #ffffff !important;
}
.prev_nextArrow .pervarrow{
    background: #fd0c0c;
    color: #ffffff;
}
.prev_nextArrow .nextarrow {
    background: #fd0c0c;
    color: #ffffff;
}
@media (max-width:576px){
    .calenderWrapper .fc-agendaDay-button.fc-button.fc-state-default.fc-corner-right{
        color: #ffffff !important;
    }
  
}

#knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-body ul.card-icons-wrapper li.card-icon.box-wrap{
    background: #3e3e3e;
}

#knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-border{border:5px solid #ddd;}
#knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-border.borderR_none{border-right:none;}

#knowledgeRespository_ui #knowledgeRespository_wrapper .card .card-body p{color: #333333;}

#my-passport-ui .cardContent .form-group .form-control{
    background: #3e3e3e;
    color: #333333    border: 1px solid #ddd;
}

.certificate-wrapper .certificate-tabs .nav-link.active:after{border-top: 12px solid #fd0c0c;}
#badges-leaderboard .badges-wrapper .badges-header-wrapper .badges-title,
#badges-leaderboard .leaderBoard-wrapper .leaderBoard-header-wrapper .leaderBoard-title {color:#333333;}
#badges-leaderboard .badges-wrapper .badges-header-wrapper .badges-status-box ul.badges-status-list{
    color: #333333;
}
#badges-leaderboard .badges-wrapper .badges-header-wrapper .badges-status-box ul.badges-status-list li:not(:last-child):after {border-right: 1px solid #ddd;}
#badges-leaderboard .badges-wrapper .badge-card-wrapper{border-top: 1px solid #ddd;}
#badges-leaderboard .badges-wrapper .badge-card-wrapper:last-child{border-bottom: 1px solid #ddd;}
#badges-leaderboard .badges-wrapper .badge-card-wrapper .v-line-right:after{    
    border-right: 1px solid #ddd;
}

#badges-leaderboard .badges-wrapper .badge-card-wrapper .badge-details .badge-details-title{color:#333333}
// .fa-star{color: ;}
#badges-leaderboard .badges-wrapper .badge-card-wrapper .badge-desc-title{color:#333333}
// #badges-leaderboard .badges-wrapper .badge-card-wrapper .badge-desc-wrapper p{color:;}

#badges-leaderboard .leaderBoard-wrapper .leader-card-row .leader-card .leader-rank{background:#000000;color:#ffffff;}
p .read-more, p .read-less{color:#fd0c0c;}

.externalCert-box-wrapper .externalCert-box-title .externalCert-box-header-text{
    color: #333333    color: #333333}


.externalCert-box-wrapper .externalCert-box-content .form-control{border:1px solid #ddd;}
.externalCert-box-wrapper .externalCert-box-content .form-control,
.externalCert-box-wrapper .externalCert-box-content label{color:#666666;}
.externalCert-box-wrapper .externalCert-box-content .submit-btn{background:#fd0c0c;color:#ffffff;}
.externalCert-box-wrapper .externalCert-box-content .cancel-btn{background:#525252;color:#ffffff;}

.certificate-right-button-wrapper .transcripts-dropdown .selection-dropdown #close_chooser,
.certificate-wrapper .transcripts-dropdown .close_chooser{
    background: #525252;
}
#my-passport-ui .leaderBCardContent,
#my-passport-ui .badgesCardcontent,
#my-passport-ui .cardContent{
    background: #fcfcfc;
}
#badges-leaderboard .badges-wrapper .badges-header-wrapper .badges-status-box ul.badges-status-list li .fa{
    color: #fd0c0c;
}
#my-badges .badge-card-body .badge-details i {
    color: #fd0c0c;
}
#my-badges .badge-counts .badge-count-card i {
    color: #fd0c0c;               
}
.certificate-wrapper .btn-clear-filter {
    background: #525252;          
}

.dataTables_filter input,
.certificate-wrapper .dataTables_filter input{
    color: #333333 !important;
}

/*need to add this*/
.certificate-wrapper .dataTables_length,
.dataTables_wrapper .dataTables_length{
    color: #333333;
} 

// .certificate-wrapper select {
//     color: ;
// }
.paginate_button.current {
    color: #fd0c0c !important;
}
.certificate-wrapper .certificate-tabs .nav-link.active {
    background: #fd0c0c ;position: relative;
    color: #ffffff !important;
} 

.certificate-wrapper .status-dropdown-btn:focus, .certificate-wrapper .status-dropdown-btn:hover,
#leaderBoard .leaderboard-dropdown-btn:hover{
    background: #fd0c0c;
    color: #ffffff !important;
}
.certificate-wrapper .dropdown-menu .dropdown-item,
#leaderBoard .dropdown-menu .dropdown-item{
    color: #333333;
}
.certificate-wrapper .dropdown-item.active, 
.certificate-wrapper .dropdown-menu .dropdown-item:hover,
#leaderBoard .dropdown-menu .dropdown-item:hover,
#leaderBoard .dropdown-menu .dropdown-item.active
{
    background: #fd0c0c !important;
}

.certificate-wrapper .selection-dropdown .custom-control {
    color: #333333;
}
.user-details-wrapper .edit-icon {
    background: #fd0c0c;
}
.user-details-wrapper .edit-icon i{
    color: #ffffff;
}
.user-details .user-detail {
    color: #333333;
}
.filter-Wrap .filter-card .input-group .input-group-prepend .input-group-text,
.filterWrap .filter-card .input-group .input-group-prepend .input-group-text{
    color: #333333;
    background: #ddd !important;
    border: 1px solid #ddd;
}
#my-passport-ui #date-dropdown .form-group .input-group-prepend .input-group-text{
    background: #525252;
    color: #333333;  
}
#my-passport-ui .courseGraph .form-group .input-group-prepend .input-group-text {
    color: #333333;
    background: #3e3e3e;
    border: 1px solid #ddd;
}
#my-passport-ui .courseGraph .calenderInputWrap .form-group .input-group-prepend .input-group-text {
    color: #333333;
    border: 1px solid #ddd;
}


.leaderBWrapper .leaderboard-details .leader-rank{
    background: #000000;
    color: #ffffff;
}
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item.active span,
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .dropdown-item:hover span{
    color: #ffffff !important;
}

.calenderWrapper .singleCalender h6{
    color: #333333}

#myProfile .fileUpload #btn_change_picture{
    background: #ffffff;
    color: #fd0c0c;
}

html  #topHeader.sticky nav.navbar{
    background:#ffffff !important;
}

#dashboardWig .custom-control{
    background: #3e3e3e;
}

#linemanager_myteam_ui #linemanagermyteam_wrapper .card{
    background:#525252;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-name{
    color:#333333;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status{
    border-top:1px solid #ddd;
    border-bottom:1px solid #ddd;
}

#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .container .status-box{
    color:#808080;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status  .status-circle{
    color:#ffffff;

}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .not-started .status-circle{
    background:#ff0000;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .in-progress .status-circle{
    background:#fab51c;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .completed .status-circle{
    background:#008000;
}
#linemanager_myteam_ui #linemanagermyteam_wrapper .card .card-body .member-status .not-completed .status-circle{
    background:#808080;
}

#linemanager_myteam_ui #linemanagermyteam_wrapper .card.member-card .message-wrapper{
    background: #fd0c0c;
}
.btn-details, .go-btn, .download-csv-btn{
    background:#fd0c0c;
    color: #ffffff;
}
.filter .fa-filter{
    color:#333333;
}
.filter-card .titleFilter{
    color: #333333;
}

.lds-ellipsis div {
    background: #fd0c0c;
}
.brdcrumblink a{
    color: #333333;
}

#ui_whitetheme .inner-content .dataTables_wrapper .table thead th,
#ui_whitetheme .inner-content .dataTables_wrapper .table td{
    border-bottom: 1px solid #ddd;  
}


ul.tagit input[type="text"] {
    color: #333333 !important;
}

#topHeader .navbar-nav.navbar-left-panel .nav-item.active .nav-link:before{
    border-bottom: 5px solid #ffffff ;
}

#catlog .boxsavedfilter, #myLearning .boxsavedfilter, #ui_recommendations .boxsavedfilter{ border: 1px solid #ddd; }
.morelink{ color: #fd0c0c;}
.iframelaunch .session-section {
    background: #eeeeee;
}
.iframelaunch .titleforsession, #ui_whitetheme .iframelaunch .titleforsession {background: #eeeeee!important;}

.iframelaunch .commonAccordionContent .subscriptionCard, .iframelaunch .timeline-icon {
    background: #ffffff !important;
}

.iframelaunch .boxfiles{       border: 2px dotted #ddd;
                               background: #ffffff;
                              }


.carousel-lo .item .panel-info .generalInfo .title{  color:#333333 }
.titleforsession{ background:#000000; }
.rightborder{ border-right:1px solid #ddd; }
.boxincont a{ color: #333333 }

#catlog svg, #myLearning svg, #ui_recommendations svg {
    fill: #ffffff;
    stroke:#ffffff;
}
.module-img svg {
    fill: #333333 !important;
    stroke:#333333!important;
}   
   
#catlog .carousel-lo .sessionItem:hover .panel-thumbnail, #catlog .carousel-lo .sessionItem.active .panel-thumbnail, 
#myLearning .carousel-lo .sessionItem:hover .panel-thumbnail, #myLearning .carousel-lo .sessionItem.active .panel-thumbnail,
#ui_recommendations .carousel-lo .sessionItem:hover .panel-thumbnail, #ui_recommendations .carousel-lo .sessionItem.active .panel-thumbnail
{border: 3px solid #fd0c0c;}

#couponInformationModal .bg-white{ background: #eeeeee;}

@media  screen and (max-width: 812px) {
     .learningpathui .custmBtnPrimary {    color: #ffffff!important;} } 


@media  only screen 
and (min-device-width: 768px) 
and (max-device-width: 1024px) 
and (orientation: landscape) 
and (-webkit-min-device-pixel-ratio: 1) {
    #my-passport-ui .leaderBCardContent, #my-passport-ui .badgesCardcontent{
        background: #fcfcfc !important;
    }
    
    #my-passport-ui .leaderBCardContent, #my-passport-ui .badgesCardcontent,
    #my-passport-ui .cardContent {
        background: #fcfcfc;
    }
    }

#myModal .commonAccordion .card-body .card .card-header a{ background: #eeeeee;}

#catlog .commonAccordion .card {background: #eeeeee !important;}
#ui_whitetheme #new_report_render .filter-card select{
    border:1px solid #ddd !important;
}


/*Start 26-12-2019**************************************************************/
#ui_whitetheme .modal .modal-body .custom-checkbox .custom-control-label:before{
    border: 1px solid #ddd;
}
#ui_whitetheme .dataTables_filter label input{
    border: 1px solid #ddd;
}
#course_requests_ui .table .custom-control-label::before{
    border-color: #ddd;
}

#course_requests_ui .custom-checkbox .custom-control-input:checked~.custom-control-label::after,
#course_requests_ui .table .custom-checkbox .custom-control-input:checked~.custom-control-label::after{
    border: solid #fd0c0c;
}


.messageWrapper .composeMail .bootstrap-tagsinput .tag{
    background-color: #3e3e3e;
    border: 1px solid #ddd;   
}

.messageWrapper .messageBlock .custom-checkbox .custom-control-input:checked~.custom-control-label::after{
    border: solid #ffffff;
}
.custom-control-label::before{
    background: #3e3e3e;
    border: #ddd solid 1px;
}
.custom-control-input:checked~.custom-control-label::before{
    background-color: #3e3e3e;
    border: #ddd solid 1px;

}

.filterWrap .filter-card .custom-checkbox .custom-control-input:checked~.custom-control-label::after{
    border: solid #fd0c0c;
}

.filterWrap .filter-card .custom-checkbox .custom-control-input:disabled:checked~.custom-control-label::before {
    background-color: #3e3e3e;
}
.paginate_button.active a{
    color: #fd0c0c;
}
.btn:hover{
    color: #ffffff;
}
#reports_ui #report_name{
    background-color: #3e3e3e !important;
}
#topHeader .navbar-collapse{
    background: #fd0c0c;
}
#topHeader .navbar-left-panel .nav-item a{
    color: #ffffff ;
}
#topHeader .navbar-left-panel .nav-item.active > a span{
    color: #ffffff ;
}

.bg-black{background:#ddd;}

.info-box-wrapper .info-box-title .info-header-text{color: #ffffff;}
.input-search{
    background: #ddd;
    color: #ffffff;
}

.input-search:focus{background-color:#ddd;color:#ffffff;}

.question-mark-icon{
    background:#fd0c0c;
    color:#000000;
}

.v-line{border-right: 1px solid #ddd;}
.h-line:after{background: #ddd;}
.filter-card{
    background:#525252;
}
.filter-card input[type="text"],.filter-card input[type="email"],
.filter-card select{background-color:#ddd !important;border:none;}
.filter-card input[type="text"],
.filter-card input[type="email"], 
.filter-card select,
input::-webkit-input-placeholder, 
input::placeholder,
// .filter-card select:focus{color: !important;} 
.filter-card label{
    color:#ffffff;
}
// .or-text{color:;}
.standard-reports-btn{color: #ffffff;}
.standard-reports-btn:hover{color:#ffffff;}
.reports-subheading{color:#fd0c0c;}

.dataTables_wrapper .dataTables_length select,
#courses-report-table-wrapper select {
   background-color: #3e3e3e !important;
    // color: ;
}

.inner-content h5 {
    color: #333333;
}
.certificate-wrapper .leaderBoard-wrapper select{
    background-color: #3e3e3e !important;
}

.btn-apply-filter,.btn-clear-filter,.btn-apply-filter:hover,.btn-clear-filter:hover{color: #ffffff;}
.btn-apply-filter {background: #fd0c0c;}
.radio-custom, .radio-custom-label {color: #666666;}
.radio-custom:checked + .radio-custom-label:before{background:#fd0c0c;}
.radio-custom + .radio-custom-label:before {
    background: #3e3e3e;
    border: 1px solid #ddd;
}

.radio-custom:checked + .radio-custom-label:before {
    color: #ffffff;
}

#reports_ui .dropdown-item, #course_requests_ui .dropdown-item{color:#ffffff !important}


/*Start:- Apsara 31-12-2019**************************************************************/
#topHeader .navbar-collapse.rmBgClrHeader .navbar-nav.navbar-left-panel .nav-item.active .nav-link:before{
    border-bottom: 5px solid #fd0c0c;
}
#topHeader .navbar-collapse.rmBgClrHeader{
    background: #ffffff !important;
    border-top: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
}
#topHeader  .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item.active a{
    color: #fd0c0c;
}
#topHeader  .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item a{
    color:#333333;
}
#topHeader  .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item a:hover {
    color: #fd0c0c;
}

#topHeader  .navbar-collapse.rmBgClrHeader  .navbar-left-panel .nav-item.active > a span {
    color: #fd0c0c; 
}
.filter-card h4{
    color: #333333;
}


#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .switchContainer .nav-link{
    background: #212121;
}
#topHeader .navbar-right-panel .nav-item .dropdown-toggle + .dropdown-menu .switchContainer .nav-link.active{
    background:  #fd0c0c}

.messageWrapper  .composeMail .bootstrap-tagsinput input{
    color: #333333;
} 

/*start:- 1-12-2019 Internal css of myLearning**************************************************************/
#catalogSort .active{
    background-color: #fd0c0c;
    color: #ffffff !important;
}

#catlog .table-bordered td, #catlog .table-bordered th{
    color: #333333}

#searchpop .search_helper_global{
    color: #fd0c0c;
}
#search_table_wrapper .table-bordered td,#search_table_wrapper .table-bordered th,
#search_table_wrapper .table td {
    border-bottom: 1px solid #ddd !important;
}
#search_table_wrapper #search_table a{
    color: #333333;
}
#search_table_wrapper #search_table td{
    color:#666666;
}

.dataTables_filter input{
    color: #666666;
}

#searchpop ul.tagit{
    background: #3e3e3e;
}

#searchpop  .searchBtnWrap button.close{
    background: #3e3e3e;
}

#reports_ui #new_report_render .select2-container--default .select2-selection--multiple,
#reports_ui #new_report_render .select2-container--default.select2-container--focus .select2-selection--multiple{
    border-color:#ddd;
}
#reports_ui select#report_name,
.course_requestsform select#action{
    background-color: #3e3e3e !important;
    color: #333333;
}

.forumWrapper .write_msg .input_msg_write {
    background: #525252;
}
.forumWrapper .type_msg{
    background: #525252;
}

.forumWrapper .iconList a{
    color: #666666;
}

.forumWrapper .outgoing_msg .name,
.forumWrapper .incoming_msg .name{
    color: #333333;
}

.forumWrapper  .outgoing_msg{
    border-bottom: 1px solid #ddd;
}
.forumWrapper  .outgoing_msg:last-child{
    border-bottom: 0px solid #ddd;
} 
#forum input.write_msg{
    color: #333333 !important;
}


@media  screen and (max-width: 767px){
    .dashboardfull .box h2, 
    .dashboardfull .inner-content h2,
    .learningpathui .inner-content h2,
    .learningpathui .box h2{
        color: #333333 !important;
    }
}

.form-control.error{
    color: #666666;
}


/*added to remove the scroll of the body*/
#view-detail-feedback .fname,
#view-detail-feedback .fdate{
    border-left: 2px solid #fd0c0c;
}


.accouncementWrapper .btnclose  span{
    background: #fd0c0c;
    color: #ffffff;
}


/*18-3-2020
 ie specific css*/
@media  screen and (-ms-high-contrast: active), (-ms-high-contrast: none){

    .messageWrapper .messageBlock .card .card-body .fa.fa-chevron-right{
        background: #fd0c0c;
    }
  
    #topHeader .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item a{
        color:#333333 !important;
    }
    #topHeader .navbar-collapse.rmBgClrHeader .navbar-left-panel .nav-item.active a,
    #topHeader  .navbar-collapse.rmBgClrHeader  .navbar-left-panel .nav-item.active > a span {
        color: #fd0c0c !important; 
    }

    #topHeader .navbar-left-panel .nav-item a{
        color: #ffffff !important; }
    .queryWrapper .messageBlock :checked.custom-control-input ~ .custom-control-label::before,
    #email_view_form .messageWrapper .messageBlock :checked.custom-control-input ~ .custom-control-label::before{
        background-color: #fd0c0c;
        border-color: #fd0c0c;
    }

    .slick-prev,.slick-next{
        background:#fd0c0c !important;
    }

    .checkmark::after{
        color: #fd0c0c !important;
    }
    .checkmark{
        border:1px solid #ddd !important;
    }
    #my-passport-ui .custom-control-label::before{
        border:1px solid #ddd !important;
    }
    #course_requests_ui .custom-checkbox .custom-control-label{
        color: #fd0c0c !important;
    }
    #course_requests_ui .table .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    #course_requests_ui .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{

        color: #fd0c0c !important;
    }
 
    #course_requests_ui .table .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    #course_requests_ui .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    .modal .modal-body .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    #dashboardWig .widget .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after,
    .instantActionBtn .custom-radio .custom-control-input:checked ~ .custom-control-label::after,
    .filterWrap .filter-card .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
     color: #fd0c0c !important;
    }


    .messageWrapper .messageBlock :checked.custom-control.custom-input ~ .custom-control-label::before{
        background-color: #fd0c0c;
    }
    .messageWrapper .messageBlock :checked.custom-control.custom-input~.custom-control-label::before{
        background-color: #fd0c0c !important;
    }
    
      .modal .modal-body .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
        color:#fd0c0c !important;
    }

    .modal .modal-body .custom-checkbox .custom-control-label{
        color:#fd0c0c !important;
    }

    #timezone_form #dashboardWig .widget .custom-checkbox .custom-control-input:checked ~ .custom-control-label::after{
        color: #fd0c0c !important;
    }
  
    #timezone_form #dashboardWig .custom-control .custom-control-label{
        color: #333333 !important;
    }
    #timezone_form #dashboardWig .custom-control .custom-control-label span{
        color: #333333 !important;
    }
    
    .instantActionBtn :checked.custom-control-input ~ .custom-control-label::before{
        background: #fd0c0c !important;
    }
    
    .commonStyleitemWrapper.active-slide{
        border:  2px solid #fd0c0c !important;
    }
}

/*18-3-2020 ie specific css*/


.anndescriptionWrapper img{
    border: 1px solid #fcfcfc;
}
.vs-video-description-drop .fa-times::before { color:#ffffff !important; }

/* 16-june*/
.assessment .btn-orange, .assessment .btnReset, .assessment .uploadBtn, 
.assessment-instruction-page .btn-save, .survey .btnReset{
    background-color: #fd0c0c !important;
    border-color: #fd0c0c !important;
    color: #ffffff !important;
}
.assessment .btn-orange:hover, .assessment .btnReset:hover, .assessment .uploadBtn:hover,
.assessment-instruction-page .btn-save:hover, .survey .btnReset:hover{
    background-color: #fd0c0c !important;
    border-color: #fd0c0c !important;
    color: #ffffff !important;
}
.survey .btn-transperant-blue, .survey .btn-white, .survey .btn-transperant, 
.survey .btn-file, .survey .btn-white-catalog, .survey .btn-default, 
.survey .btn-tran, .assessment .btn-transperant-blue, .assessment .btn-white, .assessment .btn-transperant, 
.assessment .btn-file, .assessment .btn-white-catalog, .assessment .btn-default, .assessment .btn-tran{
    background-color: #ffffff !important;
    border-color: #fd0c0c !important;
    color: #fd0c0c !important;
}
.survey .btn-transperant-blue:hover, .survey .btn-white:hover, .survey .btn-transperant:hover, 
.survey .btn-file:hover, .survey .btn-white-catalog:hover, .survey .btn-default:hover, 
.survey .btn-tran:hover, .assessment .btn-transperant-blue:hover, .assessment .btn-white:hover, .assessment .btn-transperant:hover, 
.assessment .btn-file:hover, .assessment .btn-white-catalog:hover, .assessment .btn-default:hover, .assessment .btn-tran:hover{
    background-color: #fd0c0c !important;
    border-color: #fd0c0c !important;
    color: #ffffff !important;
}

       #dashboard #announcementWrapper .alert-danger, #dashboard #expiringCertificateWrapper .alert-danger,
       #dashboard #marketingCalender .ei-events-container-mcal .alert-danger, #dashboard .chartWrap .alert-danger, 
       #dashboard .leaderBWrapper .alert-danger, #dashboad #leaderBoard .alert-danger, #dashboard .cardContent.equalHeightBlock .alert-danger,
       #dashboard #marketingCalender .alert-danger, #dashboard #myprogress .alert-danger{
        background: #fd0c0c;
        color: #ffffff;
        border-color: #fd0c0c;
    }
 
  
    
.redstatus .progress-bar{background: #ff0000;}
.greenstatus .progress-bar{background: #008000;}
.yellowstatus .progress-bar{background: #fab51c;}
.greystatus .progress-bar{background: #808080;}
/*End css for ticket-4771 added by Prashant*/

</style>    <script type="text/javascript" charset="UTF-8" src="./Course Details - Abara LMS_files/common.js.download"></script><script type="text/javascript" charset="UTF-8" src="./Course Details - Abara LMS_files/util.js.download"></script><script type="text/javascript" charset="UTF-8" src="./Course Details - Abara LMS_files/AuthenticationService.Authenticate"></script></head>
    <body class="   header-trial " id="ui_whitetheme">
    <!--start of header-->
    <?php  $lpdetails = get_lp_details($lpid);

 $courseids = get_assigned_courses($lpid);
?>
<main class="gridLayout lpDetailsPage" id="catlog">
    <div class="container-fluid">
        <!--page header-->
        <div class="pageHeader">
            <div class="row">
                <div class="col-12 col-sm-8">

                    <h3><span class="fa fa-road" ></span ><span style="color: #807e7e;"><?php echo $lpdetails->lpname ; ?></span></h3>
                </div>
                <div class="col-12 col-sm-4 quickAction d-flex flex-wrap justify-content-end">
                    <ul class="list-unstyled mb-0">                       
                        <li>
						<?php 
						global $USER ;
       ?>
						 <a href="user_lplist.php" class="btn custmBtnPrimary btn-sm mr-1">
                                <i class="fa fa-chevron-left mr-2" aria-hidden="true"></i> Back
                            </a>
						
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!--end of the page header-->
		
		 <div class="row" style="background:#fff;margin-right:5px;margin-left:10px;">
               <div class="col-lg-4 col-md-4 col-sm-4 col-5 align-self-center" style="padding : 10px">
                                    <div class="catalog-thumbnail boxin">
									<?php if(!empty($lpdetails->lpimage)){ ?>
                    <img src="<?php echo "lpimages/$lpdetails->lpimage" ?>" alt="" class="img-fluid">
                         <?php } else { ?> 
					<img src="1548346739_fundamentals.png" alt="" class="img-fluid">

                         <?php } ?>						 
								 </div>
                                </div>
                <div class="col-lg-8 col-md-4 col-sm-4 col-7 no-pad " style="padding : 10px">
									
                                            <div class="col-md-auto col">
                                              <h4 style="font-weight: bold;
    color: #000;"> Description</h4> 
                                            </div>
                                            

											
                                            <div class="col-md-auto col">
                                                <div class="coursetype">
                                                   <?php echo $lpdetails->lpdesc;?>
                                                </div>
                                            </div>
                                            
                </div>
				
            </div>
           <div  class="row" style="margin-bottom: 25px;
    background: #fff;
    margin-right: 5px;
    margin-left: 10px;">
				    <div class="col-lg-3 col-md-4 col-sm-4 col-5 align-self-center" style="padding : 10px">
                                   <i class="fa fa-book ltorgColor" style="margin-right: 2px;"></i> Courses : 
								   <span style="color:#000;font-weight:bold"> <?php


						$ccount = $DB->get_record_sql("select count(*) as cnt from {cm_lp_course} where lp_id=$lpid and lp_courseid > 0");

					  
                    if (($ccount->cnt) != 0) {
                        echo $ccount->cnt;
                    } else {
                        echo '0';
                    } ?> </span>
                                </div>  
								<div class="col-lg-4 col-md-4 col-sm-4 col-5 align-self-center" style="padding : 10px;margin-left: -20px;">
								<span class="coursetitle">
                                                    <i class="fa fa-keyboard-o mr-2" style="color: #ff6b4b;margin-right: 5px !important;"></i> No of days to complete :
                                                </span>
                                   <span style="color:#000;font-weight:bold"> <?php echo $lpdetails->lpdays;?> </span>
                                </div>
                            	
                            
                        
                </div>
				
        <div class="blockWrapper">
            <div id="message">
                                            </div>
											
	<?php 
     ?>
     <div class="sliderWrapper mt-3 mb50 learningpathui" >
   
			   <!--start of the learningpath course-->
				
				<?php
				
				
		   
	$query = "select b.id as courseid,b.fullname,b.summary,b.visible,b.points from {$CFG->prefix}cm_lp_course a,{$CFG->prefix}course b where a.lp_id = $lpid and a.lp_courseid = b.id";
		   			   

			  $objMyLPCourses = $DB->get_records_sql($query);
			  if(count($objMyLPCourses) != 0){
	
		
			   foreach($objMyLPCourses as $course){
				   
				   
  if($course->points == ''){
	  $vPoints = 'N/A';
  } else {
	   $vPoints = $course->points ;
  }
   
			   ?>
			  
                                <div class="lpathbox"> 
                    <div class="video-category slide-category slide-container slide-shortcode">
					
				
                        <span class="fa fa-circle progressdiv"></span>

						
                        <div class="width-set-list">
                           
						   <div class="row">
                                <div class="col-xl-2 col-lg-2 col-md-4 col-sm-4 col-5 align-self-center">
                                    <div class="catalog-thumbnail boxin">
									<?php
									require_once($CFG->libdir. '/coursecatlib.php');
									
		$coursen = new \course_in_list(get_course($course->courseid));
		
			if($course->course_type == 3){
		
		$courseimage = $course->imageurl;

		} else {
			
		
		$courseimage = '';
		foreach ($coursen->get_course_overviewfiles() as $file) {
		    $isimage = $file->is_valid_image();

            $url = new moodle_url("$CFG->wwwroot/pluginfile.php" . '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
            $file->get_filearea(). $file->get_filepath(). $file->get_filename(), ['forcedownload' => !$isimage]);

			$courseimage = $url ;
		}
        if (empty($courseimage)) {
            $courseimage = $CFG->wwwroot . "/theme/moove/pix/noimage.jpg";
			
        }
		
		}
		
		if($course->coursetype == 3){
		$courselink = $course->courselink;
$tag = "target=_blank";
		} else {
					$courselink = new moodle_url('/course/view.php', array('id' => $course->courseid,'visible' => 1));
$tag = "";
		}
									?>
									<!--                                        <img src="<?php echo $courseimage ; ?>" alt="" class="img-fluid">
-->
                                       <img src="./Course Details - Abara LMS_files/3785210_od4h82e_thumb.jpg" alt="" class="img-fluid">
                                   
								   </div>
                                </div>
                                <div class="col-xl-7 col-lg-6 col-md-4 col-sm-4 col-7 no-pad align-self-center">
                                    <div class="boxincont">
                                        <h5 class="d-none d-sm-block"><?php echo $course->fullname; ?></h5>
                                         <h5 class="d-block d-sm-none">
                                            <a href="javascript:void(0)" class="video-slide viewLpCourse" course_id="29" lp_id="74"> <?php echo $course->fullname; ?>
                                                <label class="show-vs-drop ease3" data-type="video"></label>
                                            </a>
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-auto col">
                                                <div class="coursetype">
                                                    <i class="fa fa-clock-o ltorgColor" style="margin-right: 3px;"></i> Duration :  <span class="coursetitle"> <?php echo $vDuration ; ?>  </span>
                                                </div>
                                            </div>
                                            <div class="col-5 no-pad d-block d-sm-none">
                                                <a href="javascript:void(0)" class="btn custmBtnDefault btn-sm mr-1 courseInfo" data-toggle="modal" data-target="#courseInformationModal" course_id="29" lp_id="74">
                                                    <span class="fa fa-info-circle"></span>
                                                </a>
                                                 <a href="javascript:void(0)" class="btn custmBtnPrimary btn-sm  viewLpCourse" course_id="29" lp_id="74">
                                            <span class="fa fa-arrow-down"></span></a>
                                            </div>
                                            <div class="col no-pad d-none d-lg-block">
											
											
                                                <span class="coursetype">
                                                    <i class="fa fa-trophy mr-2" style="margin-right:3px !important;color:#ff6b4b"></i> Points : <span class="coursetitle"><?php echo $vPoints ; ?></span>
													
                                                </span>
					
												<?php
						$completed_course = get_user_courseprogress($USER->id,$course->courseid);
				$progress = 0;	 
					if($completed_course ==2) {
						$progress="100";
					} else if($completed_course ==1) {
								$progress ="50";
                           }else{
                               $progress ="0";
                           }
		if($progress == 100){  ?>
            <span style="background-color: green;color:#fff;padding: 1px 10px;border-radius: 1px;font-size: 13px;margin-left: 15px;">Completed</span>
 <?php } else if($progress == 0){ ?>
             <span style="background-color: red;color:#fff;padding: 1px 10px;border-radius: 1px;font-size: 13px;margin-left: 15px;">Not Started</span>

 <?php } else { ?>
             <span style="background-color: blue;color:#fff;padding: 1px 10px;border-radius: 1px;font-size: 13px;margin-left: 15px;">In Progress</span>

 <?php } ?>
                                                                                                                                                                                            </div>
                                            <div class="tags tags-hidden d-none" id="c_tags_29">
                                                                                      <button type="button" class="btn btn-sm btn-outline-secondary"></button>
                                                                         <button type="button" class="btn btn-sm btn-outline-secondary"></button>
                                                                                                                                                </div>
                                        </div>
                                    </div>
                                </div>
								<?php
									


								
								
								?>
                                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-4 d-none d-sm-block align-self-center">
                                    <div class="boxbtn">
									
									
									  <a class="btn custmBtnDefault" role="" data-container="" style="padding: 10px 8px;" data-toggle="popover" data-placement="right" data-content="<div class=&quot;no-overflow&quot;><p><?php 
		if(!empty($course->summary) && $course->summary != 'NULL'){
echo htmlspecialchars($course->summary);
} else {
	echo 'No Description';
}
?></p>
</div> " data-html="true" tabindex="0" data-trigger="focus"  data-original-title="" title="" id="yui_3_17_2_1_1614076438346_27">
  <i class="icon fa fa-info-circle text-muted fa-fw " aria-hidden="true" title="Description" aria-label="Description" style="float: right;" id="yui_3_17_2_1_1614076438346_26"></i>
</a>
									
                                        
                                    </div>
                                </div>
											
						
								
                            </div>
                        </div>
                    </div>
                    <!--start of the course description-->
                    <div class="vs-video-description-drop mlmr" style="display: none;"></div>
                    <!--end of the course description-->
                </div>
                 
			   <?php

			   } 
			   } else { ?>
			   
		<div class="lpathbox"> 
                    <div class="video-category slide-category slide-container slide-shortcode">

			      			<span class="fa fa-circle progressdiv" style="color: #999999;background:#999999 !important"></span>
	
						
                        <div class="width-set-list">
                           
						   <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12  align-self-center">
                                    <div class="catalog-thumbnail boxin">
									
                                    <div class="boxincont">
                                        <h5 class="d-none d-sm-block">No courses assigned this Learning Plan</h5>
                                        

			
			  
			   </div>
			   </div>
			   </div>
			   </div>
			   </div>
			   </div>
			   </div>  
			   
				 
		<?php   } 
		   
 ?>

                                <!--end of the learningpath course-->
           
        </div>
    </div>
</main>
<!--end of the main content-->
<!--Start of the course modal-->
<div class="modal" id="courseInformationModal" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 id="title" class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>
            <!-- Modal body -->
            <div class="modal-body p-0" id="SimpleBar" data-simplebar="init"><div class="simplebar-wrapper" style="margin: 0px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" style="height: auto; overflow: hidden;"><div class="simplebar-content" style="padding: 0px;">
              <!--   <div id="SimpleBar" data-simplebar="init"> -->
                <div class="p-16 p-b-0">
                    <h3 class="mainHeading c_name"></h3>
                    <div class="basicInfo d-flex">
                        <p class="c_type"></p>
                        <p class="c_course_sts"></p>
                    </div>
                </div>
                <div class="p-16 p-b-0">
                    <h5 class="subHeading">keyword:</h5>
                    <p class="c_tag"></p>
                </div></div></div></div></div><div class="simplebar-placeholder" style="width: 0px; height: 0px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: hidden;"><div class="simplebar-scrollbar" style="transform: translate3d(0px, 0px, 0px); display: none;"></div></div>
                <div class="hr"></div>
                <div class="pr16 pl16">
                    <h5 class="subHeading">Description:</h5>
                    <p class="c_desc"></p>
                </div>
                <div class="col-md-12  col-sm-12  col-xs-12 no-margin categorybg pr16 pl16" id="category-data">
                    <div class="panel-group w-100" id="accordion" role="tablist" aria-multiselectable="true">
                        <div>
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h5 class="subHeading" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <span>
                                            <i class="fa fa-plus more-less pull-right" aria-hidden="true"></i>
                                            Category: 
                                            <span class="c_cat"></span>
                                        </span>
                                    </h5>
                                </div>
                            <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                <ul class="panel-body no-padding">
                                    <li>
                                        Sub Category 1: 
                                        <span class="c_sub1"></span>
                                    </li>
                                    <li>
                                        Sub Category 2: 
                                        <span class="c_sub2"></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="row threeGriedTier" id="remInCatalog">
                        <div class="listing-data  w-100 justify-content-between">
                            <div class="col border-right p-16">
                                <h5 class="subHeading">
                                    <span class="fa fa-calendar mr-2"></span>Start Date:
                                </h5>
                                <p class="mb-0 m-l-25"><span class="c_start_date"></span></p>
                            </div>
                            <div class="col border-right p-16">
                                <h5 class="subHeading">
                                    <span class="fa fa-calendar mr-2"></span>End Date:
                                </h5>
                                <p class="mb-0  m-l-25"><span class="c_end_date"></span></p>
                            </div>
                            <div class="col p-16">
                                <h5 class="subHeading">
                                    <span class="fa fa-calendar mr-2"></span>Duration:
                                </h5>
                                <p class="mb-0 m-l-25 c_duration"></p>
                            </div>
                        </div>
                        <div class="details-data w-100 justify-content-between">
                            <div class="elearning-course w-100">
                                <div class="col border-right p-16">
                                    <h5 class="subHeading">
                                        <span class="fa fa-calendar mr-2"></span>First Access Date:
                                    </h5>
                                    <p class="mb-0 m-l-25"><span class="c_first_access_date"></span></p>
                                </div>
                                <div class="col border-right p-16">
                                    <h5 class="subHeading">
                                        <span class="fa fa-calendar mr-2"></span>Last Access Date:
                                    </h5>
                                    <p class="mb-0  m-l-25"><span class="c_last_access_date"></span></p>
                                </div>
                                <div class="col p-16">
                                    <h5 class="subHeading">
                                        <span class="fa fa-calendar mr-2"></span>Completion Date:
                                    </h5>
                                    <p class="mb-0  m-l-25"><span class="c_completion_date"></span></p>
                                </div>
                            </div>

                            <div class="col notelearning-course  p-16">
                                <div class="col border-right p-16">
                                    <h5 class="subHeading">
                                        <span class="fa fa-calendar mr-2"></span>Completion Date:
                                    </h5>
                                    <p class="mb-0  m-l-25"><span class="c_completion_date"></span></p>
                                </div>
                                <div class="col p-16">
                                    <h5 class="subHeading">
                                        <span class="fa fa-calendar mr-2"></span>Attendance:
                                    </h5>
                                    <p class="mb-0  m-l-25"><span class="c_attendance"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <!--</div>--> 
            </div> 
        </div>
    </div>
</div>
<!--end of the course modal--><!--Start of the module modal-->


<script type="text/javascript" src="./Course Details - Abara LMS_files/catalog_details.js.download"></script>

</body></html>
 
<?php
echo $OUTPUT->footer();


			
				
  