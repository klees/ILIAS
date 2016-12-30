<?php
/******************************************************************************
 * An implementation of the "Formlets"-abstraction in PHP.
 * Copyright (c) 2014 Richard Klees <richard.klees@rwth-aachen.de>
 *
 * This software is licensed under The MIT License. You should have received
 * a copy of the along with the code.
 */
namespace  ILIAS\UI\Implementation\Component\Input\Formlet;
/**
 * A formlet represents one part of a form. It can be combined with other formlets
 * to yield new formlets. Formlets are immutable, that is they can be reused in
 * as many places as you like. All methods return fresh Formlets instead of muting
 * the Formlets they are called upon.
 */
interface Formlet {
	/**
	 * Combined the formlet with another formlet and get a new formlet. Will apply
	 * a function value in this formlet to any value in the other formlet.
	 *
	 * @return  Formlet
	 */
	public function combine(Formlet $formlet);
	/**
	 * Get a new formlet with an additional check of a predicate on the input to
	 * the formlet and an error message for the case the predicate fails. The
	 * predicates has to be a function from mixed to bool.
	 *
	 * @param   \ILIAS\UI\Component\Input\Validation\Validation  $validator
	 * @return  Formlet
	 */
	public function validates( $validator);
	/**
	 * Map a function over the input value.
	 *
	 * @return Formlet
	 */
	public function map( $transformation);

	public function extract();
}
