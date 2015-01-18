<?php
include("../module/hakodate.php");

loginCheck();
execute();

function defaultAction(){
	include("inc_index.php");
}
function memberDetailAction(){
	include("inc_member_detail.php");
}
function memberGraphAction(){
	include("inc_member_graph.php");
}
