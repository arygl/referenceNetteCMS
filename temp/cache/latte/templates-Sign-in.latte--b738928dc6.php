<?php
// source: C:\xampp\htdocs\referenceNetteCMS\app\presenters/templates/Sign/in.latte

use Latte\Runtime as LR;

class Templateb738928dc6 extends Latte\Runtime\Template
{
	public $blocks = [
		'content' => 'blockContent',
	];

	public $blockTypes = [
		'content' => 'html',
	];


	function main()
	{
		extract($this->params);
		if ($this->getParentName()) return get_defined_vars();
		$this->renderBlock('content', get_defined_vars());
		return get_defined_vars();
	}


	function prepare()
	{
		extract($this->params);
		Nette\Bridges\ApplicationLatte\UIRuntime::initialize($this, $this->parentName, $this->blocks);
		
	}


	function blockContent($_args)
	{
		extract($_args);
		?><h2><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->translate, "sign.in.header")) ?></h2>
<?php
		/* line 3 */ $_tmp = $this->global->uiControl->getComponent("signInForm");
		if ($_tmp instanceof Nette\Application\UI\IRenderable) $_tmp->redrawControl(null, false);
		$_tmp->render();
		
	}

}
