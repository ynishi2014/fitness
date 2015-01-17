<?php
include("../module/hakodate.php");

loginCheck();
execute();

function defaultAction(){
	include("inc_index.php");
}
