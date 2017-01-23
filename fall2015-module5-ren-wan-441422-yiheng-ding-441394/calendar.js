
NAME_OF_DAY = ['SUN', 'MON', 'TUE', 'WED', 'THR', 'FRI', 'SAT'];
COLORS = ['#009900', '#3366ff', '#cc00cc', '#F9910A', '#cc1230', '#8853af'];
$cellTemplate = $('<td class="cell"><div><div class="date"></div><div class="events"><ul></ul></div></div></td>');
$emptyCellTemplate = $('<td class="empty"></td>');
$eventTemplate = $('<div class="event"><span class="event-time"></span><span class="event-tag"></span><span class="event-title"></span><span class="event-action"><button class="eventDeleteBtn">Delete</button></span></div>')




currentMonthDate = new Date();
currentMonthDate.setDate(1);
token = null;
events = {};



function daysInMonth(month,year) {
    return new Date(year, month, 0).getDate();
}


function getMonthString(d){
    var yearStr = 1900+d.getYear();
    var monthStr = 1+d.getMonth()<10 ? '0'+(1+d.getMonth()) : 1+d.getMonth();
    return yearStr +'-'+ monthStr;
}

function getDateString(d){
    var yearStr = 1900+d.getYear();
    var monthStr = 1+d.getMonth()<10 ? '0'+(1+d.getMonth()) : 1+d.getMonth();
    var dateStr = d.getDate()<10 ? '0'+d.getDate() : d.getDate();
    return yearStr +'-'+ monthStr +'-'+ dateStr;
}

function createTable(startdate){
    var startDate = new Date(startdate);
    startDate.setTime( startDate.getTime() + startDate.getTimezoneOffset()*60*1000 );

    // console.log(startDate);
    
    var date = startDate.getDate();

    var deltaDate = 0;

    var emptyCell = true;


    $table = $('<table></table>');

    $tr = $('<tr></tr>');
    for(var j=0; j<7; j++){
        $tr.append($('<th>'+NAME_OF_DAY[j]+'</th>'));
    }
    $table.append($tr);


    var daysInThisMonth = daysInMonth(startDate.getMonth()+1, startDate.getYear());

    while(deltaDate+1 < daysInThisMonth){
        $tr = $('<tr></tr>');
        for(var j=0; j<7; j++){


            var d = startDate;
            d.setDate(date+deltaDate);

            if(d.getDay() == j){
                emptyCell = false;
            }

            if(deltaDate+1 > daysInThisMonth){
                emptyCell = true;
            }

            if(!emptyCell){

                $cell = $cellTemplate.clone(true);
                $cell.find('.date').text(''+d.getDate());

                var dateStr = getDateString(d);
                if(events.hasOwnProperty(dateStr)){
                    var thisDateEvent = events[dateStr];

                    $.each(thisDateEvent, function(key, e){
                        $li = $('<li event-id="'+e.id+'">'+e.title+'</li>');
                        $li.css('color', COLORS[e.tag_id]);
                        $li.addClass('tag-'+tags[e.tag_id].id);
                        $cell.find('ul').append($li);
                    });
                }

                $tr.append($cell);
                deltaDate ++;
            }else{
                $cell = $emptyCellTemplate.clone(true);
                $tr.append($cell);
            }
            
        }
        $table.append($tr);
    }


    return $table;
}


// function to display calendar and hide the login bar
function displayCalendar(){
    $login = $('#login');
    $login.hide();
    $logout = $('#logout');
    $logout.show();




    t = createTable(getDateString(currentMonthDate));
    $wrapper.append(t);

   

}

function displayEmptyCalendar(){

    events = {};
    tags = {};
    t = createTable(getDateString(currentMonthDate));
    $wrapper.append(t);
}


function getEvents(){

    $.ajax({
        method: "POST",
        url: "getTags.php",
        data: {token:token},
        success: function(data){
            //tags = JSON.parse(data);
            tags = data;
            // alert(data);
            $.each(tags, function(key, e){
                $option = $('<option value="'+e.id+'">'+e.tag+'</option>');
                $('#event-tag-select').append($option);
                $check = $('<input type="checkbox" checked value="'+e.id+'">');
                $checkSpan = $('<span>'+e.tag+'</span>');
                $checkSpan.css('color', COLORS[key]);
                $('#tag-toggle').append($check);
                $('#tag-toggle').append($checkSpan);
            });
        }
    });

    $.ajax({
        method: "POST",
        url: "getEvents.php",
        data: {token:token},
        success: function(d){
            //events = JSON.parse(d);
            events = d;
            displayCalendar()
        }
    });

    
}

function showDailyEvent(d){
    $eventList.empty();
    var dateStr = getDateString(d);
    var thisDateEvent = events[dateStr];
    $.each(thisDateEvent, function(key, e){
        $eventDiv = $eventTemplate.clone(true);
        $eventDiv.attr('event-id', e.id);
        $eventDiv.addClass('tag-'+tags[e.tag_id].id);
        $eventDiv.find('.event-time').text(e.time);
        $eventDiv.find('.event-tag').text(tags[e.tag_id].tag);
        $eventDiv.find('.event-title').text(e.title);
        $eventDiv.css('color', COLORS[e.tag_id]);
        $eventList.append($eventDiv);
    });
}


// many event listener
$(function(){


    $currentCell = null;

    $wrapper = $('#wrapper');
    $popover = $('#popover');
    $eventList = $('#event-list');
    $('#calendar-month').text(getMonthString(currentMonthDate));

    // event listener for check logged and listen login button
    $.ajax({
        method: "POST",
        url: "checkLogged.php",
        success: function(data){
            if(data.keep){
                getEvents();
            }else{
                displayEmptyCalendar();
            }
        }
    });


    





    //listening click on class cell and pop out popover
    $(document).on('click', '.cell', function(){
        $currentCell = $(this);
        $('.cell').css('background', '#fff');
        $(this).css('background', '#00cccc');


        var d = currentMonthDate;

        d.setDate(parseInt($(this).find('.date').text()));


        var events = [];
        var tempId = 10;
        $list_event = $popover.find('#list_event');

        $list_event.empty('li');

        $(this).find('li').each(function(){
            var e = $(this).text();
            $list_event.append('<li><div class="evtlist">'+ e + '</div><button name = '+tempId.toString() +'>[x]</button></li>')



            events.push(e);
        });

        $('#detail-header').text(getDateString(d));
        showDailyEvent(d);

    });


    $(document).on('click', '#submit', function(){
        var username = $("#username")[0].value;
        var password = $("#password")[0].value;
        $.ajax({
            method: "POST",
            url: "login.php",
            data:{ username: username, password: password},
            success: function(data){
                if (data.success){
                    alert("success,login in!!!");
                    token = data.token;
                    $wrapper = $('#wrapper');
                    $wrapper.empty();
                    //displayCalendar();
                    getEvents();
                }
                else{
                    alert(data.message);
                }
            }
        });
    });



    $(document).on('click', '.eventDeleteBtn', function(){
        $thisevent = $(this).parents('.event');
        var eventId = $thisevent.attr('event-id');
        $.ajax({
            method: "POST",
            url: "delEvent.php",
            data:{ event_id: eventId},
            success: function(){
                $thisevent.remove();
                $currentCell.find('li').each(function (index, element) {
                    if($(element).attr('event-id') == eventId){
                        $(element).remove();
                    }
                });
            }
        });
    });

    $(document).on('click', '#addEventBtn', function(){
        // alert($('#hrs').val());
        
        var d = currentMonthDate;
        d.setDate(parseInt($currentCell.find('.date').text()));
        var hrsStr = parseInt($('#hrs').val()) < 10 ? '0'+ $('#hrs').val() : $('#hrs').val();
        var minsStr = parseInt($('#mins').val()) < 10 ? '0'+ $('#mins').val() : $('#mins').val();

        var dateStr = getDateString(d);
        var timeStr = dateStr + ' '+hrsStr+':'+minsStr+':00';

        var tag_id = parseInt($('#event-tag-select').find(":selected").val());
        
        $.ajax({
            method: "POST",
            url: "addEvent.php",
            data:{ content: $('#content').val(), timestamp: timeStr, tag_id: tag_id, token:token},
            success: function(data){

                $li = $('<li event-id="'+data+'">'+$('#content').val()+'</li>');
                $li.css('color', COLORS[tag_id]);
                $li.addClass('tag-'+tags[tag_id].id);
                $currentCell.find('ul').append($li);
                $eventDiv = $eventTemplate.clone(true);
                $eventDiv.attr('event-id', data);
                $eventDiv.addClass('tag-'+tags[tag_id].id);
                $eventDiv.find('.event-time').text(hrsStr+':'+minsStr);
                $eventDiv.find('.event-tag').text(tags[tag_id].tag);
                $eventDiv.find('.event-title').text($('#content').val());
                $eventDiv.css('color', COLORS[tag_id]);
                $eventList.append($eventDiv);

                if(events.hasOwnProperty(dateStr)){
                    events[dateStr].push({"id":data,"title":$('#content').val(),"time":hrsStr+":"+minsStr,"tag_id":tag_id});
                }else{
                    events[dateStr] = [{"id":data,"title":$('#content').val(),"time":hrsStr+":"+minsStr,"tag_id":tag_id}];
                }
                $('#hrs').val('');
                $('#mins').val('');
                $('#content').val('');
            }
        });
    });


    //month switch button
    $(document).on('click', '#prevMonthBtn', function(){

        currentMonthDate.setMonth(currentMonthDate.getMonth()-1);
        currentMonthDate.setDate(1);
        $('#calendar-month').text(getMonthString(currentMonthDate));
        t = createTable(getDateString(currentMonthDate));    
        $wrapper.empty();
        $wrapper.append(t);
    });

    $(document).on('click', '#nextMonthBtn', function(){
        currentMonthDate.setMonth(currentMonthDate.getMonth()+1);
        currentMonthDate.setDate(1);
        $('#calendar-month').text(getMonthString(currentMonthDate));
        t = createTable(getDateString(currentMonthDate));    
        $wrapper.empty();
        $wrapper.append(t);
    });


    //popover close button
    $(document).on('click', '#popoverCloseBtn', function(){
        $popover.hide();
    });

    //logout button 
    $(document).on('click', '#logout', function(){
        $.ajax({
            method: "POST",
            url: "logout.php",
            success: function(){
                $login = $('#login');
                $login.show();
                $logout = $('#logout');
                $logout.hide();
                $wrapper = $('#wrapper');
                $wrapper.empty();
                displayEmptyCalendar();
            }
        });
    });

    $(document).on('click', '#tag-toggle input:checkbox', function(){
        var tag_id = $(this).val();
        if ($(this).is(':checked')) {
            var style = $('<style>.tag-'+tag_id+' { display: block; }</style>');
            $('html > head').append(style);
            $('.tag-'+tag_id).show();
        }else{
            var style = $('<style>.tag-'+tag_id+' { display: none; }</style>');
            $('html > head').append(style);
            $('.tag-'+tag_id).hide();
        }
    });



    //register button
    $(document).on('click','#register',function(){
        var username = $("#username")[0].value;
        var password = $("#password")[0].value;
        $.ajax({
            method: "POST",
            url: "register.php",
            data:{ username: username, password: password},
            success: function (data){
                if (data.success){
                    alert("register success!!!");
                    token = data.token;
                    console.log(token);
                    $wrapper = $('#wrapper');
                    $wrapper.empty();
                    displayCalendar();
                }
                else{
                    alert(data.message);
                }
            }
        });
    });


});








