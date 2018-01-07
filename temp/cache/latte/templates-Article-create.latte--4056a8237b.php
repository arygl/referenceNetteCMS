<?php
// source: C:\xampp\htdocs\referenceNetteCMS\app\presenters/templates/Article/create.latte

use Latte\Runtime as LR;

class Template4056a8237b extends Latte\Runtime\Template
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
		?><h2><?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->translate, "article.create.header")) ?></h2>
<?php
		if ($articleCategoriesCount > 0) {
			/* line 4 */ $_tmp = $this->global->uiControl->getComponent("createArticleForm");
			if ($_tmp instanceof Nette\Application\UI\IRenderable) $_tmp->redrawControl(null, false);
			$_tmp->render();
		}
		else {
			?>        <?php echo LR\Filters::escapeHtmlText(call_user_func($this->filters->translate, "article.thereAreNoArticleCategories")) ?>

<?php
		}
		
	}

}
