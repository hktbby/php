<?php
class database
{
 function opencon(){
return new PDO ('mysql:host=localhost;dbname=loginmethod','root', '');
}
    function check($username, $password){
$con=$this->opencon();
$query = "SELECT * from users WHERE username='".$username."'&& password='".$password."'";
return  $con->query($query)->fetch();
 
    }
    function signup($username, $password, $Firstname, $Lastname, $birthday, $sex){
        $con = $this->opencon();
        $query = $con->prepare("SELECT username FROM users WHERE username = ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();
 
        if ($existingUser){
            return false;
        }
        return $con->prepare("INSERT INTO users (username, password, Firstname, Lastname, birthday, sex) VALUES (?,?,?,?,?,?)") 
        ->execute([$username, $password, $Firstname, $Lastname, $birthday, $sex]);
    }
    function signupUser($username, $password, $firstName, $lastName, $birthday, $sex) {
        $con = $this->opencon();
   
        $query = $con->prepare("SELECT username FROM users WHERE username = ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();
        if ($existingUser){
            return false;
        }
        $query = $con->prepare("INSERT INTO users (username, password, Firstname, Lastname, birthday, sex) VALUES (?, ?, ?, ?, ?,?)");
        $query->execute([$username, $password, $firstName, $lastName, $birthday,$sex]);
        return $con->lastInsertId();
    }function insertAddress($User_id, $city, $province, $street, $barangay) {
        $con = $this->opencon();
        return $con->prepare("INSERT INTO user_address (User_id, user_add_city, user_add_province, user_add_street, user_add_barangay) VALUES (?, ?, ?, ?, ?)")
            ->execute([$User_id, $city, $province, $street, $barangay]);
    }

    function view(){
        $con = $this->opencon();
        return $con->query("SELECT users.User_id, users.username, users.password, users.Firstname, users.Lastname, users.birthday, users.sex, 
        CONCAT(user_address.user_add_street, ' ', user_address.user_add_barangay, ' ', user_address.user_add_city, ' ', user_address.user_add_province)
        as address FROM users JOIN user_address ON users.User_id = user_address.User_id;")->fetchAll();
    }
    function delete($id)
    {
        try {
            $con = $this->opencon();
            $con->beginTransaction();
 
            $qeury = $con->prepare("DELETE FROM user_address WHERE User_id =?");
            $qeury->execute([$id]);
 
            $query2 = $con->prepare("DELETE FROM user WHERE User_id =?");
           
            $con->commit();
            return true;
        } catch(PDOException $e) {
            $con->rollBack();
            return false;
        }       
    }

    function viewdata($id)
    {
    try {
        $con = $this->opencon();
        
        $qeury = $con->prepare("SELECT users.User_id, users.username, users.password, users.Firstname, users.Lastname, users.birthday, users.sex, 
        user_address.user_add_street, user_address.user_add_barangay, user_address.user_add_city, user_address.user_add_province
        FROM users JOIN user_address ON users.User_id = user_address.User_id; WHERE users.User_id= ? ");
        $qeury->execute([$id]);
        return $qeury->fetch();
       
    } catch(PDOException $e) {
        return [];
    }
    }

    function updateUser($User_id, $username, $password, $firstname, $lastname, $birthday, $sex,){
        try {
            $con = $this->opencon();
            $con->beginTransaction();
            $query = $con->prepare("UPDATE users SET Firstname=? Lastname=? birthday=? sex=? username=?
            user_password=? WHERE User_id");

            $query -> execute([$username, $password, $firstname, $lastname, $birthday, $sex, $User_id]);
            $con ->commit();
        } catch(PDOException $e) {
            $con->rollBack();
            return false;
    }

}

function updateUserAddress($user_id, $city, $province, $street, $barangay){
    try {
        $con = $this->opencon();
        $con->beginTransaction();
        $query = $con->prepare("UPDATE user_address=? SET street=? city=? province=? barangay=?
        WHERE User_id");

        $query -> execute([$user_id, $city, $province, $street, $barangay]);
        $con ->commit();
    } catch(PDOException $e) {
        $con->rollBack();
        return false;
    }
}
}
