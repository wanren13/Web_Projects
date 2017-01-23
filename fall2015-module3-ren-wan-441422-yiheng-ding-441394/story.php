<?php
   if(empty($_SESSION))
   {
    session_start();
   }
   
   include 'functions.php';
   generateToken();
   checkToken();
   
   if(!isset($_GET['var'])){
    echo "No story id passed!</br>";
    exit;
   }
   $story_id = $_GET['var'];
   $_SESSION['storyToComment'] = $story_id;
   $story = getStory($story_id);
   $username = getUser($story[3]);
   $comments = getComments($story_id);
   ?>
<!DOCTYPE html>
<html>
   <head>
      <link type="text/css" rel="stylesheet" href="css/story.css">
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
            <a href="index.php">
               <h3>Back to main page</h3>
            </a>
            <div class="DIV_1">
               <div class="DIV_2">
                  <div class="DIV_4">
                     <h1 class="H1_5">
                        <?php echo htmlspecialchars($story[0]); ?> 
                     </h1>
                  </div>
               </div>
               <div class="DIV_16">
                  <div class="DIV_216">
                     <div class="DIV_218">
                        <ul class="UL_219">
                           <li class="LI_220">
                              <div class="DIV_221">
                                 <a class="A_222"><img src='<?php echo getProfile(getUserId($username)); ?>' class="IMG_223" alt='/img/no_portrait_yet.png' /></a>
                              </div>
                           </li>
                           <li class="LI_224">
                           </li>
                           <li class="LI_225">
                              <?php echo htmlspecialchars($username); ?> 
                           </li>
                        </ul>
                     </div>
                     <div class="DIV_236">
                        <div class="DIV_237">
                           <div class="CC_240">
                              <div class="DIV_241">
                                 <?php echo htmlspecialchars($story[1]); ?> 
                              </div>
                           </div>
                           <br class="BR_243" />
                           <div class="DIV_244">
                           </div>
                        </div>
                        <div class="DIV_245">
                           <div class="DIV_246">
                              <ul class="UL_250">
                                 <li class="LI_253">
                                    <span class="SPAN_254"><?php echo htmlspecialchars($story[4]); ?></span>
                                 </li>
                              </ul>
                           </div>
                        </div>
                     </div>
                     <div class="DIV_277">
                     </div>
                  </div>
               </div>
            </div>
            <a href="editing.php"><h3>Add your own comment here!</h3></a>
            <?php foreach ($comments as $comment) {
               $content = $comment[0];
               $user_id = $comment[1];
               $comment_time = $comment[2];
               $cusername = getUser($user_id);
               ?>
            <div class="DIV_216">
               <div class="DIV_218">
                  <ul class="UL_219">
                     <li class="LI_220">
                        <div class="DIV_221">
                           <a class="A_222"><img src='<?php echo getProfile(getUserId($cusername)); ?>' class="IMG_223" alt='/img/no_portrait_yet.png' /></a>
                        </div>
                     </li>
                     <li class="LI_225">
                        <?php echo htmlspecialchars($cusername); ?>
                     </li>
                  </ul>
               </div>
               <div class="DIV_236">
                  <div class="DIV_237">
                     <div class="CC_240">
                        <div class="DIV_241">
                           <?php echo htmlspecialchars($content); ?>
                        </div>
                     </div>
                     <br class="BR_243" />
                     <div class="DIV_244">
                     </div>
                  </div>
                  <div class="DIV_245">
                     <div class="DIV_246">
                        <ul class="UL_250">
                           <li class="LI_253">
                              <span class="SPAN_254"><?php echo htmlspecialchars($comment_time); ?></span>
                           </li>
                        </ul>
                     </div>
                  </div>
               </div>
               <div class="DIV_277">
               </div>
            </div>
            <?php  }   ?>
         </div>
      </div>
   </body>
</html>