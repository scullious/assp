<?php
include_once "function.php";
ini_set("default_charset","UTF-8");

include_once('view/header.html');
include_once('view/menu.html');

echo "  <div style='display: flex; justify-content: center;'>
            <form action='admin_readings.php'>
                <button type='submit' class='btn btn-success' style='margin: 15px;'>Readings edit form</button>
            </form>
            <form action='admin_global.php'>
                <button type='submit' class='btn btn-success' style='margin: 15px;'>Adding new items</button>
            </form>
        </div>";
