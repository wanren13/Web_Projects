<?php
if(empty($_SESSION))
{
    session_start();
}
?>

<html>
    <head>
        <title>M5 Calendar</title>
        <link rel="stylesheet" type="text/css" href="calendar.css"/>
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script type="text/javascript" src="calendar.js"> </script>
    </head>
    <body>
        <div id="login">
            <input type="text" id="username" />
            <input type="password" id="password" />
            <input value="login" type="submit" id="submit" />
            <input value="register" type="submit" id="register" />
        </div>
        <header>
            <input value="logout" type="submit" id="logout" />
            <h1>Calendar</h1>
        </header>
        <div id="main">
            <div id="calendar-head">
                <button id="prevMonthBtn">Prev</button>
                <span id="calendar-month">2015-11</span>
                <button id="nextMonthBtn">Next</button>
            </div>
            <div id="tag-toggle">
                <!-- <input type="checkbox" value="1">Default
                <input type="checkbox" value="1">Default
                <input type="checkbox" value="1">Default
                <input type="checkbox" value="1">Default
                <input type="checkbox" value="1">Default -->

            </div>
            <div id="wrapper"></div>
        </div>



        <div id="detail-panel">            
            <div id="detail-header">
               2015-10-25
            </div>
            <div id="detail-body">
                <div id="event-list"></div>
                <div id="add-event">
                    <form>
                        <div><label>Time</label>&nbsp;<input type ="number" id="hrs" min="0" max="23">&nbsp;:&nbsp;<input type ="number" id="mins" min="0" max="59"></div>
                        <div><label>Event</label>&nbsp;<input type = "text" value = "" class = "listened" id = "content"></div>
                        <div><label>Tag</label>&nbsp;
                            <select id="event-tag-select">
                            </select>

                        </div>
                        <input value="Add" type="button" id="addEventBtn" />
                        <!-- <type id="addEventBtn">Add</div> -->
                    </form>
                </div>
            </div>
            <div id="detail-footer">
            </div>
        </div>


        <!--
        <div id="popover">            
            <div id="add_event">
                <input type = "text" value = "" class = "listened" id = "num1">
                <input type ="number" name="hrs" min="0" max="23">
                <input type ="number" name="mins" min="0" max="59">
                <button id = "addEvtBtn">Add New Event</button>
               
            </div>
            <div id="list_event">
                
            </div>
        </div>
        -->
    </body>
</html>