<?php
date_default_timezone_set("Asia/Kolkata");
error_reporting(E_ALL);

function deliver_response($array)
{
    //header("HTTP/1.1 ".$array['status']);
    $response['Status'] = $array['status'];
    $response['ResponseMessage'] = $array['response_msg'];
    $response['Data'] = $array['data'];
    $response['ResponseCode'] = $array['response_code'];
    $response['methodName'] = $array['methodName'];
    $response['AdditionalData'] = $array['AdditionalData'];
    echo $json_response = json_encode($response);
}

function TestConnection($arrInput)
{
    global $fields_head, $wwwroot;
    $count = 0;
    $arrResult[$count]["connection"] = 1;
    $count++;
    $arrReturn["Data"] = $arrResult;
    echo json_encode($arrReturn);
    exit;
}

function validateLogin($arrInput)
{

    global $CFG, $DB;

    $vUserId = $arrInput['userid'];
    $vUsername = $arrInput['username'];
    $vPassword = $arrInput['password'];

    $vUsername = 'admin';
    $vPassword = 'Learn@123';

    $query = "select id from mdl_user where username like BINARY '$vUsername'";
    $vUsernameExits = $DB->get_record_sql($query);

    if ($vUsernameExits->id == '') {

        $query = "select id from mdl_user where username like '$vUsername'";
        $vUsernameExits = $DB->get_record_sql($query);
    }

    if ($vUsernameExits->id != '') {

        $restformat = 'json';

        $params = array('username' => $vUsername, 'password' => $vPassword);

        $serverurl = $CFG->wwwroot . '/login/token.php?service=moodle_mobile_app';

        $curl = new curl;
        $restformat = ($restformat == 'json') ? '&moodlewsrestformat=' . $restformat : '';

        $resp = $curl->post($serverurl . $restformat, $params);

        $arrToken = json_decode($resp, true);

        if ($arrToken[token] != '') {

            //get userid from username
            $objUser = $DB->get_record('user', array("username" => $vUsername));

            //update token in user key auth plugin tables
            $vMoodleToken = $arrToken[token];
            $vUserId = $objUser->id;

            $objUserKey = $DB->get_record('user_private_key', array('userid' => $vUserId, 'script' => 'auth/userkey'));

            if ($objUserKey->id != '') {

                $DB->set_field('user_private_key', 'value', $vMoodleToken, array('userid' => $vUserId, 'script' => 'auth/userkey'));
            } else {

                $objUserAuth = new stdclass();
                $objUserAuth->script = 'auth/userkey';
                $objUserAuth->value = $vMoodleToken;
                $objUserAuth->userid = $vUserId;
                $objUserAuth->timecreated = time();

                $DB->insert_record('user_private_key', $objUserAuth);

            }

            $arrResults['Data']['result'] = 1;
            $arrResults['Data']['message'] = 'Success';
            $arrResults['Data']['token'] = $arrToken[token];
            $arrResults['Data']['userid'] = $objUser->id;
            $arrResults['Data']['email'] = $objUser->email;
            $arrResults['Data']['firstname'] = $objUser->firstname;
            $arrResults['Data']['lastname'] = $objUser->lastname;
            $arrResults['Data']['surname'] = $objUser->lastname;
            $arrResults['Data']['country'] = $objUser->country;
            $arrResults['Data']['city'] = $objUser->city;
            $arrResults['Data']['phone1'] = $objUser->phone1;
            $arrResults['Data']['role'] = $vRole;
            $arrResults['Data']['paytm_id'] = $CFG->Merchant_ID;
            $arrResults['Data']['colorcode'] = $objTeacherConfig->color_code;
            $arrResults['Data']['fontcolorcode'] = $objTeacherConfig->font_color;

            if (strpos($objTeacherConfig->logo_path, 'Choose Logo') !== false) {
                $vLogoAvailable = false;

            } else {
                $vLogoAvailable = true;
            }

            if ($objTeacherConfig->logo_path != '' and $vLogoAvailable == true) {
                $arrResults['Data']['customlogo'] = $CFG->wwwroot . '/cm/custom_api/' . $objTeacherConfig->logo_path;
            } else {
                $arrResults['Data']['customlogo'] = null;
            }

        } else {
            $arrResults['Data']['result'] = 0;
            $arrResults['Data']['token'] = '';
            $arrResults['Data']['message'] = 'Username / Password is wrong, please enter the correct details';

        }

    } else {

        $arrResults['Data']['result'] = 0;
        $arrResults['Data']['token'] = '';
        $arrResults['Data']['message'] = 'Username / Password is wrong, please enter the correct details';

    }

    return $arrResults;

}
