<?php showHeader()?>
<div class="hero-unit">
	<h2>Fitness</h2>
	<br>
	<form action="index.php?action=login" method="post" name="form">
		<div style="width:300px;padding-bottom:15px;">
			<div style="float:left;padding-top:4px;">ID</div>
			<input type="text" name="login" value="<?=@$_POST['login']?>" placeholder="ID" style="float:right;width:200px;">
		</div><br clear="all">
		<div style="width:300px;padding-bottom:15px;">
			<div style="float:left;padding-top:4px;">Password</div>
			<input type="password" name="password" value="<?=@$_POST['password']?>" placeholder="password" style="float:right;width:200px;">
		</div><br clear="all">
		<?php showError('password')?>
		<div style="margin-top:5px;">
			<label><input type="checkbox" name="save_id_password" value="1"  style="margin-top:-1px;" <?=@$checked?>> IDとパスワードを保存する</label>
		</div>
		<button class="btn" data-toggle="button" onclick="form.submit();" style="width:300px;height:40px;margin-top:5px;">ログイン</button>
	</form>
</div>
<?php showFooter()?>