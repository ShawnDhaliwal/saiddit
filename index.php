
<?php
    session_start();
?>

<!DOCTYPE html>

<html lang="en">

    <head >
         
        <title > Saiddit  </title>
        <link rel="stylesheet" type="text/css" href="styles.css" />
        <link rel="stylesheet" type="text/css" href="sweetalert.css"/>
        <script src = "sweetalert.min.js"></script>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css">
        <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
        <script src="http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js"></script>
        <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.min.js"></script>
        <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/additional-methods.min.js"></script>
        <link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

        <!-- I have no idea what this function does but jquery tabs needs it-->
        <script>
            $(function() {
            $( "#tabs" ).tabs();
        });
        </script>
        
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
                    swal("Registration Failed","Username already taken","error"); 
                </script>
            <?php
            
            } else{
                /*Code gets here if username not taken*/ 
                $sql = "INSERT INTO users (username, password)
                VALUES ('$usernameValue','$userpasswordValuehash')";
            
                if ($conn->query($sql) === TRUE) {
                    $sql = "SELECT * FROM users WHERE username = '$usernameValue'";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    if(mysqli_num_rows($result)==1){
                        
                        $user_id = $row["id"];
                    }
                    $sql = "SELECT * FROM subsaiddit WHERE is_default = 1";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    if(mysqli_num_rows($result)>0){
                        //subscribe new user to defualt subsaiddits
                        $subscribedsubsaiddits = array();
                        array_push($subscribedsubsaiddits, $row['id']);
                        while($row = mysqli_fetch_assoc($result)) {
                            array_push($subscribedsubsaiddits, $row['id']);
                        }
                        $x = 0;
                        
                        while($x < count($subscribedsubsaiddits)){
                            
                            $sql = "INSERT INTO subscribe (user_id, subsaiddit_id) VALUES ('$user_id','$subscribedsubsaiddits[$x]')";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_fetch_assoc($result);
                            $x++;
         
                        }
                    }
                ?> <script type="text/javascript">
                    swal("Registration Successful","","success");
                   </script>
                <?php
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            $conn->close();
        }
        /* if log in button is pressed, then this code will execute */
        if (isset($_POST['submitbuttonlogin'])){
            include("config.php");
 
            // username and password entered into form
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
                    swal("Try Again","Invalid Username or Password","error"); 
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
        
    /* if add friend button is pressed. This adds the friend to the friends table in mysql */
        if(isset($_POST['addfriendsubmit'])){
            //Get userid of logged in user
            include("config.php");
            $user = $_SESSION['username_in'];
            $sql="SELECT * FROM users WHERE username='$user'";
            $result=mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $userid = $row["id"];
            //get the id of the friend the user is adding
            $addfriend = ($_POST['addfriendl']);
            $sql="SELECT * FROM users WHERE username='$addfriend'";
            $result=mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            //if id of the friend the user is trying to add is found
            if(mysqli_num_rows($result) == 1){
                $friendid = $row["id"];
                $sql="SELECT * FROM friends WHERE user_id='$userid' AND friend_id ='$friendid'";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                //if user is already added
                if(mysqli_num_rows($result) == 1){
                    ?>
                    <script type = "text/javascript">
                    swal("Oops","Friend is already added","error");
                    </script>
                    <?php
                } else{
                    //$row["id"] is the id of the user we found through the query. 
                    $sql = "INSERT INTO friends (user_id, friend_id)
                    VALUES ('$userid','$friendid')";
                
                    if ($conn->query($sql) === TRUE) {
                    ?>
                    <script type ="text/javascript">
                        swal("Success","User is now your friend","success");
                    </script>
                    <?php
                    } else {
                    ?>
                    <script type ="text/javascript">
                        swal("Error","Something went wrong","error");
                    </script>
                    <?php
                    }
         
                    $conn->close();
                }
            } else {
                ?>
                <script type="text/javascript">
                    swal("Try again","Can't find Username","error");
                </script>
            <?php
                
            }
        }
        
        if(isset($_POST['createsubsaiddit'])){
                include("config.php");
                $user = $_SESSION['username_in'];
                $subsaiddit_title = ($_POST['subsaidditTitle']);
                $subsaiddit_desc = ($_POST['desc']);
            
                $sql = "INSERT INTO subsaiddit (title, description,creator)
                VALUES ('$subsaiddit_title','$subsaiddit_desc','$user')";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                
                $sql="SELECT * FROM users WHERE username='$user'";
                $result=mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $userid = $row["id"];
            
                $sql="SELECT * FROM subsaiddit WHERE title='$subsaiddit_title'";
                $result=mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                $subsaidditid = $row["id"];
            
                $sql = "INSERT INTO subscribe (user_id, subsaiddit_id)
                VALUES ('$userid','$subsaidditid')";
                $result = mysqli_query($conn, $sql);
                $row = mysqli_fetch_assoc($result);
                
        }
        
        if(isset($_POST['subscribebutton'])){
            include ("config.php");
            $subsaidditid = $_POST['subscribebuttonl'];
            
            $user = $_SESSION['username_in'];
            $sql="SELECT * FROM users WHERE username='$user'";
            $result=mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $userid = $row["id"];
            
          
            
            $sql = "INSERT INTO subscribe (user_id, subsaiddit_id)
            VALUES ('$userid','$subsaidditid')";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            
            ?>
            <script type="text/javascript">
                swal("Success","You have subscribed to a new subsaiddit","success");
            </script>
            <?php   
        }
        
        if(isset($_POST['deletePostbutton'])){
            include("config.php");
            $postid = $_POST['deletePostbuttonl'];
            $sql = "DELETE FROM post WHERE id = '$postid'";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            ?> 
            <script type="text/javascript">
            swal("Post Deleted","","success");
            </script>
            <?php
            
        }
            
         if(isset($_POST['newpostsubmit'])){
            include ('config.php');
             
            $user = $_SESSION['username_in'];
            $sql="SELECT * FROM users WHERE username='$user'";
            $result=mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
             
            $userid = $row["id"];
            $subsaidditname = $_POST['newpostTosubl'];
            $title = $_POST['newpostTitlel'];
            $url = $_POST['newpostURLl'];
            $description = $_POST['newpostDescriptionl'];
             
            //get subsaiddit id
             
            $sql="SELECT * FROM subsaiddit WHERE title='$subsaidditname'";
            $result=mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $subsaidditid = $row["id"];

            //insert into post
            $sql = "INSERT INTO post (title, url,text)
            VALUES ('$title','$url','$description')";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
             
            //get post id
            $sql="SELECT * FROM post WHERE title='$title'";
            $result=mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $postid = $row["id"];
             
            //insert into post belongs to
             
            $sql = "INSERT INTO postbelongs (subsaiddit_id, post_id)
            VALUES ('$subsaidditid','$postid')";
            $result = mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            
            ?> 
            <script type="text/javascript">
            swal("Post successful","","success");
            </script>
            <?php
        
         }
        
        
        //if remvoe friend submit button is pressed 
        if(isset($_POST['removefriendsubmit'])){
            //get current loged in user id
            include("config.php");
            $user = $_SESSION['username_in'];
            $sql="SELECT * FROM users WHERE username='$user'";
            $result=mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $userid = $row["id"];
            //get the id of the friend the user is trying to remove
            $removefriend = ($_POST['removefriendl']);
            $sql="SELECT * FROM users where username='$removefriend'";
            $result=mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            $removefriendid = $row["id"];
            //get the data from the friends table
            $sql="SELECT * FROM friends WHERE user_id='$userid' AND friend_id = '$removefriendid'";
            $result=mysqli_query($conn, $sql);
            $row = mysqli_fetch_assoc($result);
            if(mysqli_num_rows($result) == 1){
                $sql = "DELETE FROM friends WHERE user_id = '$userid' AND friend_id = '$removefriendid'";
                
                if ($conn->query($sql) === TRUE) {
                ?>
                    <script type ="text/javascript">
                        swal("Success","User removed from friends list","success");
                    </script>
                <?php
                } else {
                 ?>
                    <script type ="text/javascript">
                        swal("Error","Something went wrong","error");
                    </script>
                <?php
                }
         
            $conn->close();
            } else {
                ?>
                <script type="text/javascript">
                    swal("User not found","User is not in your friends list", "error");
                </script>
            <?php
                
            }
        }
    ?> 
        
        <div class = "header">
            <input type="image" src=redditlogo.png height="65px" >Saiddit            
        </div>
        <!-- Determine if we should display "Log in or Sign up" or "Log out". Dependant on if user is loged in or not.-->
        <br>
        <?php
        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) { ?>
        <form method = "post" action="">
        <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#"><?php echo $_SESSION['username_in']?></a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#allsubsaidditspopup" data-rel="popup">All Subsaiddits</a></li>
                    <li><a href="#mysubsaidditspopup" data-rel="popup">Subscribed</a></li>
                    <li><a href="#createmysubsaidditspopup" data-rel="popup">New Subsaiddit</a></li>                                    
                    <li><a href="#newpostpopup" data-rel="popup" >New Post</a></li>
                    <li><a href="#friendspopup" data-rel="popup">View Friends</a></li>
                    <li><a href = "#addfriendspopup" data-rel="popup">Add Friend</a></li>
                    <li><a href = "#removefriendspopup" data-rel="popup">Remove Friend</a></li>
                    <li><a href = "#LogOutPopUp" data-rel="popup">Log Out</a></li>
                </ul>
            </div>
        </nav>
        </form>
        <?php } else { ?>
            <form method = "post" action="">
            <nav class="navbar navbar-default">
            <div class="container-fluid">
                <div class="navbar-header">
                    <a class="navbar-brand" href="#">Welcome</a>
                </div>
                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#allsubsaidditspopup" data-rel="popup">All Subsaiddits</a></li>
                    <li><a href="#SignUpPopUp" data-rel="popup">Sign Up</a></li>
                    <li><a href = "#LogInPopUp" data-rel="popup">Log In</a></li>
                </ul>
            </div>
        </nav>
        </form>
        <?php } ?>
        <div data-role="popup" class = "ui-content" id="createmysubsaidditspopup" style="min-width:250px;">
            
             <form name = "SubsaidditForm" method="post" action="" >
                <h3>Subsaiddit information</h3>
                <label for="subsaidditTitle" class="ui-hidden-accessible">Title:</label>
                <input type="text" name="subsaidditTitle" id="subsaidditTitle" placeholder="Title" required>
                <label for="desc" class="ui-hidden-accessible">Description:</label>
                <textarea cols="40" rows="5" name="desc" id="desc" placeholder = "Short description.."></textarea>
                <input type="submit" action = "" data-inline="true" value="Create" name = "createsubsaiddit">
            </form>
            
        </div>
        <div data-role="popup" class = "ui-content" id="allsubsaidditspopup" style="min-width:250px;">
                <h3>All Subsaiddits</h3>
                <?php 
                    include ("config.php");
                    $sql = "SELECT * FROM subsaiddit";
                    $result = mysqli_query($conn, $sql);
                    //save friend id values into array
                    while($row = mysqli_fetch_assoc($result)) {
                        $saidditname = $row["title"];
                        $isdefault = $row["is_default"];
                        if($isdefault == 0){
                        echo "<li style='list-style-type: none;''><a href='#'style = 'color: black'>".$saidditname."</a></li>";
                        }
                        else{
                        echo "<li style='list-style-type: none;''><a href='#'>".$saidditname."</a></li>";
                        }
                    }
                    
            
                ?>
            
        </div>

        <div data-role="popup" class = "ui-content" id="mysubsaidditspopup" style="min-width:250px;">
                <h3>Subscribed Subsaiddits</h3>
                <?php 
                    include ("config.php");
                    $user = $_SESSION['username_in'];
                    $sql = "SELECT * FROM users WHERE username = '$user'";
                    $result = mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $userid = $row["id"];
                    $sql = "SELECT * FROM subscribe WHERE user_id = '$userid'";
                    $result = mysqli_query($conn, $sql);
                    $subscribesubsaidditids = array();
                    //save friend id values into array
                    while($row = mysqli_fetch_assoc($result)) {
                        array_push($subscribesubsaidditids, $row['subsaiddit_id']);
                    }
                    
                    $x = 0;
                    while($x <count($subscribesubsaidditids)){
                        
                        $sql = "SELECT * FROM subsaiddit WHERE id = '$subscribesubsaidditids[$x]'";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);
                        $subsaidditname = $row["title"];
                        $isdefault = $row["is_default"];
                        if($isdefault == 0){
                        echo "<li style='list-style-type: none;'><a href='#'style = 'color:black'>".$subsaidditname."</a></li>";
                        }else{
                        echo "<li style='list-style-type: none;'><a href='#'>".$subsaidditname."</a></li>";
                        }
                        $x++;        
                    }
                        
            
            
                ?>
        </div>
        
        <div data-role="popup" class = "ui-content" id="LogInPopUp" style="min-width:250px;">
            <form name = "Loginform" method="post" action="" >
                <h3>Login information</h3>
                <label for="usrnm" class="ui-hidden-accessible">Username:</label>
                <input type="text" name="userl" id="usrnm" placeholder="Username" required>
                <label for="pswd" class="ui-hidden-accessible">Password:</label>
                <input type="password" name="passwl" id="pswd" placeholder="Password" required>
                <input type="submit" action = "" data-inline="true" value="Log in" name = "submitbuttonlogin">
            </form>
        </div>
        
        <div data-role="popup" class = "ui-content" id="LogOutPopUp" style="min-width:250px;">
            <form name = "LogOutform" method="post" action="" >
                <h3>Are you sure?</h3>
                <input type="submit" action = "" data-inline="true" value="Yes" name = "submitbuttonlogout">
                <input type="submit" action = "" data-inline="true" value="Cancel" name = "cancelbuttonlogout">
            </form>
        </div>
        
        <div data-role="popup" class = "ui-content" id="addfriendspopup" style="min-width:250px;">
            <form name = "addfriendform" method="post" action="">
                <label for="addfriendusrnm" class="ui-hidden-accessible">Username:</label>
                <input type="text" name="addfriendl" id="addfriend" placeholder="Username" required>
                <input type="submit" action = "" data-mini="true" data-inline="true" value="Add" name = "addfriendsubmit">
            </form>
        </div>
        
        <div data-role="popup" class = "ui-content" id="removefriendspopup" style="min-width:250px;">
            <form name = "removefriendform" method="post" action="">
                <label for="removefriendusrnm" class="ui-hidden-accessible">Username:</label>
                <input type="text" name="removefriendl" id="removefriend" placeholder="Username" required>
                <input type="submit" action = "" data-mini="true" data-inline="true" value="Remove" name = "removefriendsubmit">
            </form>
        </div>
        
        <div data-role="popup" class = "ui-content" id="friendspopup" style="min-width:250px;">
              <h3>Your Friends</h3>

                <?php
                    include("config.php");
                    //get current loged in user id
                    $user = $_SESSION['username_in'];
                    $sql="SELECT * FROM users where username='$user'";
                    $result=mysqli_query($conn, $sql);
                    $row = mysqli_fetch_assoc($result);
                    $userid = $row["id"];
                    //get all their friends from friends table
                    $sql ="SELECT * FROM friends where user_id='$userid'";
                    $result=mysqli_query($conn,$sql);
                    $friendids = array();
                    //save friend id values into array
                    while($row = mysqli_fetch_assoc($result)) {
                        array_push($friendids, $row['friend_id']);
                    }
                    //count friends
                    $totalfriends = count($friendids);
                    if($totalfriends == 0){
                        
                        echo"No Friends";
                    }
                    $x = 0;
                    //go through array and display on page their friends name
                    while($x<$totalfriends){
                        $idfriend = $friendsids[$x];
                        //get friends username from their id
                        $sql = "SELECT * FROM users WHERE id ='$friendids[$x]'";
                        $result = mysqli_query($conn, $sql);
                        $row = mysqli_fetch_assoc($result);
                        $friendname = $row["username"];
                        echo "<li style='list-style-type: none;'><a href='#'>".$friendname."</a></li>";
                        $x++;        
                    }     
                
                ?>

        </div>

        <!-- This code is for the sign up pop box that appears after clicking on sign up-->
        <div data-role="popup" class = "ui-content" id="SignUpPopUp" style="min-width:250px;">
            <form name = "SignUpForm" method="post" onsubmit=" return validateSignUp()">
                <h3>Sign Up Information</h3>
                <fieldset>
                <label for="usrnm" class="ui-hidden-accessible" requied>Username:</label>
                <input type="text" name="user" id="usrnm" placeholder="Username" required>
                <label for="pswd" class="ui-hidden-accessible">Password:</label>
                <input type="password" name="passw" id="pswd" placeholder="Password" required>
                <label for="pswd2" class="ui-hidden-accessible" >Confirm Password:</label>
                <input type="password" name="passw2" id="pswd2" placeholder="Confirm Password" required>
                <input type="submit" action="" data-inline="true" value="Sign Up" name="submitbuttonsignup">
                </fieldset>
            </form>
        </div>
        
        <script type="text/javascript">
            /* This code executes when sign up button is pressed in the pop up box. It checks to see if confirm password and password fields match, and if all fields are filled in */
         function validateSignUp() {
             //  validateForm function starts here
             var password1 = document.forms["SignUpForm"]["pswd"].value;
             var password2 = document.forms["SignUpForm"]["pswd2"].value;
            if (password1 != password2){
                        swal("Registration Failed","Passwords don't match","error")
                        return false;
                }
         }
        </script>
        
        <div class="container-fluid">
            <ul class="nav nav-tabs">
                <?php 
                    $sql = "SELECT * FROM subsaiddit";
                    $result = mysqli_query($conn, $sql);
                    $subsaidditnames = array();
                    $subsaidditdesc = array();
                    $subsaidditids = array();
                    while($row = mysqli_fetch_assoc($result)) {
                        array_push($subsaidditnames, $row["title"]);
                        array_push($subsaidditdesc, $row["description"]);
                        array_push($subsaidditids, $row["id"]);
                    }
                    ?><li class='active'><a data-toggle='tab' href='#home'>HOME</a></li><?php
                    $x = 0;
                    $y = 1;
                    while($x<count($subsaidditnames)){
                        ?><li><a data-toggle='tab' href='#tabs-<?php echo $y; ?>'><?php echo $subsaidditnames[$x]; ?></a></li>
                        <?php
                        $x++;
                        $y++;        
                    }                    
                ?>

            </ul>
            <div class="tab-content">
                    <div id="home" class = "tab-pane fade in active">
                        <h3>HOME</h3>
                        <ul class = "listset">
                        <?php 
                        include ("config.php");
                        $sql = "SELECT * FROM post";
                        $result = mysqli_query($conn, $sql);
                        if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
                           //get user id
                            $user = $_SESSION['username_in'];
                            $sql = "SELECT * FROM users WHERE username = '$user'";
                            $result = mysqli_query($conn, $sql);
                            $row = mysqli_fetch_assoc($result);
                            $userid = $row["id"];
                            
                            /*go to subscribe table and store ids of the subsaiddits the user is subcribed too */
                            
                            $sql = "SELECT * FROM subscribe WHERE user_id = '$userid'";
                            $result = mysqli_query($conn, $sql);
                            $subscribed_subs_ids = array();
                            while($row = mysqli_fetch_assoc($result)) {
                                array_push($subscribed_subs_ids, $row["subsaiddit_id"]);
                            }
                            
                            /* Go to the postbelongs to and get all the postids that belong to a subsaiddit the user is subcribed too*/
                            
                            
                            $postids = array();
                            $x=0;
                            while($x<count($subscribed_subs_ids)){
                                $sql = "SELECT * FROM postbelongs WHERE subsaiddit_id = '$subscribed_subs_ids[$x]'";
                                $result = mysqli_query($conn, $sql);
                                while($row = mysqli_fetch_assoc($result)) {
                                    array_push($postids, $row["post_id"]);
                                }
                                $x++;
                            }
                            $x= 0;
                            while($x<count($postids)){
                                $sql = "SELECT * FROM post WHERE id = '$postids[$x]'";
                                $result = mysqli_query($conn, $sql);
                                 while($row = mysqli_fetch_assoc($result)) {
                                     ?>
                                    <li>
                                    <img src="http://lorempixum.com/100/100/nature/1" >
                                    <h3><?php echo $row['title'];?></h3>
                                    <p><?php echo $row['text'] ?></p> 
                                    </li>
                                    <?php  
                                }
                                $x++;
                            }
                            
                        } else {
                            while($row = mysqli_fetch_assoc($result)) {
                            ?>
                            <li>
                            <img src="http://lorempixum.com/100/100/nature/1" >
                            <h3><?php echo $row['title'];?></h3>
                            <p><?php echo $row['text'] ?></p> 
                            </li>

                            <?php    
                            }                            
                        }

                        ?>
                        </ul>
                    </div>
                
                    <?php 
                        $x = 0;
                        $y = 1;
                        while($x<count($subsaidditnames)){
                            ?>
                            <div id="tabs-<?php echo $y;?>" class = "tab-pane fade">  
                                <h3><?php echo $subsaidditnames[$x]; ?><span style="float: right"><form method="post">
                                    <input type="hidden" name="subscribebuttonl" value="<?php echo $subsaidditids[$x]?>"/>
                                    <input type="submit" data-inline="true" value="Subscribe" name="subscribebutton"></form>
                                    </span></h3>
                                    <p><?php echo $subsaidditdesc[$x];?></p>
                                    <ul class = "listset">
                                    <?php 
                                    include ("config.php");
                                    $z = 0;
                                    $sql="SELECT * FROM postbelongs WHERE subsaiddit_id='$subsaidditids[$x]'";
                                    $result=mysqli_query($conn, $sql);
                                    $postids = array();
                                    
                                    while($row = mysqli_fetch_assoc($result)) {
                                        array_push($postids, $row["post_id"]);
                                    }
      
                                    while($z < count($postids)){
                                    $sql="SELECT * FROM post WHERE id='$postids[$z]'";
                                    $result=mysqli_query($conn, $sql);
                                     while($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                        
                                        <form method = "post" >
                                        <li>
                                            <img src="http://lorempixum.com/100/100/nature/1" >
                                            <h3><?php echo $row['title'];?></h3>
                                            <p><?php echo $row['text'] ?></p>
                                            <div data-role="button" data-type="submit" data-inline="true" id="buttoncontainer">
                                            <input type="hidden" name="deletePostbuttonl" value="<?php echo $row['id']?>"/>
                                            <input type = "submit" data-inline="true" value="Delete"  name="deletePostbutton">
                                            </div>

                                            
                                            </li>
                                        </form>
                                        <?php
                                        }
                                        $z++;
                                    }
                                    ?>
                                </ul>
                            </div>
                       <?php     
                            $x++;
                            $y++;
                        }                  
                    ?>
            </div>
        </div>
       
   <div data-role="popup" class = "ui-content" id="newpostpopup" style="min-width:500px;">
            <form name = "newpostform" method="post" action="">
                <h3>New Post Information</h3>
                <label for="newpostTosub" class="ui-hidden-accessible">To Subsaiddit:</label>
                <input type="text" name="newpostTosubl" id="newpostTosub" placeholder="subsaiddit name.." required>
                
                <label for="newpostTitle" class="ui-hidden-accessible">Title:</label>
                <input type="text" name="newpostTitlel" id="newpostTitle" placeholder="Title.." required>
                  
                <label for="newpostTitle" class="ui-hidden-accessible">URL:</label>
                <input type="text" name="newpostURLl" id="newpostURL" placeholder="URL..">

                <textarea type="text" name="newpostDescriptionl" id="newpostDescription" placeholder="Description..." cols="40" rows="50">
                </textarea>
                
                <input type="submit" action = "" data-mini="true" data-inline="true" value="Post" name = "newpostsubmit">
                
            </form>
        </div>

        
    </body>

</html>
