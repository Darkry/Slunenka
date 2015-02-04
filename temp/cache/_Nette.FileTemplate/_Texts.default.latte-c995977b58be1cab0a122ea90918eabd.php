<?php //netteCache[01]000401a:2:{s:4:"time";s:21:"0.74180300 1421422533";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:81:"C:\xampp\htdocs\slunenka\app\AdminModule\TextModule\templates\Texts\default.latte";i:2;i:1420563075;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:28:"$WCREV$ released on $WCDATE$";}}}?><?php

// source file: C:\xampp\htdocs\slunenka\app\AdminModule\TextModule\templates\Texts\default.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'dtkpirq67p')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lb9a715aee3f_head')) { function _lb9a715aee3f_head($_l, $_args) { extract($_args)
?><script type="text/javascript" src="<?php echo Nette\Templating\Helpers::escapeHtml($basePath . "/DA/js/" . "ajax", ENT_NOQUOTES) ?>
.js"></script><script type="text/javascript" src="<?php echo Nette\Templating\Helpers::escapeHtml($basePath . "/DA/js/" . "confirm", ENT_NOQUOTES) ?>
.js"></script><link rel="stylesheet" media="screen,projection,tv" href="<?php echo Nette\Templating\Helpers::escapeHtml($basePath . "/DA/css/" . "textTable", ENT_NOQUOTES) ?>
.css" type="text/css" /><?php
}}

//
// block content
//
if (!function_exists($_l->blocks['content'][] = '_lb0717fc223c_content')) { function _lb0717fc223c_content($_l, $_args) { extract($_args)
?><h2>Texty na webu</h2>

<table class="stripedTable"<?php echo ' id="' . $_control->getSnippetId('textTable') . '"' ?>>
<?php call_user_func(reset($_l->blocks['_textTable']), $_l, $template->getParameters()) ?>
</table>
<?php $_ctrl = $_control->getComponent("vp"); if ($_ctrl instanceof Nette\Application\UI\IRenderable) $_ctrl->validateControl(); $_ctrl->render() ?>
<br />
<?php if ($presenter->isAllowed('Texts', 'addTexts')): ?><a id="addText" href="<?php echo htmlSpecialChars($_control->link("")) ?>
">Přidat text</a><?php endif ;
}}

//
// block _textTable
//
if (!function_exists($_l->blocks['_textTable'][] = '_lb9f4b3c0749__textTable')) { function _lb9f4b3c0749__textTable($_l, $_args) { extract($_args); $_control->validateControl('textTable')
?>    <tr class="th">
<?php if ($options['date']): ?>        <th>Datum</th>
<?php endif ;if ($options['author']): ?>        <th>Autor</th>
<?php endif ;if ($options['name']): ?>        <th>Název</th>
<?php endif ;if ($options['shortVersion']): ?>        <th>Zkrácená verze</th>
<?php endif ?>
        <th>Text</th>
        <th></th>
    </tr>
<?php $iterations = 0; foreach ($texts as $text): ?>    <tr>
<?php if ($options['date']): ?>        <td><?php echo Nette\Templating\Helpers::escapeHtml($template->date($text->getDate(), "j. n. Y"), ENT_NOQUOTES) ?></td>
<?php endif ;if ($options['author']): ?>        <td><?php echo Nette\Templating\Helpers::escapeHtml($text->getAuthor(), ENT_NOQUOTES) ?></td>
<?php endif ;if ($options['name']): ?>        <td><?php echo Nette\Templating\Helpers::escapeHtml($text->getName(), ENT_NOQUOTES) ?></td>
<?php endif ;$shortText = strip_tags($text->getShortVersion()) ;if ($options['shortVersion']): ?>
        <td><?php echo $template->truncate($shortText, 15) ?></td>
<?php endif ;$textd = strip_tags($text->getText()) ?>
        <td><?php echo $template->truncate($textd, 30) ?></td>
        <td>
<?php if ($presenter->isAllowed('Texts', 'editTexts')): ?>            <a href="<?php echo htmlSpecialChars($_control->link("text", array($text->id))) ?>
"><img src="<?php echo htmlSpecialChars($basePath) ?>/DA/icons/edit.png" alt="Upravit text" /></a>
<?php endif ;if ($presenter->isAllowed('Texts', 'deleteTexts')): ?>            <a class="ajax" data-confirm="Opravdu chcete tento text smazat?" href="<?php echo htmlSpecialChars($_control->link("deleteText!", array($text->id))) ?>
"><img src="<?php echo htmlSpecialChars($basePath) ?>/DA/icons/cross.png" alt="Smazat text" /></a>
<?php endif ?>
        </td>
    </tr>
<?php $iterations++; endforeach ;
}}

//
// end of blocks
//

// template extending and snippets support

$_l->extends = empty($template->_extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $template->_extended = $_extended = TRUE;


if ($_l->extends) {
	ob_start();

} elseif (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars()) ; call_user_func(reset($_l->blocks['content']), $_l, get_defined_vars()) ; 