<!DOCTYPE html>
<html>
  <head>
    <title>Social</title>
    <!-- CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
  </head>
  <body>
    <style>
      * {
        font-size: 14px;
        font-family: 'Helvetica', sans-serif;
      }
      body {
        background-color: #FFF;
      }
      form {
        position: absolute;
        top: 0;
        width: 100%;
      }
    </style>
    <?php
    require 'config/config.php';
    include('includes/classes/User.php');
    include('includes/classes/Post.php');
    include('includes/classes/Notification.php');

    if(isset($_SESSION['username'])) {
      $userLoggedIn = $_SESSION['username']; //sets the logged in user with the session
      $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
      $user = mysqli_fetch_array($user_details_query); //Access User Details
    } else {
      //if a user is not logged in...
      header('Location: register.php'); //redirects back to register page
    }

    //Get id of Post
    if(isset($_GET['post_id'])){
      $post_id = $_GET['post_id'];
    }

    //Get information about likes and associated users
    $get_likes = mysqli_query($con, "SELECT likes, added_by FROM posts WHERE id='$post_id'");
  	$row = mysqli_fetch_array($get_likes);
  	$total_likes = $row['likes'];
  	$user_liked = $row['added_by'];

    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_liked'");
    $row = mysqli_fetch_array($user_details_query);
    $total_user_likes = $row['num_likes']; //Total for user

    //Like Button & Unlike Buttons
    if(isset($_POST['like_button'])) {
      //Like button is pressed
        $total_likes++;
        $query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
        $total_user_likes++;
        $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
        //Likes Table: id, username, post_id
        $insert_user = mysqli_query($con, "INSERT INTO likes VALUES('', '$userLoggedIn', '$post_id')");

          //Notification
            if($user_liked != $userLoggedIn) {
              $notification = new Notification($con, $userLoggedIn);
              $notification->insertNotification($post_id, $user_liked, 'like');
            }
    }

    if(isset($_POST['unlike_button'])) {
      //UnLike button is pressed
        $total_likes--;
        $query = mysqli_query($con, "UPDATE posts SET likes='$total_likes' WHERE id='$post_id'");
        $total_user_likes--;
        $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
        //Likes Table: id, username, post_id
        $insert_user = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
    }


      //Check for previous likes
      $check_query = mysqli_query($con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND post_id='$post_id'");
      $num_rows = mysqli_num_rows($check_query);

        if($num_rows > 0) {
          //UNLIKE Options
          echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
                  <input type="submit" class="comment_like" name="unlike_button" value="&#x2665;">
                  <div class="like_value">
                    &#x2665; ' . $total_likes . '
                  </div>
                </form>
                ';
        } else {
          //LIKE Options
          echo '<form action="like.php?post_id=' . $post_id . '" method="POST">
                  <input type="submit" class="comment_like" name="like_button" value="&#x2661;">
                  <div class="like_value">
                    &#x2665; ' . $total_likes . '
                  </div>
                </form>
                ';
        }

    ?>
  </body>
</html>
