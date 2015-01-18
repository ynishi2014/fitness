<?php showHeader(array('back'=>'index.php'))?>
<div class="span12">
<ul class="breadcrumb">
	<li>
		<a href="index.php">Home</a> 
		<span class="divider">&gt;</span>
		会員詳細
	</li>
</ul>
</div>
<div class="span12">
<table class="table table-striped table-bordered table-condensed">
	<tbody>
		<tr>
			<th width="120">ID</th>
			<td>90001</td>
		</tr>
		<tr>
			<th width="120">名前</th>
			<td>田中太郎</td>
		</tr>
		<tr>
			<th width="120">直近の連絡</th>
			<td>2015-1-15</td>
		</tr>
		<tr>
			<th width="120">最終出席</th>
			<td>2015-1-15</td>
		</tr>
	</tbody>
</table>

<strong>[ <a href="index.php?action=memberDetail">履歴</a> | <a href="index.php?action=memberGraph">グラフ</a> ]</strong>

<div><strong>[ <a href="index.php?action=memberGraph">体重</a> | <a href="index.php?action=memberGraph">血圧</a> | <a href="index.php?action=memberGraph">活動量</a> ]</strong></div>

<div>
<img src="graph.jpeg">
</div>

</div>

<?php showFooter()?>
