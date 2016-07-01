<?php
    session_start();
?>

<html>
    <head>
         
        <title> Saiddit  </title>
        <link rel="stylesheet" type="text/css" href="styles.css" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
        <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
        
    </head>
    <body>

    <?php
        /* if sign up button is pressed, then this code will execute */
        if (isset($_POST['submitbuttonsignup'])){
            include("config.php");

            $usernameValue = $_POST['user'];
            $userpasswordValue = $_POST['passw'];
            $userpasswordValuehash = hash('sha256', $userpasswordValue);
        

            $sql = "SELECT * FROM users WHERE username = '$usernameValue'";
            $result = mysqli_query($conn, $sql);
            
             /* Check if username is taken */

            if($result && mysqli_num_rows($result)>0){
            ?>
                <script type="text/javascript">
                    alert("Username already taken"); 
                </script>
            <?php
            
            } else{
                /*Code gets here if username not taken*/ 
                $sql = "INSERT INTO users (username, password)
                VALUES ('$usernameValue','$userpasswordValuehash')";
            
                if ($conn->query($sql) === TRUE) {
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            $conn->close();

            }
        }
        /* if log in button is pressed, then this code will execute */
        if (isset($_POST['submitbuttonlogin'])){

            include("config.php");
 
            // username and password sent from form 
            $myusername=$_POST['userl']; 
            $mypassword=$_POST['passwl']; 
            $mypasswordhash = hash('sha256', $mypassword);
            $sql="SELECT * FROM users WHERE username='$myusername' AND password='$mypasswordhash' LIMIT 1";
            $result=mysqli_query($conn, $sql);
            //If queries retreieved from mysql_query is equal to 1
            /*ie, we found an entry in the database that has the username and password entered into Log in form*/
            if(mysqli_num_rows($result) == 1){
                
            $_SESSION['loggedin'] = true;
            $_SESSION['username_in'] = $myusername;
            //echo"Welcome ".$_SESSION['username_in']."! Redirecting..";
 
            }else{
                ?>
                <script type="text/javascript">
                    alert("Invalid Username or Password"); 
                </script>
            <?php
            }
        
            $conn->close();
    }
    /* if log out button is pressed then this executes */
        if(isset($_POST['submitbuttonlogout'])){
            $_SESSION['loggedin'] = false;
            unset($_SESSION["username_in"]); 
            
        }
    ?> 
        <div class = "header">
            <input type="image" src=redditlogo.png height="65px" >Saiddit            
        </div>
        <!-- Determine if we should display "Log in or Sign up" or "Log out". Dependant on if user is loged in or not.-->
        <div class = "loginsignup">
            <?php 
                if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) { ?>
                    <form method = "post" action = "">
                    <input type="submit"  data-inline="true" data-mini="true" value="Log Out" name="submitbuttonlogout">
                    </form>
            <?php }else{?>
                    Want to join us?<a href = "#LogInPopUp" data-rel="popup" data-inline ="true" data-mini="true"> Log in</a> or <a href = "#SignUpPopUp" data-rel="popup">Sign up</a>
            <?php } ?>
     </div>
        
        <!-- This is the code for the log in pop up box that appears after clicking on log in-->
        <div data-role="popup" class = "ui-content" id="LogInPopUp" style="min-width:250px;">
            <form name = "Loginform" method="post" action="" onsubmit = "return validateLogin()">
                <h3>Login information</h3>
                <label for="usrnm" class="ui-hidden-accessible">Username:</label>
                <input type="text" name="userl" id="usrnm" placeholder="Username">
                <label for="pswd" class="ui-hidden-accessible">Password:</label>
                <input type="password" name="passwl" id="pswd" placeholder="Password">
                <input type="submit" action = "" data-inline="true" value="Log in" name = "submitbuttonlogin">
            </form>
        </div>
        
        <script type="text/javascript">
            /*T his code executes if the log in button is pressed. It checks to see if the fields are filled in */
         function validateLogin() {

             //  validateLogin function starts here
             var username = document.forms["Loginform"]["usrnm"].value;
             var password1 = document.forms["Loginform"]["pswd"].value;
            //For some reason I cant get it to work in one if statement with lots of ORs
                if (username==null || username==""){
                    alert("Please fill out all fields");
                    return false;
                }
                else if (password1==null || password1==""){
                        alert("Please fill out all fields");
                        return false;
                }
         }
        </script>

        <!-- This code is for the sign up pop box that appears after clicking on sign up-->
        <div data-role="popup" class = "ui-content" id="SignUpPopUp" style="min-width:250px;">
            <form name = "SignUpForm" method="post" onsubmit=" return validateSignUp()">
                <h3>Sign Up Information</h3>
                <label for="usrnm" class="ui-hidden-accessible">Username:</label>
                <input type="text" name="user" id="usrnm" placeholder="Username">
                <label for="pswd" class="ui-hidden-accessible">Password:</label>
                <input type="password" name="passw" id="pswd" placeholder="Password">
                <label for="pswd2" class="ui-hidden-accessible">Confirm Password:</label>
                <input type="password" name="passw2" id="pswd2" placeholder="Confirm Password">
                <input type="submit" action="" data-inline="true" value="Sign Up" name="submitbuttonsignup">
            </form>
        </div>
        
        <script type="text/javascript">
            /* This code executes when sign up button is pressed in the pop up box. It checks to see if confirm password and password fields match, and if all fields are filled in */
         function validateSignUp() {

             //  validateForm function starts here
             var username = document.forms["SignUpForm"]["usrnm"].value;
             var password1 = document.forms["SignUpForm"]["pswd"].value;
             var password2 = document.forms["SignUpForm"]["pswd2"].value;
            //For some reason I cant get it to work in one if statement with lots of ORs
                if (username==null || username==""){
                    alert("Please fill out all fields");
                    return false;
                }
                else if (password1==null || password1==""){
                        alert("Please fill out all fields");
                        return false;
                }
                else if (password2==null || password2==""){
                        alert("Please fill out all fields");
                        return false;
                }
                else if (password1 != password2){
                        alert("Passwords don't match")
                        return false;
                }

         }
        </script>
  
            
        <!-- The horizontal menu -->
        <div id = "nav">
        <ul class = "default_subsaiddits">
            <li class = "subOption"><a href ="#">HOME</a></li>
            <li class = "subOption"><a href ="#">FUNNY</a></li>
            <li class = "subOption"><a href ="#">PICS</a></li>
            <li class = "subOption"><a href ="#">SHOWERTHOUGHTS</a></li>
            <li class = "subOption"><a href ="#">VIDEOS</a></li>
            <li class = "subOption"><a href ="#">AWW</a></li>
            <li class = "subOption"><a href ="#">GAMING</a></li>
            <li class = "subOption"><a href ="#">NEWS</a></li>
            <li class = "subOption"><a href ="#">MOVIES</a></li>


        </ul>
        </div>
        <!-- This links-->
        <div>
        <ol type="1" >
            <li class = "showing"><a href ="#">Cat throws dog off of roof!</a></li>
            <li class = "showing"><a href ="#">Some more cat memes</a></li>
            <li class = "showing"><a href ="#">UK Votes to Seperate from EU</a></li>
            <li class = "showing"><a href ="#">Cavs win first championship</a></li>
            <li class = "showing"><a href ="#">How to tie a shoe, the fast way</a></li>
            <li class = "showing"><a href ="#">Kids arguing about stuff</a></li>
            <li class = "showing"><a href ="#">Instant karma</a></li>
            <li class = "showing"><a href ="#">Why do we have a pink toe?</a></li>
            <li class = "showing"><a href ="#">Gravitational waves detected, AGAIN</a></li>
            <li class = "showing"><a href ="#">MEMES ALL DAY EVERYDAY</a></li>

        </ol>
        </div>

        
    </body>

</html>
