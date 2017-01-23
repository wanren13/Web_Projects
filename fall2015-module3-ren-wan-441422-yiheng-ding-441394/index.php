<?php
   if(empty($_SESSION))
   {
    session_start();
   }
   include 'functions.php';
   generateToken();
   checkToken();   
   $stories = getAllStories();
   ?>
<!DOCTYPE html>
<html>
   <head>
      <link type="text/css" rel="stylesheet" href="css/home.css">
      <title>Look at me</title>
   </head>
   <body>
      <header>
         <div class="wrapper" style="margin: 0 auto">
            <div class="toolbar">
               <div class="left">
                  <a href="<?php if(isset($_SESSION['username'])){echo "personal.php";}else{echo "index.php";}?>">Hello 
                  <?php if(isset($_SESSION['username'])) {
                     echo $_SESSION['username'];}?>
                  </a>
               </div>
               <div class="right"><a href="<?php if(isset($_SESSION['username'])){ echo "logout.php";}else{echo "login.php";}?>"><?php if(isset($_SESSION['username'])){echo "Logout";}else{echo "Login";}?></a></div>
            </div>
         </div>
      </header>
      <div class="wrapper">
         <div class="block">
            <h3>All News Here</h3>
            <ul>
               <?php 
                  foreach ($stories as $story) {
                      $story_id = $story[0];
                      $title = $story[1];
                      $content = $story[2];
                      $link = $story[3];
                      $author_id = $story[4];
                      $post_time = $story[5];
                      $num_comments = count(getComments($story_id));                 
                  ?>
               <li class="LI_1">
                  <div class="DIV_2">
                     <div class="DIV_3">
                        <div class="DIV_4">
                           <?php echo $num_comments; ?>
                        </div>
                     </div>
                     <div class="DIV_5">
                        <div class="DIV_6">
                           <div class="DIV_7">
                              <a href="story.php?var=<?php echo $story_id;?>"><?php echo $title; ?></a><span class="SPAN_9"></span>
                           </div>
                           <div class="DIV_19">
                              <span class="SPAN_20"><?php echo getUser($author_id); ?></span> <span class="SPAN_22"><?php echo substr($post_time, 5, -1); ?></span>
                           </div>
                        </div>
                        <div class="DIV_16">
                           <div class="DIV_17">
                              <div class="DIV_18">
                                 <?php echo $content; ?>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </li>
               <?php } ?>
            </ul>
         </div>
      </div>
   </body>
</html>