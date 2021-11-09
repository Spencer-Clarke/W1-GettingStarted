<?php
require(__DIR__."/../../partials/nav.php");?>
<style>
    .input_section{
        position: fixed;
        left: 50%;
        margin-left: -150px;
        border: 1px solid black;
        box-shadow: 5px 5px black;
        padding: 10px;
        background-color: #a2eda1;
        width: 300px;
        height: 200px;
    }

</style>
<form class="input_section" onsubmit="return validate(this)" method="POST">
    <div>
        <label for="username">Username</label>
        <input type="text" name="username"/>
    </div><br>
    <div>
        <label for="email">Email</label>
        <input type="email" name="email" maxlength="30"/>
    </div><br>
    <div>
        <label for="pw">Password</label>
        <input type="password" id="pw" name="password" required minlength="8" />
    </div><br>
    <input type="submit" value="Login" />
</form>
<script>
    function validate(form) {
        //TODO 1: implement JavaScript validation
        //ensure it returns false for an error and true for success

        return true;
    }
</script>
<?php
 //TODO 2: add PHP Code
 if((isset($_POST["email"]) || isset($_POST["username"])) && isset($_POST["password"])){
     $username = se($_POST, "username","",false);
     //get the email key from $_POST, default to "" if not set, and return the value
     $email = se($_POST, "email","", false);
     //same as above but for password
     $password = se($_POST, "password", "", false);
     //TODO 3: validate/use
     $errors = [];
     $hasErrors = false;
     if(empty($email) && empty($username)){
         flash("Email or username must be set", "warning");
         $hasErrors = true;
     }
     //sanitize
     $email = filter_var($email, FILTER_SANITIZE_EMAIL);
     //validate
     if(!filter_var($email, FILTER_VALIDATE_EMAIL) && empty($username)){
         flash("Invalid email", "warning");
         $hasErrors = true;
     }
     if(empty($password)){
         flash("Password must be set", "warning");
         $hasErrors = true;
     }
     if(strlen($password) < 8){
         flash("Password must be at least 8 characters", "warning");
         $hasErrors = true;
     }
     if($hasErrors){ 
     }
     else{
        $db = getDB();
        $stmt = $db->prepare("SELECT id, email, username, password from Users where email = :email");
        try {
            $r = $stmt->execute([":username" => $username]);
            
            if ($r) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    $hash = $user["password"];
                    unset($user["password"]);
                    if (password_verify($password, $hash)) {
                        flash("Welcome $email");
                        $_SESSION["user"] = $user;
                        //lookup potential roles
                        $stmt = $db->prepare("SELECT Roles.name FROM Roles 
                        JOIN UserRoles on Roles.id = UserRoles.role_id 
                        where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                        $stmt->execute([":user_id" => $user["id"]]);
                        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC); //fetch all since we'll want multiple
                        //save roles or empty array
                        if ($roles) {
                            $_SESSION["user"]["roles"] = $roles; //at least 1 role
                        } else {
                            $_SESSION["user"]["roles"] = []; //no roles
                        }
                        die(header("Location: home.php"));
                    } else {
                    }
                } else {
                    flash("User not found", "danger");
                }
            }
        } catch (Exception $e) {
            flash("<pre>" . var_export($e, true) . "</pre>");
        }
     } 
 }
?>
<?php
require_once(__DIR__ . "/../../partials/flash.php");
?>