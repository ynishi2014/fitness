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
<table class="table table-striped table-bordered table-condensed">
	<tbody>
		<tr>
			<th width="50">日時</th>
			<td>2015-1-15 10:10  <span style="color:green">出席</span></td>
		</tr>
		<tr>
			<td colspan="2">
				<textarea style="width:800px;height:140px;">40分ほど体を動かしていった。孫の算数のテストの結果がよかった。</textarea>
			</td>
		</tr>
	</tbody>
</table>

<table class="table table-striped table-bordered table-condensed">
	<tbody>
		<tr>
			<th width="50">日時</th>
			<td>2015-1-12 10:10 <span style="color:red">電話</span></td>
		</tr>
		<tr>
			<td colspan="2">
				正月に食べ過ぎて体調を崩していたそうだ。もうすぐいけそうとのこと。
			</td>
		</tr>
	</tbody>
</table>


<table class="table table-striped table-bordered table-condensed">
	<tbody>
		<tr>
			<th width="50">日時</th>
			<td>2014-12-28 10:10 <span style="color:green">出席</span></td>
		</tr>
		<tr>
			<td colspan="2">
				今夜、息子の家族が帰省してくるそうだ。
			</td>
		</tr>
	</tbody>
</table>


</div>

<?php showFooter()?>
