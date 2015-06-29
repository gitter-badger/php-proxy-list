<?php
/**
 * Created by PhpStorm.
 * User: EC_l
 * Date: 27.02.14
 * Time: 9:05
 * Email: bpteam22@gmail.com
 */

use bpteam\ProxyList\Proxy;
?>
<form method="post">
	<input type="text" name="list_name" value="<?=isset($_POST['list_name'])?$_POST['list_name']:''?>"> List name<br/>
<?
if(isset($_POST['list_name'])){
	$proxy = new Proxy();
	$proxy->open($_POST['list_name']);
	if(isset($_POST['send_form'])){
		$proxy->write($_POST['url'], 'url');
		$proxy->write(explode("\n",$_POST['check_word']), 'check_word');
		$functions = array();
		foreach ($proxy->getProxyFunction() as $function) {
			if($function == 'country') continue;
			if(isset($_POST['function_'.$function])){
				$functions[] = $function;
			}
		}
		if($_POST['country']){
			$proxy->write(explode("\n",$_POST['country']), 'country');
		}
		$proxy->write($functions, 'function');
		$proxy->write(isset($_POST['need_update']), 'need_update');
	}
?>
	<input name="send_form" type="hidden" value="create">
	<input name="url" type="text" value="<?=$proxy->read('url')?>"> url<br/>
	<textarea name="check_word"><?=implode("\n", $proxy->read('check_word'))?></textarea>check_word<br/>
	<textarea name="country"><?=implode("\n", $proxy->read('country'))?></textarea>country<br/>
	<?foreach ($proxy->getProxyFunction() as $function) {
		if($function == 'country') continue;
	?>
		<input name="function_<?=$function?>" type="checkbox" <?=(in_array($function, $proxy->read('function')))?'checked':''?>><?=$function?> <br/>
	<?
	}
	?>
	<input type="checkbox" name="need_update" <?=$proxy->read('need_update')?'checked':''?>>need_update<br/>
<?
}
?>
	<input type="submit" name="s1" value="Go!">
</form>