var current_month = 0;
var current_year = 0;
var current_version = null;
var param = null;
var lastid = null;

$(document).ready(function()
{
	//initialize_calendar();
});

function initialize_calendar()
{
    param = $(document).getUrlParam('m');
    if (null == param)
    {
        var myDate = new Date();
        current_month = myDate.getMonth()+1;
        current_year = myDate.getFullYear();
    }
    else
    {
        var myDate = param.split('-');
        current_month = parseInt(myDate[1],10);
        current_year = parseInt(myDate[0],10);
    }

    if ($("#single_calendar").length > 0)
    {
        current_version = "#single_calendar";
    }
    else if ($("#jobs_calendar").length > 0)
    {
        current_version = "#jobs_calendar";
    }
    else if ($("#full_calendar").length > 0)
    {
        current_version = "#full_calendar";
    }

    $.post("/ajax/ajax_calendar.php", { month: current_month, year :current_year, version: current_version},
        function(data) {
            $(current_version).html(data);
            var param2 = $(document).getUrlParam('d');
            if (null != param2)
            {
                $('.list').hide();
                $('#tr_'+param).show();
                $('.tr').hide();
                id = parseInt(param2);
                $('#tr_'+id).show();
            }
            else
            {
                $('.list').hide();
                $('#tr_'+param).show();
                $('#'+param).removeClass('event');
                $('#'+param).addClass('active');
                lastid = param;
            }
    });
}

function getPrevMonth()
{
    if(current_month == 1)
    {
        current_month = 12;
        current_year = current_year - 1;
    }
    else
    {
        current_month = current_month - 1;
    }
    $.post("/ajax/ajax_calendar.php", { month: current_month, year :current_year, version: current_version},
        function(data) {
        $(current_version).html(data);
    });
}

function getNextMonth()
{
    if(current_month == 12)
    {
        current_month = 1;
        current_year = current_year + 1;
    }
    else
    {
        current_month = current_month + 1;
    }
    $.post("/ajax/ajax_calendar.php", { month: current_month, year :current_year, version: current_version},
        function(data) {
        $(current_version).html(data);
    });
}

function next(id)
{
    $('.tr').hide();
    id = parseInt(id) + 1;
    $('#tr_'+id).show();
}

function prev(id)
{
    $('.tr').hide();
    id = parseInt(id) - 1;
    $('#tr_'+id).show();
}

function showEvent(id)
{
    if (null != lastid)
    {
        $('#'+lastid).removeClass('active');
        $('#'+lastid).addClass('event');
    }
    lastid = id;
    $('#'+id).removeClass('event');
    $('#'+id).addClass('active');

    if ('#full_calendar' == current_version)
    {
        $('.list').hide();
        $('#tr_'+id).show();
    }
    else if ('#single_calendar' == current_version)
    {
        window.location = "?m="+id;
    }
    else
    {
    	window.location = $('#links_events').val()+"/?m="+id;
    }
}

function showAllEvents()
{
    $('.list').show();
}

function showProps(obj){
    var props = [];
    for (var prop in obj)
        props.push(prop);
    alert(props.join(', '));
}
