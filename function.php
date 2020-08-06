<?php
// connect to DB
function getConnect() {
    $db = mysqli_connect('localhost', 'p_boronin', 'Bor1on7#', 'assp') or die('Connection error.');
    //$db = mysqli_connect('localhost', 'root', '', 'assp') or die('Connection error.');
    mysqli_set_charset($db, 'utf8');
    return $db;
}

// html rendering
function render($file, $arr = []) {
    $file = $file . '.html';
    include_once "view/$file";
}

// accessing of $_POST, $_GET, etc
function escape($text, $db) {
    return mysqli_real_escape_string($db, $text);
}

// finding the name of .php file
function getUrl() {
    $url = pathinfo($_SERVER['SCRIPT_NAME']);
    return $url['basename'];
}

// auxiliary func for date (returns previous m_Y depending on input)
function reduce_date($x, $y) {
    if ($x > 10) {
        return ($x - 1) . "_" . $y;
    } elseif ($x == 1) {
        return 12 . "_" . ($y - 1);
    } else {
        return (strval(0 . ($x - 1))) . "_" . $y;
    }
}

// smart func that retrieves data from complex mysql query (SELECT) with multiple columns/expressions
function selectQuery($query) {
    $db = getConnect();
    $query = str_replace("\n", " ", $query); // remove 'carriage return' + 'new line' symbols (sometimes "\r\n" works)
    preg_match("/SELECT .+ FROM/", $query, $str); // extract string exactly between SELECT and FROM
    preg_match_all("/(?:[a-z0-9_]+((?=, ?)|(?= *(FROM))))+/", $str[0], $fields); // arrange an array of fields' names
    $q = mysqli_query($db, $query); // querying...
    
    if ($q) {
        $j = 0;
        while($qq = mysqli_fetch_assoc($q)) { // fetching from the response
            foreach ($fields[0] as $f) {
                $arr[$j][$f] = $qq[$f]; // run through $fields arr and create assoc array of db elements
            }
            $j++;
        }
    }
    mysqli_close($db);
    return !empty($arr) ? $arr : false;
}

// year selector
$year = date('Y');
$year_sel = array();
while ($year >= 2017) {
    array_push($year_sel, $year);
    $year--;
}

// just aux array for months
$month_sel = [  'Январь'    => '01',
                'Февраль'   => '02', 
                'Март'      => '03', 
                'Апрель'    => '04', 
                'Май'       => '05', 
                'Июнь'      => '06', 
                'Июль'      => '07', 
                'Август'    => '08', 
                'Сентябрь'  => '09', 
                'Октябрь'   => '10', 
                'Ноябрь'    => '11', 
                'Декабрь'   => '12'
            ];

//-------------------------------------------------------------
//-------------------------??????------------------------------
//-------------------------------------------------------------
