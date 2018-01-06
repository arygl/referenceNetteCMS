<?php
// source: C:\xampp\htdocs\referenceNetteCMS\app\presenters/templates/ArticleCategory/manage.latte

use Latte\Runtime as LR;

class Templatea2908f4e89 extends Latte\Runtime\Template
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
		?><h2><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->translate, "articleCategory.manage.header")) ?></h2>
<a href="<?php echo LR\Filters::escapeHtmlAttr($this->global->uiControl->link("ArticleCategory:create")) ?>"><?php
		echo LR\Filters::escapeHtmlText(call_user_func($this->filters->translate, "articleCategory.addCategory")) ?></a>
<?php
	}

}
