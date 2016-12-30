<?php

namespace ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Refactor;

use ILIAS\UI\Implementation\Component\Input\Formlet\Internal\Value as V;

class Map {


	/**
	 * @var V\FunctionCallable
	 */
	protected $model_to_view = null;

	/**
	 * @var V\FunctionCallable
	 */
	protected $view_to_model = null;

	/**
	 * Map constructor.
	 * @param V\FunctionCallable $mapper_function
	 */
	public function __construct(
			V\FunctionCallable $model_to_view = null,
			V\FunctionCallable $view_to_model = null

	)
	{
		if($model_to_view){
			$this->model_to_view = $model_to_view;
		}else{
			$this->model_to_view = new V\Identity();
		}

		if($view_to_model){
			$this->view_to_model = $view_to_model;
		}else{
			$this->view_to_model  = new V\Identity();
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
	 * @return V\FunctionCallable
	 */
	public function getModelToView()
	{
		return $this->model_to_view;
	}

	/**
	 * @return V\FunctionCallable
	 */
	public function getViewToModel()
	{
		return $this->view_to_model;
	}

}
