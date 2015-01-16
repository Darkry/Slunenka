<?php //netteCache[01]000384a:2:{s:4:"time";s:21:"0.77562900 1421422533";s:9:"callbacks";a:2:{i:0;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:9:"checkFile";}i:1;s:64:"C:\xampp\htdocs\slunenka\app\AdminModule\templates\@layout.latte";i:2;i:1387839600;}i:1;a:3:{i:0;a:2:{i:0;s:19:"Nette\Caching\Cache";i:1;s:10:"checkConst";}i:1;s:25:"Nette\Framework::REVISION";i:2;s:28:"$WCREV$ released on $WCDATE$";}}}?><?php

// source file: C:\xampp\htdocs\slunenka\app\AdminModule\templates\@layout.latte

?><?php
// prolog Nette\Latte\Macros\CoreMacros
list($_l, $_g) = Nette\Latte\Macros\CoreMacros::initRuntime($template, 'nzph2zea0m')
;
// prolog Nette\Latte\Macros\UIMacros
//
// block head
//
if (!function_exists($_l->blocks['head'][] = '_lbe65b08379a_head')) { function _lbe65b08379a_head($_l, $_args) { extract($_args)
;
}}

//
// block _flashMessages
//
if (!function_exists($_l->blocks['_flashMessages'][] = '_lbac4b4644d8__flashMessages')) { function _lbac4b4644d8__flashMessages($_l, $_args) { extract($_args); $_control->validateControl('flashMessages')
?>                    <script type="text/javascript">
                        $('document').ready(function() {
<?php $iterations = 0; foreach ($flashes as $flash): if ($flash->type == "success"): ?>
                                    alertify.success(<?php echo Nette\Templating\Helpers::escapeJs($flash->message) ?>);
<?php elseif ($flash->type == "error"): ?>
                                    alertify.error(<?php echo Nette\Templating\Helpers::escapeJs($flash->message) ?>);
<?php else: ?>
                                    alertify.log(<?php echo Nette\Templating\Helpers::escapeJs($flash->message) ?>);
<?php endif ;$iterations++; endforeach ?>
                        });
                    </script>
<?php
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
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <meta name="description" content="DarkAdmin web administration" />
<?php if (isset($robots)): ?>        <meta name="robots" content="<?php echo htmlSpecialChars($robots) ?>" />
<?php endif ?>

        <title><?php if (isset($title)): echo Nette\Templating\Helpers::escapeHtml($title, ENT_NOQUOTES) ?>
 | DarkAdmin<?php else: ?>DarkAdmin<?php endif ?></title>

<?php if ($user->isLoggedIn()): ?>        <link rel="stylesheet" media="screen,projection,tv" href="<?php echo htmlSpecialChars($basePath) ?>/css/reset.css" type="text/css" />
<?php endif ;if ($user->isLoggedIn()): ?>        <link rel="stylesheet" media="screen,projection,tv" href="<?php echo htmlSpecialChars($basePath) ?>/DA/css/style.css" type="text/css" />
<?php endif ;if (!$user->isLoggedIn()): ?>        <link rel="stylesheet" media="screen,projection,tv" href="<?php echo htmlSpecialChars($basePath) ?>/DA/css/loginstyle.css" type="text/css" />
<?php endif ?>
        <link rel="stylesheet" media="screen,projection,tv" href="<?php echo htmlSpecialChars($basePath) ?>/DA/css/alertify.css" type="text/css" />

        <script type="text/javascript" src="<?php echo htmlSpecialChars($basePath) ?>/js/jquery.js"></script>
        <script type="text/javascript" src="<?php echo htmlSpecialChars($basePath) ?>/js/netteForms.js"></script>
<script type="text/javascript" src="<?php echo Nette\Templating\Helpers::escapeHtml($basePath . "/DA/js/" . "alertify", ENT_NOQUOTES) ?>
.js"></script>        <script type="text/javascript">
            $('document').ready(function() {
                $('#leveMenu ul li span').click(function() {
                    $(this).parent().find("ul").slideToggle();
                });
            });
        </script>
	<?php if ($_l->extends) { ob_end_clean(); return Nette\Latte\Macros\CoreMacros::includeTemplate($_l->extends, get_defined_vars(), $template)->render(); }
call_user_func(reset($_l->blocks['head']), $_l, get_defined_vars())  ?>

    </head>

    <body>

        <script> document.body.className+=' js' </script>
        <div id="horniLista">
        </div>
<?php if ($user->isLoggedIn()): $m = $modules->getModuleInfo('HistoryModule') ;if ($m->getComponentState("History") && $m->getActionState("History","viewHistory") && $acl->isAllowed($userRole, 'HistoryModule', 'History', 'viewHistory')): ?>
                <a id="historie" href="<?php echo htmlSpecialChars($_control->link(":Admin:History:History:")) ?>
"><span>Historie</span></a>
<?php endif ;endif ?>
        <div id="all">
            <div id="listaIn">
                <a href="<?php echo htmlSpecialChars($_control->link(":Admin:Index:Index:")) ?>
"><div class="logo"><h1>DarkAdmin</h1></div></a>
<?php if ($user->isLoggedIn()): ?>                <div class="user">
                    <a style="font-weight: bold;" href="<?php echo htmlSpecialChars($_control->link(":Admin:AccountEdit:AccountEdit:")) ?>
"><?php echo Nette\Templating\Helpers::escapeHtml($user->getIdentity()->realName, ENT_NOQUOTES) ?>
</a> | <a class="odhlasit" href="<?php echo htmlSpecialChars($_control->link("logout!")) ?>
">Odhlásit se</a>
                </div>
<?php endif ?>
            </div>
            <div id="obsah">
<?php if ($user->isLoggedIn()): ?>
                <div id="leveMenu">
                    <ul>
<?php $m = $modules->getModuleInfo('TextModule') ;if ($m->isEditable()): ?>                        <li><span>Obsah webu</span>
                            <ul>
<?php if ($m->getComponentState('Texts')): ?>                                <li style="list-style-image: url('<?php echo htmlSpecialChars(Nette\Templating\Helpers::escapeCss($basePath)) ?>/DA/icons/texts.png');">
                                    <a href="<?php echo htmlSpecialChars($_control->link(":Admin:Text:Texts:")) ?>
">Texty na webu</a>
                                </li>
<?php endif ?>
                            </ul>
                        </li>
<?php endif ;$m = $modules->getModuleInfo('GalleryModule') ;if ($m->isEditable()): ?>                        <li><span>Galerie</span>
                            <ul>
<?php if ($m->getComponentState('Galleries')): ?>                                <li style="list-style-image: url('<?php echo htmlSpecialChars(Nette\Templating\Helpers::escapeCss($basePath)) ?>/DA/icons/gallery.png');">
                                    <a href="<?php echo htmlSpecialChars($_control->link(":Admin:Gallery:Galleries:")) ?>
">Správa galerií</a>
                                </li>
<?php endif ;if ($m->getComponentState('Images') && $acl->isAllowed($userRole, 'GalleryModule', 'Images', 'addImages')): ?>
                                <li style="list-style-image: url('<?php echo htmlSpecialChars(Nette\Templating\Helpers::escapeCss($basePath)) ?>/DA/icons/addImage.png');">
                                    <a href="<?php echo htmlSpecialChars($_control->link(":Admin:Gallery:Images:")) ?>
">Přidat obrázky</a>
                                </li>
<?php endif ?>
                            </ul>
                        </li>
<?php endif ;$m = $modules->getModuleInfo('ListsModule') ;if ($m->isEditable()): ?>
                        <li><span><?php echo Nette\Templating\Helpers::escapeHtml($m->getModuleRealName(), ENT_NOQUOTES) ?></span>
                            <ul>
<?php if ($m->getOption("Lists","listsInMenu") == true): $iterations = 0; foreach ($lists as $list): ?>
                                <li style="list-style-image: url('<?php echo htmlSpecialChars(Nette\Templating\Helpers::escapeCss($basePath)) ?>/DA/icons/list.png');">
                                    <a href="<?php echo htmlSpecialChars($_control->link(":Admin:Lists:Lists:list", array($list->getId()))) ?>
"><?php echo Nette\Templating\Helpers::escapeHtml($list->getName(), ENT_NOQUOTES) ?></a>
                                </li>
<?php $iterations++; endforeach ;else: endif ?>
                            </ul>
                        </li>
<?php endif ;$m = $modules->getModuleInfo('WebUserModule') ;if ($m->isEditable()): ?>
                        <li><span><?php echo Nette\Templating\Helpers::escapeHtml($m->getModuleRealName(), ENT_NOQUOTES) ?></span>
                            <ul>
<?php if ($m->getComponentState('UserList')): ?>                                <li style="list-style-image: url('<?php echo htmlSpecialChars(Nette\Templating\Helpers::escapeCss($basePath)) ?>/DA/icons/user.png');">
                                    <a href="<?php echo htmlSpecialChars($_control->link(":Admin:WebUser:User:")) ?>
">Seznam uživatelů</a>
                                </li>
<?php endif ?>
                            </ul>
                        </li>
<?php endif ;$m = $modules->getModuleInfo('SettingsModule') ;if ($m->isEditable()): ?>                        <li><span>Nastavení</span>
                            <ul>
<?php if ($m->getComponentState('Roles')): ?>                                <li style="list-style-image: url('<?php echo htmlSpecialChars(Nette\Templating\Helpers::escapeCss($basePath)) ?>/DA/icons/computer.png');">
                                    <a href="<?php echo htmlSpecialChars($_control->link(":Admin:Settings:Roles:")) ?>
">Role a práva uživatelů</a></li>
<?php endif ;if ($m->getComponentState('Users')): ?>                                <li style="list-style-image: url('<?php echo htmlSpecialChars(Nette\Templating\Helpers::escapeCss($basePath)) ?>/DA/icons/user.png');">
                                    <a href="<?php echo htmlSpecialChars($_control->link(":Admin:Settings:Users:")) ?>
">Uživatelé administrace</a></li>
<?php endif ;if ($m->getComponentState('Problem')): ?>                                <li style="list-style-image: url('<?php echo htmlSpecialChars(Nette\Templating\Helpers::escapeCss($basePath)) ?>/DA/icons/cross.png');">
                                    <a href="<?php echo htmlSpecialChars($_control->link(":Admin:Settings:Problem:")) ?>
">Nahlásit problém</a></li>
<?php endif ?>
                            </ul>
                        </li>
<?php endif ?>
                    </ul>
                </div>
                <div id="pravySloupec">

<div id="<?php echo $_control->getSnippetId('flashMessages') ?>"><?php call_user_func(reset($_l->blocks['_flashMessages']), $_l, $template->getParameters()) ?>
</div>
<?php Nette\Latte\Macros\UIMacros::callBlock($_l, 'content', $template->getParameters()) ?>
                </div>
                <div style="clear: both"></div>
<?php else: Nette\Latte\Macros\UIMacros::callBlock($_l, 'content', $template->getParameters()) ;endif ?>
            </div>
        </div>
    </body>
</html>
