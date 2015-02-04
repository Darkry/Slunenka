<?php //netteCache[01]000377a:2:{s:4:"time";s:21:"0.81071800 1421422533";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:57:"C:\xampp\htdocs\slunenka\libs\panels\bar.user.panel.latte";i:2;i:1387839600;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:28:"$WCREV$ released on $WCDATE$";}}}?><?php

// source file: C:\xampp\htdocs\slunenka\libs\panels\bar.user.panel.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'h2suhrc46b')
;
// prolog Nette\Latte\Macros\UIMacros

// snippets support
if (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//

	/**
	 * @author Mikulas Dite
	 */
?>

<style type="text/css">
#frm-UserPanel-login label[for^=frmlogin-user] {margin-left: 0.5em;}
#frm-UserPanel-login input[type=radio] {margin: 4px 0 4px 0;}
#frm-UserPanel-login table {width: 100%; margin-bottom: 1em;}
</style>

<?php if ($user->isLoggedIn()): ?>
<h1>Logged in as <span style="font-style: italic; margin: 0; padding: 0;"><?php echo Nette\Templating\Helpers::escapeHtml($username, ENT_NOQUOTES) ?></span></h1>
<?php else: ?>
<h1>Logged out <span style="font-style: italic; margin: 0; padding: 0;">guest</span></h1>
<?php endif ?>

<div class="nette-inner">
<?php $_ctrl = $_control->getComponent("login"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>

<?php if ($user): ?>	<table style="width: 100%;">
		<tbody>
<?php $iterations = 0; foreach ($data as $key => $value): ?>			<tr>
				<td><?php echo Nette\Templating\Helpers::escapeHtml($key, ENT_NOQUOTES) ?></td>
<?php if (is_scalar($value)): ?>
				<td><?php echo Nette\Templating\Helpers::escapeHtml($value, ENT_NOQUOTES) ?></td>
<?php else: ?>
				<td style="font-style: italic;">
					<?php if (is_object($value)): ?> <?php echo Nette\Templating\Helpers::escapeHtml(get_class($value), ENT_NOQUOTES) ?>

					<?php elseif (is_array($value)): ?> Array
					<?php else: ?> Object
<?php endif ?>
				</td>
<?php endif ?>
			</tr>
<?php $iterations++; endforeach ;if ($user->roles): ?>			<tr>
				<td>roles</td>
				<td><?php echo Nette\Templating\Helpers::escapeHtml(implode(', ', $user->roles), ENT_NOQUOTES) ?></td>
			</tr>
<?php endif ?>
		</tbody>
	</table>
<?php endif ?>
</div>

<script type="text/javascript">
	var user_panel_form = document.forms['frm-UserPanel-login'];
	user_panel_form.elements['frmlogin-send'].parentNode.parentNode.style.display = 'none';
	
	var user_panel_elements = user_panel_form.getElementsByTagName('input');
	for (user_panel_index = 0; user_panel_index < user_panel_elements.length; user_panel_index++) {
		if (user_panel_elements[user_panel_index].getAttribute('class') == 'onClickSubmit') {
			user_panel_elements[user_panel_index].onclick = function () {
				user_panel_form.submit();
			};
		}
	}
</script>
