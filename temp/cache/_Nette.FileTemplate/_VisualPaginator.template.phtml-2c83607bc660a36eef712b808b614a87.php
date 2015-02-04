<?php //netteCache[01]000391a:2:{s:4:"time";s:21:"0.78924500 1421422533";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:71:"C:\xampp\htdocs\slunenka\libs\components\VisualPaginator\template.phtml";i:2;i:1387839600;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:28:"$WCREV$ released on $WCDATE$";}}}?><?php

// source file: C:\xampp\htdocs\slunenka\libs\components\VisualPaginator\template.phtml

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'qcjnbhohm1')
;
// prolog Nette\Latte\Macros\UIMacros

// snippets support
if (!empty($_control->snippetMode)) {
	return Nette\Latte\Macros\UIMacros::renderSnippets($_control, $_l, get_defined_vars());
}

//
// main template
//
if ($paginator->pageCount > 1): ?>
<div class="paginator">
    <span>
<?php if ($paginator->isFirst()): ?>
	<span class="button">« Previous</span>
<?php else: ?>
	<a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $paginator->page - 1))) ?>">« Previous</a>
<?php endif ?>

<?php $iterations = 0; foreach ($iterator = $_l->its[] = new Nette\Iterators\CachingIterator($steps) as $step): if ($step == $paginator->page): ?>
		<span class="current"><?php echo Nette\Templating\Helpers::escapeHtml($step, ENT_NOQUOTES) ?></span>
<?php else: ?>
		<a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $step))) ?>
"><?php echo Nette\Templating\Helpers::escapeHtml($step, ENT_NOQUOTES) ?></a>
<?php endif ?>
	<?php if ($iterator->nextValue > $step + 1): ?><span>…</span><?php endif ?>

<?php $iterations++; endforeach; array_pop($_l->its); $iterator = end($_l->its) ?>

<?php if ($paginator->isLast()): ?>
	<span class="button">Next »</span>
<?php else: ?>
	<a href="<?php echo htmlSpecialChars($_control->link("this", array('page' => $paginator->page + 1))) ?>">Next »</a>
<?php endif ?>
    </span>
</div>
<?php endif ;