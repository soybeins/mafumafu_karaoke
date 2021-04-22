<?php

function connectDB(){
    $server= "localhost";
    $user= "root";
    $password= "";
    $db= "mafumafu_karaoke";

    $con = mysqli_connect($server,$user,$password,$db);
    
    return $con;
}

//===================================================== E M P L O Y E E  F U N C T I O N S ==============================================

//  ======================EMPLOYEE LOG-IN ====================
function empLogin($email,$pass){
    $con = connectDB();
    $ret = 0;
    $query = "select employee_id,email,password from employee where email = '$email' && password = '$pass'";

    $res = mysqli_query($con,$query);

    if(mysqli_num_rows($res) > 0){
        $ret = 1;
    }

    return $ret;
}

function empDetails($email,$pass){
    $con = connectDB();
    $ret = 0;
    $row;
    $query = "select * from employee where email = '$email' && password = '$pass'";

    $res = mysqli_query($con,$query);

    if(mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
    }

    return $row;
}

function empDetail($empId){
    $con = connectDB();
    $ret = 0;
    $row;
    $query = "select * from employee where employee_id = $empId ";

    $res = mysqli_query($con,$query);

    if(mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
    }

    return $row;
}


// =========================================================== 



//  ====================== SELECT FUNCTIONS ====================

function isCard($uuid){
    $con = connectDB();
    $ret = 0;
    $query = "select count(card_id) as count,card_uuid from card where card_uuid = $uuid";

    $res = mysqli_query($con,$query);

    if( mysqli_num_rows($res) > 0 ){
        $row = mysqli_fetch_assoc($res);
        if( $row['card_uuid'] == $uuid){
            $ret = 1;
        }
    }
    return $ret;
}

function isAdmin($empId){
    $con = connectDB();
    $ret = 0;
    $res = mysqli_query($con,"select * from employee where employee_id = $empId");

    if(mysqli_num_rows($res) > 0){
        $row = mysqli_fetch_assoc($res);
        if( $row['admin'] == 'T'){
            $ret = 1;
        }
    }

    return $ret;
}

function displayCards(){
    $con = connectDB();
    $query = "select * from card";

    $res = mysqli_query($con,$query);
   
    return $res;
}

function displayEmployees(){
    $con = connectDB();
    $query = "select * from employee";

    $res = mysqli_query($con,$query);
   
    return $res;
}

function displayEmployeeById($empId){
    $con = connectDB();

    $res  = mysqli_query($con,"select * from employee where employee_id = $empId ");

    return $res;
}


function displayEmployee($name){
    $con = connectDB();

    $res  = mysqli_query($con,"select * from employee where concat(firstname,' ',middlename,' ',lastname) like '%$name%' ");

    return $res;
}

function displayCard($uuid){
    $con = connectDB();
    $query = "select * from card where card_uuid = $uuid";

    $res = mysqli_query($con,$query);
   
    return $res;
}

function displayTransactions(){
   $con = connectDB();
   $query="select * from transaction";

   $res = mysqli_query($con,$query);
   
   return $res; 
}

function displayRooms(){
    $con = connectDB();
    $query="select * from room";
 
    $res = mysqli_query($con,$query);
    
    return $res;  
}

function displayRoom($key){
    $con = connectDB();
    $query="select * from room where concat(room_name,' ',type,' ',status) like '%$key%' ";
 
    $res = mysqli_query($con,$query);
    
    return $res;  
}

function getAvailableRooms(){
    $con = connectDB();

    $query = "select * from room where status = 'unoccupied'";

    return mysqli_query($con,$query);
}

function getRoomtime($roomId){
    $con = connectDB();

    $query = "select roomtime.roomtime_id as roomtime_id from roomtime inner join room on roomtime.room_id = room.room_id where roomtime.room_id = $roomId && roomtime.flag = 'T'";

    return mysqli_query($con,$query);
}

function getRoomtime2($roomtimeId,$roomId){
    $con = connectDB();

    $query = "select roomtime.roomtime_id,roomtime.time_end as time_end from roomtime inner join room on roomtime.room_id = room.room_id 
            where roomtime.room_id = $roomId && roomtime.roomtime_id = $roomtimeId";

    return mysqli_query($con,$query);
}

function getRoomtimeId($room,$date,$time_start){
    $con = connectDB();

    $query = "select roomtime_id from roomtime where room_id = $room && date = '$date' && time_start = '$time_start'";

    return mysqli_query($con,$query);
}

function displayRoomtime(){
    $con = connectDB();

    return mysqli_query($con,"select * from roomtime rt,room r where rt.room_id = r.room_id");
}


// =========================================================== 

//  ====================== INSERT FUNCTIONS ====================

function createCard($card_uuid){
    $cards = displayCards();
    $flag = 0;
    while($row = mysqli_fetch_assoc($cards)){
        if($row['card_uuid'] == $card_uuid){
            $flag = 1;
        }
    }

    if($flag == 0){
        $con = connectDB();
        $ret = 0;
        $date = date("Y-m-d");
        $query = "insert into card(card_uuid,vip,date_registered,last_updated) values ($card_uuid,'F','$date','$date')";
    
        $res = mysqli_query($con,$query);
    
        return $res;
    }else{
        return 0;
    }
  
}

function createTransaction($uuid,$description,$vip,$total,$empId){
    $con = connectDB();
    $date = date("Y-m-d H:i:s");
    $query = "insert into transaction(card_uuid,description,date,vip,total,employee_id)
         values ($uuid,'$description','$date','$vip',$total,$empId)";

  
    return mysqli_query($con,$query) or die(mysqli_error($con));
}

function createTransactionRoom($uuid,$description,$vip,$total,$roomId,$roomtimeId,$empId){
    $con = connectDB();
    $date = date("Y-m-d H:i:s");
    $query = "insert into transaction(card_uuid,description,date,vip,total,room_id,roomtime_id,employee_id) 
                            values ($uuid,'$description','$date','$vip',$total,$roomId,$roomtimeId,$empId)";

    return mysqli_query($con,$query) or die(mysqli_error($con));
}

function createRoomtime($room,$hours,$start_time){
    $con = connectDB();
    $curr_date = date('Y-m-d');
    $end_time = strtotime("+$hours hours", strtotime($start_time));
    $end = date('H:i:s',$end_time); 
    $query = "insert into roomtime(room_id,date,time_start,time_end,flag) values ($room,'$curr_date','$start_time','$end','T')";

    return mysqli_query($con,$query);
}

function createEmployee($position,$firstname,$middlename,$lastname,$email,$address,$password,$gender){
    $con = connectDB();
    $date = date("Y-m-d");
    $res = 0;
    ($position == "Admin")?$admin = "T":$admin = "F";

    $query = "insert into employee(admin,firstname,middlename,lastname,gender,address,email,password,date_employed,position)
                             values ('$admin','$firstname','$middlename','$lastname','$gender','$address','$email','$password','$date','$position')";

    $res = mysqli_query($con,$query);

    return $res;
}

function createRoom($name,$type,$capacity){
    $con = connectDB();
    $status = "unoccupied";
    return mysqli_query($con,"insert into room(room_name,type,capacity,status) values ('$name','$type',$capacity,'$status')");
}


// =========================================================== 

// ==================== UPDATE FUNCTIONS =====================

function reloadCard($balance,$uuid){
    $res = displayCard($uuid);
    $con = connectDB();
    $row = mysqli_fetch_assoc($res);
    $balance += $row['balance'];
    $query = "update card set balance = $balance where card_uuid = $uuid";

    $res = mysqli_query($con,$query);

    return $res;
}

function extendRoom($hours,$roomId,$roomtimeId){
    $con = connectDB();
    $curr_date = date('Y-m-d');
    $row = getRoomtime2($roomtimeId,$roomId);
    $time_end = mysqli_fetch_assoc($row);
    $end_time = strtotime("+$hours hours", strtotime($time_end['time_end']));
    $end = date('H:i:s',$end_time);

    return mysqli_query($con,"update roomtime set date = '$curr_date', time_end = '$end' where room_id = $roomId && roomtime_id = $roomtimeId");
}

function updateBalance($balance,$uuid){
    $con = connectDB();

    $query = "update card set balance = $balance where card_uuid = $uuid";
    $res = mysqli_query($con,$query);
    return $res;
}

function vip($uuid){
    $con = connectDB();
    $query = "update card set vip = 'T' where card_uuid = $uuid";
    $res = mysqli_query($con,$query);
    return $res;
}

function updateRoomStatus($room){
    $con = connectDB();
    $query = "update room set status = 'occupied' where room_id = $room";
    $res = mysqli_query($con,$query);
    return $res;
}

function freeRoomStatus($room){
    $con = connectDB();
    $query = "update room set status = 'unoccupied' where room_id = $room";
    $res = mysqli_query($con,$query);
    return $res;
}

function updateRoomtimeStatus($curr_date,$curr_time){
    $roomtime = displayRoomtime();
    $con = connectDB();
    $res = 0;
    while($row = mysqli_fetch_assoc($roomtime)){
        if($row['flag'] == 'T'){
            if($curr_date >= $row['date']){
                if($curr_time >= $row['time_end']){
                    $res = mysqli_query($con,"update roomtime set flag = 'F' where roomtime_id = ".$row['roomtime_id']);
                    if($res){
                        freeRoomStatus($row['room_id']);
                    }
                
                }
            }           
        }

    }
    return $res;
}

function updateEmployee($empId,$position,$firstname,$middlename,$lastname,$email,$address,$password,$gender){
    $con = connectDB();
    ($position == "Admin")?$admin = "T":$admin = "F";
    $query = "update employee set admin= '$admin',firstname='$firstname',middlename='$middlename', 
                        lastname='$lastname',gender='$gender',address='$address',email='$email', 
                        password='$password',position='$position' where employee_id = $empId";

    return $res = mysqli_query($con,$query);
}

function updateRoom($roomId,$name,$type,$capacity){
    $con = connectDB();
    $query = "update room set room_name = '$name', type = '$type',capacity = '$capacity' where room_id = $roomId";
    return mysqli_query($con,$query);
}

// =========================================================== 


// ==================== UPDATE FUNCTIONS =====================

function deleteCard($uuid){
    $con = connectDB();
    $query = "delete from card where card_uuid = $uuid";

    $res = mysqli_query($con,$query);
    return $res;
}

function deleteEmployee($empId){
    $con = connectDB();

    $res = mysqli_query($con,"delete from employee where employee_id = $empId");

    return $res;
}

function deleteRoom($roomId){
    $con = connectDB();
    $query = "delete from room where room_id = $roomId";

    $res = mysqli_query($con,$query);
    return $res;
}



// =========================================================== 

//===================================================== E N D  O F  E M P L O Y E E  F U N C T I O N S ==============================================
?>