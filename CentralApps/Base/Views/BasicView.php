<?php
namespace CentralApps\Base\Views;

class BasicView extends AbstractView
{
	public function generate($model=null, $model_name=null)
	{
		$model_name = (is_null($model_name)) ? 'model' : $model_name;

	}
}