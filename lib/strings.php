<?php # (jEdit options) :folding=explicit:collapseFolds=1:
/*****************************************************************************
    Defines string-handling and common formatting functions.
*****************************************************************************/
require_once(MP_BASE_DIR.'/lib/timezones.php');

#{{{ startsWith, endsWith - tests whether haystack starts/ends with needle
############################################################################
function startsWith($haystack, $needle)
{
    //return (strncmp($haystack, $needle, strlen($needle)) == 0);
    return $needle == substr($haystack, 0, strlen($needle));
}
function endsWith($haystack, $needle)
{
    return $needle == substr($haystack, -strlen($needle));
}
#}}}########################################################################

#{{{ mp_num - "$x + 0" as PHP 7 did it (PHP 8 throws TypeError on "" + 0)
############################################################################
/**
* Numeric value of a parsed field: the leading number of a string (0 if none),
* 0 for null/false. Replaces the "$x + 0" casts, which PHP 8 refuses for
* non-numeric strings such as an empty column.
*/
function mp_num($x)
{
    if(is_string($x) && !is_numeric($x))
        return preg_match('/^[ \t\n\r\v\f]*[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?/', $x, $m) ? $m[0] + 0 : 0;
    return $x + 0;
}
#}}}########################################################################

#{{{ formatFilesize - human-readable file size with at least 2 sig. digits.
############################################################################
function formatFilesize($size)
{
        if( $size >= 10000000000 ) $size = round($size/1000000000, 0) . " Gb";
    elseif( $size >= 1000000000 )  $size = round($size/1000000000, 1) . " Gb";
    elseif( $size >= 10000000 )    $size = round($size/1000000, 0) . " Mb";
    elseif( $size >= 1000000 )     $size = round($size/1000000, 1) . " Mb";
    elseif( $size >= 10000 )       $size = round($size/1000, 0) . " Kb";
    elseif( $size >= 1000 )        $size = round($size/1000, 1) . " Kb";
    else $size = $size . " bytes";

    return $size;
}
#}}}########################################################################

#{{{ formatDayTime(Brief) - formats the day and time from a Unix timestamp
############################################################################
function formatDayTime($time)
{
    return formatDayAdaptive($time) . " at " . formatTime($time);
}

function formatDayTimeBrief($time)
{
    $day = formatDayAdaptive($time);
    if($day == 'Today') return formatTime($time);
    else                return $day;
}
#}}}########################################################################

#{{{ formatDayAdaptive - formats the day intelligently from a Unix timestamp
############################################################################
// TODO: This doesn't treat cases that straddle the new year...
function formatDayAdaptive($time)
{
    global $TIME_ZONES;
    $zone           = (isset($_SESSION['timeZone']) ? $_SESSION['timeZone'] : MP_DEFAULT_TIMEZONE);
    $zone_abbrev    = $TIME_ZONES[$zone]['abbrev'];
    $offset_sec     = $TIME_ZONES[$zone]['seconds'];
    
    $now            = time();
    $nowDate        = getdate($now+$offset_sec);
    $timeDate       = getdate($time+$offset_sec);
    if($nowDate['year'] == $timeDate['year'] && mp_num($nowDate['yday']) == mp_num($timeDate['yday']))
        return "Today";
    elseif($nowDate['year'] == $timeDate['year'] && mp_num($nowDate['yday']) == $timeDate['yday']+1)
        return "Yesterday";
    elseif($nowDate['year'] == $timeDate['year'] && mp_num($nowDate['yday']) > mp_num($timeDate['yday']) && mp_num($nowDate['yday']) < $timeDate['yday']+7)
        return gmdate("l", ($time+$offset_sec));
    else
        return gmdate("j M Y", ($time+$offset_sec));
}
#}}}########################################################################

#{{{ formatTime - formats the time only from a Unix timestamp
############################################################################
function formatTime($time)
{
    global $TIME_ZONES;
    $zone           = (isset($_SESSION['timeZone']) ? $_SESSION['timeZone'] : MP_DEFAULT_TIMEZONE);
    $zone_abbrev    = $TIME_ZONES[$zone]['abbrev'];
    $offset_sec     = $TIME_ZONES[$zone]['seconds'];
    return gmdate("g:ia", ($time+$offset_sec)) . " $zone_abbrev";
}
#}}}########################################################################

#{{{ formatHoursElapsed - displays time-to-live as "days, hours"
############################################################################
function formatHoursElapsed($secs)
{
    $one_hour   = 60*60;
    $one_day    = $one_hour*24;

    $days = floor($secs / $one_day);
    if($days >= 2)
        $msg = "$days days and ";
    elseif($days >= 1)
        $msg = "$days day and ";
    
    $secs -= $days * $one_day;
    $hours = floor($secs / $one_hour);
    if($hours >= 2)
        $msg .= "$hours hours";
    elseif($hours >= 1)
        $msg .= "$hours hour";
    else
        $msg .= "less than an hour";
        
    return $msg;
}
#}}}########################################################################

#{{{ formatMinutesElapsed - displays time-to-live as "days, hours, minutes"
############################################################################
function formatMinutesElapsed($secs)
{
    $one_minute = 60;
    $one_hour   = $one_minute*60;
    $one_day    = $one_hour*24;

    $days = floor($secs / $one_day);
    if($days >= 2)
        $msg = "$days days, ";
    elseif($days >= 1)
        $msg = "$days day, ";
    
    $secs -= $days * $one_day;
    $hours = floor($secs / $one_hour);
    if($hours >= 2)
        $msg .= "$hours hours and ";
    elseif($hours >= 1)
        $msg .= "$hours hour and ";
        
    $secs -= $hours * $one_hour;
    $minutes = floor($secs / $one_minute);
    if($minutes >= 2)
        $msg .= "$minutes minutes";
    elseif($minutes >= 1)
        $msg .= "$minutes minute";
    else
        $msg .= "less than one minute";
        
    return $msg;
}
#}}}########################################################################

#{{{ ordinalSuffix - determines 1st, 2nd, 3rd, 4th, ... suffix for number
############################################################################
/**
* Documentation for this function.
*/
function ordinalSuffix($num)
{
    $num %= 100;
    if(11 <= $num && $num <= 19) return "th";
    
    $num %= 10;
    if($num == 1) return "st";
    elseif($num == 2) return "nd";
    elseif($num == 3) return "rd";
    else return "th";
}
#}}}########################################################################

#{{{ a_function_definition - sumary_statement_goes_here
############################################################################
/**
* Documentation for this function.
*/
//function someFunctionName() {}
#}}}########################################################################

#{{{ a_function_definition - sumary_statement_goes_here
############################################################################
/**
* Documentation for this function.
*/
//function someFunctionName() {}
#}}}########################################################################

#{{{ a_function_definition - sumary_statement_goes_here
############################################################################
/**
* Documentation for this function.
*/
//function someFunctionName() {}
#}}}########################################################################

#{{{ a_function_definition - sumary_statement_goes_here
############################################################################
/**
* Documentation for this function.
*/
//function someFunctionName() {}
#}}}########################################################################
?>
