<?php  

// **************************************************
// REDIRECT FUNCTION
//
// NOTE: This must be called before any HTML or
//       whitespace (i.e. before header sent)
// **************************************************

// redirect the user to a different page
function redirect_to($page) {
  header("Location: " . $page);
  exit;
}

// **************************************************
// NAVBAR FUNCTIONS
//
// NOTE: PATHS WILL NEED UPDATED WHEN DEPLOYING - 
//       TO SPECIFY HOME DIRECTORY
// **************************************************

// get the navbar for the correct context
function get_navbar() {
  $html = navbar_open();
  if (is_logged_in()) {
    // user is logged in - display user nav bar
    $html .=  user_navbar();
  } else {
    // user is not logged in, display lite navbar
    $html .= simple_navbar();
  }
  $html .= navbar_close();

  return $html;
}

// get the opening section of the navbar
function navbar_open() {
  $html = "<nav class = \"navbar navbar-inverse navbar-fixed-top\" style=\"height: auto;\">";
  $html .= "<div class = \"container\">";
  $html .= "<div class=\"navbar-header\">";
  $html .= "<img src=\"../public/static/img/logo.png\" class=\"logo\" width=\"30\" height=\"30\" alt=\"\"/>";
  $html .= "<a href=\"../public/index.php\" class=\"navbar-brand\">PharmGenius</a>";
  $html .= "<button class = \"navbar-toggle\" data-toggle= \"collapse\" data-target = \".navHeaderCollapse\">";
  $html .= "<span class=\"icon-bar\"></span>";
  $html .= "<span class=\"icon-bar\"></span>";
  $html .= "<span class=\"icon-bar\"></span>";
  $html .= "</button>";
  $html .= "</div>";
  $html .= "<div class = \"collapse navbar-collapse navHeaderCollapse\">";
  $html .= "<ul class = \"nav navbar-nav navbar-right\">";

  return $html;
}

// get the closing section of the navbar
function navbar_close() {
  $html = "</ul></div></div></nav>";
  return $html;
}

// get the admin section of the navbar
function admin_navbar() {
  $html = "<li><a href=\"../public/reviewCategories.php\" id=\"reviewCats\" class=\"nav-button\">View Category Database</a></li>";
  return $html;
}

// get the user section of the navbar
function user_navbar() {
  $html = "<li><a href=\"../public/select_quiz.php\" id=\"quizme\" class=\"nav-button\">Quiz Me!</a></li>";
  $html .= "<li><a href=\"../public/leaderboard.php\" id=\"leaderboard\" class=\"nav-button\">Leaderboard</a></li>";
  $html .= "<li class=\"dropdown\">";
  $html .= "<a href=\"\" class=\"dropdown-toggle\" data-toggle=\"dropdown\"><span>Questions&nbsp;</span><b class=\"caret\"></b></a>";
  $html .= "<ul class=\"dropdown-menu\">";
  $html .= "<li><a href=\"../public/review_my_questions.php\" id=\"myQuestions\">Your Questions</a></li>";
  $html .= "<li><a href=\"../public/submit_new.php\" id=\"submitNew\">Submit A Question</a></li>";
  $html .= "<li><a href=\"../public/review_new_questions\" id=\"ReviewNew\" class=\"nav-button\">Review New Questions</a></li>";
  $html .= "<li><a href=\"../public/review_old_questions\" id=\"ReviewOld\" class=\"nav-button\">View Question Database</a></li>";

  if (is_user_admin()) {
    $html .= admin_navbar();
  }

  $html .= "</ul></li>";
  $html .= "<li class=\"dropdown\">";
  $html .= "<a href=\"../public/index.php\" class=\"dropdown-toggle\" data-toggle=\"dropdown\">";
  $html .= "<span class=\"profile-container\">";
  $html .= "<img src=\"../public/static/img/logo.png\" class=\"profile-small\" alt=\"\"/>&nbsp;&nbsp;Profile&nbsp;</span><b class=\"caret\" style=\"font-size:1.5em;\"></b></a>";
  $html .= "<ul class=\"dropdown-menu\">";
  $html .= "<li><a href=\"../public/profile.php?id=";
  $html .= htmlentities($_SESSION["username"]); // add username to the profile page get request
  $html .= "\">View Profile</a></li>";
  $html .= "<li><a href=\"../public/send_bug_report.php\" data-toggle=\"modal\">Report a Bug</a></li>";
  $html .= "<li><a href=\"../public/logout.php\">Sign Out</a></li>";
  $html .= "</ul></li>";

  return $html;
}

// get the default navbar (not logged in)
function simple_navbar() {
  $html = "<li><a href=\"login.php\">Sign In</a></li>";

  return $html;
}

// **************************************************
// PLAY BUTTON FUNCTIONS
// **************************************************

// get the play button display depending on if user is logged in or not
function get_play_button_display() {
  if (is_logged_in()) {
    return get_play_button();
  } else {
    return get_about_button();
  }
}

// get the button to play quiz if logged in
function get_play_button() {
  $html = "<a href=\"select_quiz.php\" class=\"btn btn-circle\" id=\"on-play\">";
  $html .= "<i style=\"cursor:pointer; color:white; position: relative; left: 3px; top: -2px;\" class=\"fa fa-play animated\"></i></a>";

  return $html;
}

// show the button that directs to about (if user not logged in)
function get_about_button()  {
  $html = "<div id = \"play\">";
  $html .= "<a href=\"#about\" class=\"btn btn-circle page-scroll\">";
  $html .= "<i style=\"position: relative; left: -1px;\" class=\"fa fa-angle-double-down animated\"></i></a></div>";

  return $html;
}

// **************************************************
// DROPDOWN FUNCTIONS
// **************************************************

// gets html for the quiz selection drop downs - builds html for the
// category, then calls get_quiz_dropdown
function get_initial_dropdowns() {
  $html = "<div class=\"col-sm-6 quiz-text\">Select a Category:</div>";
  $html .= "<div class=\"col-sm-6 quiz-select\">";
  $html .= "<select style=\"color:black\" id=\"category\" name=\"category\" method=\"POST\">";

  // get all active categories
  $category_set = get_all_categories(true);
  
  while ($category = mysqli_fetch_assoc($category_set)) {
    $name = htmlentities($category["category_name"]);
    $id = htmlentities($category["category_id"]);
    if (!isset($quiz_dropdown)) {
      $quiz_dropdown = get_quiz_dropdown($id);
    }
    $html .= "<option value=\"{$id}\">{$name}</option>";
  }
  $html .= "</select><br /><br /></div>";

  if (isset($quiz_dropdown)) {
    $html .= $quiz_dropdown;
  }

  return $html;
}

// gets html for the quiz drop down
function get_quiz_dropdown($category_id) {
  $html = "<div class=\"col-sm-6 quiz-text\">Select a Quiz:</div>";
  $html .= "<div class=\"col-sm-6 quiz-select\">";
  $html .= "<select style=\"color:black\" id=\"quiz\" name=\"quiz\" method=\"POST\">"; 

  $quiz_set = get_quizzes_for_category($category_id, true);
   
  while ($quiz = mysqli_fetch_assoc($quiz_set)) {
    $name = htmlentities($quiz["quiz_name"]);
    $id = htmlentities($quiz["quiz_id"]);
    $html .= "<option value=\"{$id}\">{$name}</option>";
  }
  $html .= "</select><br /><br /></div>";

  return $html;
}

// **************************************************
// DATABASE HELPER FUNCTIONS
// **************************************************

// ensure query worked
function confirm_query($result_set) {
  if (!$result_set) {
      die("Database query failed.");
    }
}

// clean up string to prevent injections
function mysql_prep($string) {
    global $connection;
    
    $escaped_string = mysqli_real_escape_string($connection, $string);
    return $escaped_string;
  }

// **************************************************
// RETRIEVE DATA FUNCTIONS
// **************************************************

// find a user - return the user or null if not found
function find_user($username) {
  global $connection;

  $safe_username = mysql_prep($username);

  $query = "SELECT * ";
  $query .= "FROM user ";
  $query .= "WHERE username = '{$safe_username}' ";
  $query .= "LIMIT 1";

  $user_set = mysqli_query($connection, $query);
  confirm_query($user_set);
  if ($user = mysqli_fetch_assoc($user_set)) {
    return $user; // user found and admin
  } else {
    return null; // user found, but not admin
  }
}

// get all active categories
// if active_only is true, only active categories will be returned
function get_all_categories($active_only) {
  global $connection;

  $query = "SELECT category_id, category_name ";
  $query .= "FROM category ";
  if ($active_only) {
      $query .= "WHERE active_flag = 'Y' ";
  }
  $query .= "ORDER BY category_name";

  $category_set = mysqli_query($connection, $query);
  confirm_query($category_set);

  return $category_set;
}

// get quiz_ids and quiz_names for specified category
// if active_only is true, only active quizzes will be returned
function get_quizzes_for_category($category_id, $active_only) {
  global $connection;

  $safe_category_id = mysql_prep($category_id);

  $query = "SELECT quiz_id, quiz_name ";
  $query .= "FROM quiz ";
  $query .= "WHERE category_id = {$safe_category_id} ";
  if ($active_only) {
      $query .= "AND active_flag = 'Y' ";
  }
  $query .= "ORDER BY quiz_name";

  $quiz_set = mysqli_query($connection, $query);
  confirm_query($quiz_set);

  return $quiz_set;
}


// **************************************************
// LOGIN/PASSWORD FUNCTIONS
// **************************************************

// return a hashed password to store in the database
function encrypt_password($password) {
  $format = "$2y$10$"; // using Blowfish algorithm 10 times.
  $salt_length = 22; // need 22 characters for salt

  $salt = get_salt($salt_length);
  $format_and_salt = $format . $salt;
  $hashed_password = crypt($password, $format_and_salt);

  return $hashed_password;
}

// generate a random salt for encryption
function get_salt($length) {
  $random_string = md5(uniqid(mt_rand(), true));
  $base64 = base64_encode($random_string);
  $modified_base64 = str_replace("+", ".", $base64);

  $salt = substr($modified_base64, 0, $length);
  return $salt;
}

// Return true if password matches existing hash, false otherwise
function does_password_match($password, $existing_hash) {
  $hash = crypt($password, $existing_hash);

  if ($hash === $existing_hash) {
    return true;
  } else {
    return false;
  }
}

// returns data about the user if login succeeds, false otherwise.
function attempt_login($username, $password) {
  $user = find_user($username);
  if ($user) {
    // user found, check password
    if (does_password_match($password, $user["password"])) {
      // password matched, return user
      return $user;
    } else {
      // password does not match
      return false;
    }
  } else {
    // user not found
    return false;
  }
}

// returns true if the user is logged in and an admin, false otherwise
function is_user_admin() {
  if (is_logged_in()) {
    // return true if admin flag is set for this user
    return $_SESSION["admin_flag"] === "Y";
  } else {
    // user not logged in - can't be an admin
    return false;
  }
}

// returns true if the user is logged in and an owner, false otherwise
function is_user_owner() {
  if (is_logged_in()) {
    // return true if owner flag is set for this user
    return $_SESSION["owner_flag"] === "Y";
  } else {
    // user not logged in - can't be an owner
    return false;
  }
}

// returns true if user is logged in.
function is_logged_in() {
  return (isset($_SESSION["username"]));
}

// redirect user if not logged in
function confirm_login_status() {
  if (!is_logged_in()) {
    redirect_to("../public/index.php");
  }
}

// logout user by removing pertinent session variables
function logout() {
  $_SESSION["username"] = null;
  $_SESSION["active_flag"] = null;
  $_SESSION["admin_flag"] = null;
  $_SESSION["owner_flag"] = null;
}

?>