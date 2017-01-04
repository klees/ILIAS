<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet;

use ILIAS\UI\Implementation\Component\Input\Formlet\FunctionValue as FV;

class Map {


	/**
	 * @var FV\FunctionValue
	 */
	protected $model_to_view = null;

	/**
	 * @var FV\FunctionValue
	 */
	protected $view_to_model = null;

	/**
	 * Map constructor.
	 * @param FV\FunctionValue $mapper_function
	 */
	public function __construct(
			FV\FunctionValue $model_to_view = null,
			FV\FunctionValue $view_to_model = null

	)
	{
		if($model_to_view){
			$this->model_to_view = $model_to_view;
		}else{
			$this->model_to_view = new FV\Identity();
		}

		if($view_to_model){
			$this->view_to_model = $view_to_model;
		}else{
			$this->view_to_model  = new FV\Identity();
		}

	}


	public function modelToView($value){
		$model_to_view = $this->model_to_view->apply($value);
		return $model_to_view->get();
	}
	public function viewToModel($value){
		$view_to_model = $this->view_to_model->apply($value);
		return $view_to_model->get();
	}

	/**
	 * @return FV\FunctionValue
	 */
	public function getModelToView()
	{
		return $this->model_to_view;
	}

	/**
	 * @return FV\FunctionValue
	 */
	public function getViewToModel()
	{
		return $this->view_to_model;
	}

}
