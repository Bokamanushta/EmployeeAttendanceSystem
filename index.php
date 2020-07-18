<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';
require_once 'classes/Complain.php';
require_once 'classes/Admin.php';
require_once 'classes/Employee.php';
require_once 'models/complianModel.php';
require_once 'models/AdminModel.php';
require_once 'models/EmployeeModel.php';
require_once 'core/config.php';

$app = new \Slim\App;

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Taling from the root");
    return $response;
});

// get all complains
$app->get('/api/complains', function (Request $request, Response $response, array $args) {
    $complainModel = ComplainModel::getInstance();
    $complainModel->getAllComplains();
});

// get single complain
$app->get('/api/complains/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $complainModel = ComplainModel::getInstance();
    $complainModel->getSingleComplain($id);
});

// create a complain
$app->post('/api/complains', function (Request $request, Response $response, array $args) {
    $complain = new Complain($_POST["id"], $_POST["title"], $_POST["description"], $_POST["status"]);
    $complainModel = ComplainModel::getInstance();
    $complainModel->addNewComplain($complain);
});

//edit complain
$app->put('/api/complains/{id}', function (Request $request, Response $response, array $args) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body, true);
    $id = $args['id'];
    $complain = new Complain($id, $data["title"], $data["description"], $data["status"]);
    $complainModel = ComplainModel::getInstance();
    $complainModel->updateComplain($complain);
});

//delete complain 
$app->delete('/api/complains/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $complainModel = ComplainModel::getInstance();
    $complainModel->deleteComplain($id);
});


//**Admin Functionality */
//**Start of Admin Functionality Coding. This section hold all the appropriate routes that is necessary for admin*/
//**Admin Functionality */

// create an Admin
$app->post('/api/admin', function (Request $request, Response $response, array $args) {
    $id = $_POST["id"];
    // $file = $_POST["file"];
    // $allowed = array('jpg', 'jpeg', 'png');
    // $ext = explode('.', $file['name']);
    // $FileExt = strtolower(end($ext));
    // if (in_array($FileExt, $allowed)) {
    //     $fileDest = "files/pictures/admins/{$id}.{$FileExt}";
    //     move_uploaded_file($file['tmp_name'], $fileDest);
    // } else echo "You can not upload {$FileExt} type of file";

    // inserting data
    $admin = new Admin($id, $_POST["name"], $_POST["username"], $_POST["password"]);
    $adminModel = AdminModel::getInstance();
    $adminModel->addNewAdmin($admin);
});

//login functionality
$app->post('/api/login', function (Request $request, Response $response, array $args) {
    $username = $_POST["username"];
    $password =  $_POST["password"];
    $adminModel = AdminModel::getInstance();
    $adminModel->login($username,$password);
});

//logout functionality
$app->get('/api/logout', function (Request $request, Response $response, array $args) {
    $adminModel = AdminModel::getInstance();
    $adminModel->logout();
});

// get All Attendace time IN 
$app->post('/api/admin/timein', function (Request $request, Response $response, array $args) {
    $date = $_POST['date'];
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getAllTimeIN($date);
});

// get All Attendace time out 
$app->get('/api/admin/timeout', function (Request $request, Response $response, array $args) {
    // $date = $_POST['date'];
    $employeeModel = EmployeeModel::getInstance();
    // $employeeModel->getAllTimeOut($date);
    $employeeModel->getAllEmployeeID();
});


//**End of Admin Functionality */
//**Admin Functionality Ends here*/


//**EMployee Functionality */
//**Start of EMployee Functionality Coding. This section hold all the appropriate routes that is necessary for EMployee*/
//**EMployee Functionality */

$app->post('/api/employee', function (Request $request, Response $response, array $args) {
    $employee = new Employee($_POST["id"], $_POST["name"], $_POST["email"], $_POST["rfid"],
    $_POST["dob"], $_POST["phone"], $_POST["ic"], $_POST["dept"],$_POST["pos"]);
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->addNewEmployee($employee);
});

// get all positions
$app->get('/api/positions/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getAllPositions($name);
});

// get all Departments
$app->get('/api/departments', function (Request $request, Response $response, array $args) {
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getAllDepartments();
});


// update the employee
//edit complain not verified
$app->put('/api/employee/{id}', function (Request $request, Response $response, array $args) {
    $request_body = file_get_contents('php://input');
    $data = json_decode($request_body, true);
    $employee = new Employee($args['id'], $data["name"], $data["email"], $data["rfid"],
    $data["dob"], $data["phone"], $data["ic"], $data["dept"],$data["pos"]);
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->updateEmployeeInfo($employee);
});


// get all employee
$app->get('/api/employee', function (Request $request, Response $response, array $args) {
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getAllEmployee();
});

// delete an emplyee 
$app->delete('/api/employee/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->deleteEmployee($id);
});

// get an single employee verifed
$app->get('/api/employee/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getEmployeeInfo($id);
});

// make attendance 
$app->post('/api/attendace/timein', function (Request $request, Response $response, array $args) {
    $rfid = $_POST["rfid"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->timeIN($rfid,$date,$time);
});
// make attendance 
$app->post('/api/attendace/timeout', function (Request $request, Response $response, array $args) {
    $rfid = $_POST["rfid"];
    $date = $_POST["date"];
    $time = $_POST["time"];
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->timeOut($rfid,$date,$time);
});

// record absents 
$app->post('/api/attendace/absent', function (Request $request, Response $response, array $args) {
    $rfid = $_POST["rfid"];
    $date = $_POST["date"];
    $month = $_POST["month"];
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->recordAbsent($rfid,$date,$month);
});

// this route will take the dashboard data
$app->get('/api/dashboard', function (Request $request, Response $response, array $args) {
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getAllDashboardData();
});
// this route will give all the employee who will get less paymetnt
$app->get('/api/empoyee/payment', function (Request $request, Response $response, array $args) {
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getAllLeesPaymentEmployee();
});
// this route will give all the employee who will get less paymetnt
$app->get('/api/empoyee/department', function (Request $request, Response $response, array $args) {
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->viewAllDeaprtment();
});
// this route will give all the employee who will get less paymetnt
$app->get('/api/empoyee/halloffames', function (Request $request, Response $response, array $args) {
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getAllHallFames();
});
$app->post('/api/admin/timin', function (Request $request, Response $response, array $args) {
    $time = $_POST['time'];
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getAllTimeIN($time);
});
$app->post('/api/admin/timeout', function (Request $request, Response $response, array $args) {
    $time = $_POST['time'];
    $employeeModel = EmployeeModel::getInstance();
    $employeeModel->getAllTimeOut($time);
});
//**End of EMployee Functionality */
//**EMployee Functionality Ends here*/

$app->run();
