<?php

require '../../config/config.php';
include('../classes/User.php');
include('../classes/Post.php');

if(isset($_POST['post_body'])) {
  //If text area of form is filled
  $post = new Post($con, $_POST['user_from']);
  $post->submitPost($_POST['post_body'], $_POST['user_to']);
}

 ?>
