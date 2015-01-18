<?php showHeader()?>
<script>

setInterval(function(){
document.attend.attend.focus();
}, 1000)

</script>
<div class="span12">
<ul class="breadcrumb">
	<li>
		<a href="index.php">Home</a>
	</li>
</ul>
</div>
<div class="span12">
<form name="attend" action="index.php?action=memberDetail" method="post">
<div>
<div style="color:gray;font-size:smaller">※カード忘れは↓に番号を入力して出席ボタンを押して下さい</div>
<input type="text" style="width:100px" id="attend"/><input type="submit" value="出席">
</div>
<form>

[ <a href="index.php">会員登録</a> ] ----
[ <a href="index.php">1週間連絡無</a> ]
[ <a href="index.php">1週間出席無</a> ]
[ <a href="index.php">2週間連絡無</a> ]
[ <a href="index.php">2週間出席無</a> ]

<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th width="50">ID</th>
			<th width="100">氏名</th>
			<th width="80">最終出席 <a href="#order">▼</a></th>
			<th width="80">最終連絡 <a href="#order">▼</a></th>
			<th width="130">電話</th>
			<th width="80">入金</th>
			<th >メモ</th>
		</tr>
	</thead>
	<tbody>
	<?php for($i = 0; $i < 10; $i++){?>
		<tr>
			<td><a href="index.php?action=memberDetail"><?=$i + 90000?></a></td>
			<td>田中 太郎</td>
			<td>2015-1-10</td>
			<td>2015-1-10</td>
			<td>090-1134-4917</td>
			<td>2015-1-5</td>
			<td>ペットの猫が最近元気がなくて・・・</td>
		</tr>
	<?php }?>
	</tbody>
</table>
</div>
<?php showFooter()?>
