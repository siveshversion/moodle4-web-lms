<?php
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');

header('Access-Control-Max-Age: 1000');

header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

header('Content-Type: application/json');
include_once '../../config.php';
include_once './functions.php';
require_once "./curl.php";

global $CFG;

require_once $CFG->dirroot . '/cm/lib/cmlib.php';

error_reporting(E_ALL);

if (isset($_GET['methodname'])) {

    $methodName = $_GET['methodname'];

    if (function_exists($methodName)) {

        $_POST['Data'] = file_get_contents("php://input");

        if (isset($_POST)) {

            $array = $_POST;

            if ($methodName == 'upload_question_bank' || 'saveProgram' || 'certificatePreview') {
                $returnResults = $methodName($array, $_FILES); //function call
            } else {
                $returnResults = $methodName($array); //function call
            }

            $objResponse = new stdClass();

            $objResponse->ResponseCode = $returnResults['ResponseCode'];

            $objResponse->ResponseMessage = $returnResults['ResponseMessage'];

            $vJSONData = $returnResults['Data'];

            if (isset($returnResults['AdditionalData'])):

                $vJSONAddData = $returnResults['AdditionalData'];

            else:

                $vJSONAddData = null;

            endif;

            deliver_response(array('methodName' => $methodName, 'status' => 200, 'response_msg' => $objResponse->ResponseMessage, 'data' => $vJSONData, 'response_code' => $objResponse->ResponseCode, 'nextoffset' => 7));

        } else {

            deliver_response(array('methodName' => $methodName, 'status' => 200, 'response_msg' => 'No input datas specified from1', 'data' => null, 'response_code' => 0, 'nextoffset' => 7));

            die;

        }

    } else {

        deliver_response(array('methodName' => $methodName, 'status' => 200, 'response_msg' => 'Method not found', 'data' => null, 'response_code' => 0, 'AdditionalData' => null));

        die;

    }

} else {

    deliver_response(array('methodName' => 'method not exist', 'status' => 400, 'response_msg' => 'Invalid Request', 'data' => null, 'response_code' => 0, 'AdditionalData' => null));

    die;

}
