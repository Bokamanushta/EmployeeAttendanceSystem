<?php

require_once 'api/db.php';
require_once 'classes/Employee.php';

// session_start();

class EmployeeModel
{
    private static $_instance = null;
    private $_db;

    public function __construct()
    {
        $this->_db = DB::getInstance();
    }

    // implementing singleton class
    public static function getInstance()
    {
        if (!isset(self::$_instance)) self::$_instance = new EmployeeModel;
        return self::$_instance;
    }

    // getting a single admin onfo for setting page
    public function getEmployeeInfo($id)
    {
        try {
            $sql = "SELECT * FROM employee WHERE employeeID = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            echo json_encode($result);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }

    // get all departments 
    public function getAllDepartments()
    {
        try {
            $sql = "SELECT name FROM department";
            $stmt = $this->_db->query($sql);
            $departments = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($departments);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }


    // get all departments 
    public function getAllPositions($name)
    {
        try {
            $sqlDep = "SELECT departmentID FROM department WHERE name  = :name";
            $stmtDE = $this->_db->prepare($sqlDep);
            $stmtDE->bindParam(':name', $name);
            $stmtDE->execute();
            $resultDe = $stmtDE->fetch(PDO::FETCH_OBJ);

            $sql = "SELECT name FROM position WHERE departmentID  = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $resultDe->departmentID);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($result);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }


    // adding neew Admins 
    public function addNewEmployee(Employee $emplyee)
    {
        try {
            // first getting the positiona nd department id 
            $position = $emplyee->getPosition();
            $sqlFirst = "SELECT * FROM position WHERE name = :position";
            $stmt = $this->_db->prepare($sqlFirst);
            $stmt->bindParam(':position', $position);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            $sql = "INSERT INTO employee (employeeID,name,ic,rfid,dob,email,phone,departmentID,positionID) 
            VALUES (:employeeID,:name,:ic,:rfid,:dob,:email,:phone,:departmentID,:positionID)";

            $stmt = $this->_db->prepare($sql);

            $stmt->bindValue(':employeeID', $emplyee->getID());
            $stmt->bindValue(':name', $emplyee->getName());
            $stmt->bindValue(':ic', $emplyee->getIC());
            $stmt->bindValue(':rfid', $emplyee->getRFID());
            $stmt->bindValue(':dob', $emplyee->getDOB());
            $stmt->bindValue(':email', $emplyee->getEmail());
            $stmt->bindValue(':phone', $emplyee->getPhone());
            $stmt->bindValue(':departmentID', $result->departmentID);
            $stmt->bindValue(':positionID', $result->positionID);
            // $stmt->bindValue(':picture', $admin->getPicture());

            $stmt->execute();
            $count = $stmt->rowCount();

            $data = array(
                "status" => "success",
                "rowcount" => $count,
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }

    // editing Admin's info 
    public function updateEmployeeInfo(Employee $employee)
    {
        $sql = "UPDATE employee SET
                name	 = :name,
                ic = :ic,
                rfid = :rfid,
                dob  = :dob,
                email = :email,
                phone = :phone,
                departmentID  = :departmentID,
                positionID = :positionID
			    WHERE employeeID = :id";

        try {

            // first getting the positiona nd department id 
            $position = $employee->getPosition();
            $sqlFirst = "SELECT * FROM position WHERE name = :position";
            $stmt = $this->_db->prepare($sqlFirst);
            $stmt->bindParam(':position', $position);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);

            $id = $employee->getID();
            $name = $employee->getName();
            $ic = $employee->getIC();
            $rfid = $employee->getRFID();
            $dob = $employee->getDOB();
            $email = $employee->getEmail();
            $phone = $employee->getPhone();
            $departmentID = $result->departmentID;
            $positionID = $result->positionID;
            // $picture = $admin->getPicture();

            $stmt = $this->_db->prepare($sql);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':ic', $ic);
            $stmt->bindParam(':rfid', $rfid);
            $stmt->bindParam(':dob', $dob);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':departmentID', $departmentID);
            $stmt->bindParam(':positionID', $positionID);
            // $stmt->bindParam(':picture', $picture);

            $stmt->execute();
            $count = $stmt->rowCount();

            $data = array(
                "rowAffected" => $count,
                "status" => "success"
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }

    // getAll Employee 
    public function getAllEmployee()
    {
        try {
            $sql = "SELECT * FROM employee";
            $stmt = $this->_db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $data = array();
            foreach ($result as $res) {
                $deptName = $this->getDepartmentName($res->departmentID);
                $posName = $this->getPositionName($res->positionID);
                $res->posName = $posName;
                $res->deptName = $deptName;
                array_push($data, $res);
            }
            // print_r($data);
            echo json_encode($data);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            echo json_encode($e);
        }
    }

    // delete a employee 
    public function deleteEmployee($id)
    {
        try {
            $sql = "DELETE FROM employee WHERE employeeID = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $count = $stmt->rowCount();

            $data = array(
                "rowAffected" => $count,
                "status" => "success"
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // this function will take the attendance
    public function timeIN($rfid, $date, $time)
    {

        $data = $this->getEmployeeID($rfid);
        if ($data['status'] == "success") {
            try {
                $sql = "INSERT INTO timein (time,date,employeeID) 
                VALUES (:time,:date,:employeeID)";
                $stmt = $this->_db->prepare($sql);
                $stmt->bindValue(':employeeID', $data['employeeID']);
                $stmt->bindValue(':time', $time);
                $stmt->bindValue(':date', $date);

                $stmt->execute();
                $count = $stmt->rowCount();
                $data = array(
                    "status" => "success",
                    "rowcount" => $count,
                );
                echo json_encode($data);
            } catch (PDOException $e) {
                $data = array("status" => "fail");
                echo json_encode($data);
            }
        } else echo json_encode($data);
    }


    public function timeOut($rfid, $date, $time)
    {

        $data = $this->getEmployeeID($rfid);
        if ($data['status'] == "success") {
            try {
                $sql = "INSERT INTO timeout (time,date,employeeID) 
                VALUES (:time,:date,:employeeID)";
                $stmt = $this->_db->prepare($sql);
                $stmt->bindValue(':employeeID', $data['employeeID']);
                $stmt->bindValue(':time', $time);
                $stmt->bindValue(':date', $date);

                $stmt->execute();
                $count = $stmt->rowCount();
                $data = array(
                    "status" => "success",
                    "rowcount" => $count,
                );
                echo json_encode($data);
            } catch (PDOException $e) {
                $data = array("status" => "fail");
                echo json_encode($data);
            }
        } else echo json_encode($data);
    }

    public function getEmployeeID($rfid)
    {
        try {
            $sql = "SELECT employeeID FROM employee WHERE rfid = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $rfid);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $employeeID = $result->employeeID;
            $data = array(
                "status" => "success",
                "employeeID" => $employeeID,
            );
            return $data;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            return $data;
        }
    }

    // get all time in 
    public function getAllTimeIN($date)
    {
        try {
            $sql = "SELECT * FROM timein WHERE date = :date";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($result);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // get all time out
    public function getAllTimeOut($date)
    {
        try {
            $sql = "SELECT * FROM timeout WHERE date = :date";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            echo json_encode($result);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // record absents 
    public function recordAbsent($rfids, $date, $month)
    {
        $absentIDs = array();
        $empIDs = array();
        foreach ($rfids as $rfid) {
            $data = $this->getEmployeeID($rfid);
            if ($data['status'] == "success") {
                array_push($absentIDs, $data['employeeID']);
            }
        }
        $employeeIDs = $this->getAllEmployeeID();
        for ($i = 0; $i < sizeof($employeeIDs); $i++) {
            array_push($empIDs, $employeeIDs[$i]['employeeID']);
        }
        $absents = array_diff($empIDs, $absentIDs);
        if (!empty($absents)) {
            foreach ($absents as $absent) {
                try {
                    $sql = "INSERT INTO absent (date,employeeID) 
                    VALUES (:date,:employeeID)";
                    $stmt = $this->_db->prepare($sql);
                    $stmt->bindValue(':employeeID', $absent);
                    $stmt->bindValue(':date', $date);

                    $stmt->execute();
                    $count = $stmt->rowCount();

                    $deduction = $this->getDeductRate($month);

                    if ($deduction['status'] == "success") {
                        $totalNow = $this->getAttendenceRate($absent);
                        $perc = ($totalNow['attendance'] - $deduction['rate']);
                        $this->updateEmployee($absent, $perc);
                        $data = array(
                            "status" => "success",
                            "rowcount" => $count,
                        );
                        echo json_encode($data);
                    } else echo json_encode('dedution Unsuccessfull');
                } catch (PDOException $e) {
                    $data = array("status" => "fail");
                    echo json_encode($e);
                }
            }
        } else echo json_encode('No absent Good');
    }

    // update the overall attendace in Employee DB
    public function updateEmployee($id, $val)
    {
        try {
            $sql = "UPDATE employee SET
                attendance = :val
                WHERE employeeID = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':val', $val);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $data = array("status" => "success");
            return $data;
            // echo json_encode($data);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }
    // get All eployee ID 
    public function getAllEmployeeID()
    {
        try {
            $sql = "SELECT employeeID FROM employee";
            $stmt = $this->_db->query($sql);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_values($result);
        } catch (PDOException $e) {
            // $data = array("status" => "fail");
            return json_encode($e);
        }
    }

    // get deduction rate 
    public function getAttendenceRate($id)
    {
        try {
            $sql = "SELECT attendance FROM employee WHERE employeeID = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $attd = (int)$result->attendance;
            $data = array(
                "status" => "success",
                "attendance" => $attd,
            );
            return $data;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // get deduction rate validated
    public function getDeductRate($mon)
    {
        try {
            $sql = "SELECT days FROM workdays WHERE month = :month";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':month', $mon);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            // print_r($result);
            $day = (int)$result->days;
            $rate = (1 / $day) * 100;
            // print_r($rate);
            $data = array(
                "status" => "success",
                "rate" => $rate,
            );
            return $data;
            // echo json_encode($data);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }


    // this will get the desktop data 
    public function getAllDashboardData()
    {
        try {
            $totalEmployee = $this->getTotalTemplate('employee');
            // print_r($totalEmployee);
            $totalComplains = $this->getTotalTemplate('complain');
            // print_r($totalComplains);
            $totalDepartment = $this->getTotalTemplate('department');
            // print_r($totalDepartment);
            $totalAdmin = $this->getTotalTemplate('admin');
            // print_r($totalAdmin);
            $totalAbsent = $this->getTotalAbsent('2020-06-06');
            // print_r($totalAbsent);
            $absentNames = $this->getAllAbsentName('2020-06-06');
            // print_r($absentNames);
            $complains = $this->getComplains();
            // print_r($complains);

            $data = array(
                'empNo' => $totalEmployee,
                'compNo' => $totalComplains,
                'deptNo' => $totalDepartment,
                'admNo' => $totalAdmin,
                'absentNo' => $totalAbsent,
                'absentNo' => $totalAbsent,
                'absentName' => $absentNames,
                'complains' => $complains,
            );
            echo json_encode($data);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    public function getTotalTemplate($table)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM {$table}";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            return $result->total;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }


    public function getTotalAbsent($date)
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM absent WHERE date = :date";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            // print_r($result->total);
            return $result->total;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // get all absentName
    public function getAllAbsentName($date)
    {
        try {
            $sql = "SELECT employeeID FROM absent WHERE date = :date";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':date', $date);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            // print_r($result);
            $names = array();
            foreach ($result as $id) {
                array_push($names, $this->getEmployeeName($id->employeeID));
            }
            // print_r($names);
            return $names;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    public function getEmployeeName($id)
    {
        try {
            $sql = "SELECT name FROM employee WHERE employeeID = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            $employeeName = $result->name;
            // print_r($employeeName);
            return $employeeName;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            return $data;
        }
    }


    public function getComplains()
    {
        try {
            $sql = "SELECT title, description FROM complain";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $data = array();
            foreach ($result as $compain) {
                $tmpar = array('title' => $compain->title, 'desc' => $compain->description);
                array_push($data, $tmpar);
            }
            // print_r($data);
            return $data;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }


    // less payment page 
    public function getAllLeesPaymentEmployee()
    {
        try {
            $sql = "SELECT * FROM employee WHERE attendance < :attendance";
            $stmt = $this->_db->prepare($sql);
            $a = '90';
            $stmt->bindParam(':attendance', $a);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $data = array();

            foreach ($result as $res) {
                $deptName = $this->getDepartmentName($res->departmentID);
                $posName = $this->getPositionName($res->positionID);
                $res->posName = $posName;
                $res->deptName = $deptName;
                array_push($data, $res);
            }
            // print_r($data);
            echo json_encode($data);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // this function will resolve the departmentID with the department name
    public function getDepartmentName($id)
    {
        try {
            $sql = "SELECT name FROM department WHERE departmentID = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            // print_r($result->name);
            return $result->name;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }
    // this function will resolve the position ID with the position name
    public function getPositionName($id)
    {
        try {
            $sql = "SELECT name FROM position WHERE positionID = :id";
            $stmt = $this->_db->prepare($sql);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_OBJ);
            // print_r($result->name);
            return $result->name;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    // this function will return the list of the departments that have been asssigne by the asmin
    public function getAllDept()
    {
        try {
            $sql = "SELECT DISTINCT departmentID from position";
            $stmt = $this->_db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            // print_r($result);
            $data = array();
            foreach ($result as $res) {
                array_push($data, $res->departmentID);
            }
            return $data;
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }

    public function viewAllDeaprtment()
    {
        try {
            $ids = $this->getAllDept();
            $data = array();
            $i = 0;
            foreach ($ids as $id) {
                $sql = "SELECT name FROM position WHERE departmentID = :id";
                $stmt = $this->_db->prepare($sql);
                $stmt->bindParam(':id', $id);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_OBJ);
                $depName = $this->getDepartmentName($id);
                $names = array($depName);
                foreach ($result as $res) {
                    array_push($names, $res->name);
                }
                array_push($data, $names);
            }
            echo json_encode($data);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }


    // hall of fame page 
    public function getAllHallFames()
    {
        try {
            $sql = "SELECT * FROM employee WHERE attendance > :attendance";
            $stmt = $this->_db->prepare($sql);
            $a = '90';
            $stmt->bindParam(':attendance', $a);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            $data = array();

            foreach ($result as $res) {
                $deptName = $this->getDepartmentName($res->departmentID);
                $posName = $this->getPositionName($res->positionID);
                $res->posName = $posName;
                $res->deptName = $deptName;
                array_push($data, $res);
            }
            // print_r($data);
            echo json_encode($data);
        } catch (PDOException $e) {
            $data = array("status" => "fail");
            echo json_encode($data);
        }
    }
}
