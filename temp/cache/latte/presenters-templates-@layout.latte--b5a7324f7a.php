<?php
// source: C:\xampp\htdocs\referenceNetteCMS\app\presenters/templates/@layout.latte

use Latte\Runtime as LR;

class Templateb5a7324f7a extends Latte\Runtime\Template
{
	public $blocks = [
		'head' => 'blockHead',
		'scripts' => 'blockScripts',
	];

	public $blockTypes = [
		'head' => 'html',
		'scripts' => 'html',
	];


	function main()
	{
		extract($this->params);
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">

	<title>Nette blog Doctrine 2</title>

	<meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="shortcut icon" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 9 */ ?>/favicon.ico">
	<link rel="stylesheet" href="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 10 */ ?>/css/style.css">
	<?php
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('head', get_defined_vars());
?>
</head>
<body>
    <div id="container">

<?php
		if (count($flashes) > 0) {
?>
                    <div id="flashes">
                            <div class="text">
<?php
			$iterations = 0;
			foreach ($flashes as $flash) {
				?>                                    <div<?php if ($_tmp = array_filter(['flash', $flash->type])) echo ' class="', LR\Filters::escapeHtmlAttr(implode(" ", array_unique($_tmp))), '"' ?>>
                                            <?php echo LR\Filters::escapeHtmlText($flash->message) /* line 20 */ ?>

                                    </div>
<?php
				$iterations++;
			}
?>
                            </div>
                    </div>
<?php
		}
?>

            <div id="header">
                    <div id="logo">
                            <h1>Blog system</h1>
                    </div>
                    <div id="userInfo">
<?php
		if ($user->isLoggedIn()) {
			?>                                    <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->translate, "common.loggedAs")) ?>: <?php
			echo LR\Filters::escapeHtmlText($userEntity->name) /* line 32 */ ?>

<?php
		}
?>
                    </div>
                    <div id="menu">
                            <a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("Homepage:default")) ?>"><?php
		echo LR\Filters::escapeHtmlText(call_user_func($this->filters->translate, "menu.homepage")) ?></a>
                    </div>
            </div>
            <div id="content">
<?php
		$this->renderBlock('content', $this->params, 'html');
?>
            </div>
            <div id="footer">
                    <div class="text">
                            &copy; Konesoft Corporation
                    </div>
            </div>
    </div>

<?php
		$this->renderBlock('scripts', get_defined_vars());
?>
</body>
</html>
<?php
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		if (isset($this->params['flash'])) trigger_error('Variable $flash overwritten in foreach on line 19');
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockHead($_args)
	{
		
	}


	function blockScripts($_args)
	{
		extract($_args);
?>
    <script src="//code.jquery.com/jquery-1.11.2.min.js"></script>
    <script src="//nette.github.io/resources/js/netteForms.min.js"></script>
    <script src="<?php echo LR\Filters::escapeHtmlAttr(LR\Filters::safeUrl($basePath)) /* line 52 */ ?>/js/main.js"></script>
<?php
	}

}
