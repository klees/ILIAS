<?php declare(strict_types=1);

/* Copyright (c) 1998-2019 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace ILIAS\Refinery;

use ILIAS\Refinery\In;
use ILIAS\Refinery\To;
use ILIAS\Refinery\ByTrying;
use ILIAS\Refinery\Random\Group as RandomGroup;

/**
 * @author  Niels Theen <ntheen@databay.de>
 */
class Factory
{
    private \ILIAS\Data\Factory $dataFactory;
    private \ilLanguage $language;

    public function __construct(\ILIAS\Data\Factory $dataFactory, \ilLanguage $language)
    {
        $this->dataFactory = $dataFactory;
        $this->language = $language;

        $this->language->loadLanguageModule('validation');
    }

    /**
     * Combined validations and transformations for primitive data types that
     * establish a baseline for further constraints and more complex transformations
     */
    public function to() : To\Group
    {
        return new To\Group($this->dataFactory);
    }

    /**
     * Combined validations and transformations for primitive data types that
     * establish a baseline for further constraints and more complex transformations.
     *
     * Other then the `to`-group, the `kindlyTo` transformation attempts to implement
     * [Postels Law](https://en.wikipedia.org/wiki/Robustness_principle) by being
     * reasonably liberal when interpreting data. Look into the various transformations
     * in the group for detailed information what works exactly.
     */
    public function kindlyTo() : KindlyTo\Group
    {
        return new KindlyTo\Group($this->dataFactory);
    }

    /**
     * Creates a factory object to create a transformation object, that
     * can be used to execute other transformation objects in a desired
     * order.
     */
    public function in() : In\Group
    {
        return new In\Group();
    }

    /**
     * Contains constraints and transformations on numbers. Each constraint
     * on an int will attempt to transform to int as well.
     */
    public function int() : Integer\Group
    {
        return new Integer\Group($this->dataFactory, $this->language);
    }

    /**
     * Contains constraints for string
     */
    public function string() : String\Group
    {
        return new String\Group($this->dataFactory, $this->language);
    }

    /**
     * Contains constraints and transformations for custom functions.
     */
    public function custom() : Custom\Group
    {
        return new Custom\Group($this->dataFactory, $this->language);
    }

    /**
     * Contains constraints for container types (e.g. arrays)
     */
    public function container() : Container\Group
    {
        return new Container\Group($this->dataFactory);
    }

    /**
     * Contains constraints for password strings
     */
    public function password() : Password\Group
    {
        return new Password\Group($this->dataFactory, $this->language);
    }

    /**
     * Contains constraints for logical compositions with other constraints
     */
    public function logical() : Logical\Group
    {
        return new Logical\Group($this->dataFactory, $this->language);
    }

    /**
     * Contains constraints for null types
     */
    public function null() : IsNull
    {
        return new IsNull($this->dataFactory, $this->language);
    }

    /**
     * Contains constraints for numeric data types
     */
    public function numeric() : Numeric\Group
    {
        return new Numeric\Group($this->dataFactory, $this->language);
    }

    /**
     * Contains transformations for DateTime
     */
    public function dateTime() : DateTime\Group
    {
        return new DateTime\Group();
    }

    /**
     * Contains transformations for Data\URI
     */
    public function uri() : URI\Group
    {
        return new URI\Group();
    }

    /**
     * Accepts Transformations and uses first successful one.
     * @param Transformation[] $transformations
     */
    public function byTrying(array $transformations) : ByTrying
    {
        return new ByTrying($transformations, $this->dataFactory, $this->language);
    }

    public function random() : RandomGroup
    {
        return new RandomGroup();
    }

    public function identity() : IdentityTransformation
    {
        return new IdentityTransformation();
    }
}
