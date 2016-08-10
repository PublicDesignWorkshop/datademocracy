<?
function unixDate($stringdate){
   //explode out the weird time format to make something strftime compatible
    $datearray  = explode(' ', $stringdate);
	$timearray  = explode(';', $datearray[4]);

	// print_r($datearray);
	$hour		= $timearray[0];
	$minute		= $timearray[1];
	$second		= $timearray[2];
    $month 		= $datearray[1];
    $day        = $datearray[2];
    $year		= $datearray[5];

    //this is super gross, but no more than the bit above
	switch ($month) {
	    case "Jan": $month = 01; break;
	    case "Feb": $month = 02; break;
	    case "Mar": $month = 03; break;
	    case "Apr": $month = 04; break;
	    case "May": $month = 05; break;
	    case "Jun": $month = 06; break;
	    case "Jul": $month = 07; break;
	    case "Aug": $month = 08; break;
	    case "Sep": $month = 09; break;
	    case "Oct": $month = 10; break;
	    case "Nov": $month = 11; break;
	    case "Dec": $month = 12; break;
	}

    $unixdate = mktime((int)$hour, (int)$minute, (int)$second, (int)$month, (int)$day, (int)$year);
    return $unixdate;
}

function getDateFromTweet($input, $format){
	$unixdate = unixDate($input);
    $date	  = strftime($format, $unixdate);

    return $date;
}

function time_elapsed_string($ptime)
{
    $etime = time() - $ptime;

    if ($etime < 1)
    {
        return '0 seconds';
    }

    $a = array( 365 * 24 * 60 * 60  =>  'year',
                 30 * 24 * 60 * 60  =>  'month',
                      24 * 60 * 60  =>  'day',
                           60 * 60  =>  'hour',
                                60  =>  'minute',
                                 1  =>  'second'
                );
    $a_plural = array( 'year'   => 'years',
                       'month'  => 'months',
                       'day'    => 'days',
                       'hour'   => 'hours',
                       'minute' => 'minutes',
                       'second' => 'seconds'
                );

    foreach ($a as $secs => $str)
    {
        $d = $etime / $secs;
        if ($d >= 1)
        {
            $r = round($d);
            return $r . ' ' . ($r > 1 ? $a_plural[$str] : $str) . ' ago';
        }
    }
}

?>