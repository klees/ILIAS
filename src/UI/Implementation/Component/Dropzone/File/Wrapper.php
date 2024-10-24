<?php declare(strict_types=1);

namespace ILIAS\UI\Implementation\Component\Dropzone\File;

use ILIAS\UI\Component\Dropzone\File as F;
use ILIAS\UI\Component\Component;
use LogicException;

/**
 * Class Wrapper
 *
 * @author  nmaerchy <nm@studer-raimann.ch>
 *
 * @package ILIAS\UI\Implementation\Component\Dropzone\File
 */
class Wrapper extends File implements F\Wrapper
{
    /**
     * @var Component[]
     */
    protected array $components;
    protected string $title = "";

    /**
     * @param Component[]|Component $content Component(s) being wrapped by this dropzone
     */
    public function __construct(string $url, $content)
    {
        parent::__construct($url);
        $this->components = $this->toArray($content);
        $types = array( Component::class );
        $this->checkArgListElements('content', $this->components, $types);
        $this->checkEmptyArray($this->components);
    }

    /**
     * @inheritdoc
     */
    public function withContent($content) : F\Wrapper
    {
        $clone = clone $this;
        $clone->components = $this->toArray($content);
        $types = array( Component::class );
        $this->checkArgListElements('content', $clone->components, $types);
        $this->checkEmptyArray($clone->components);

        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function withTitle(string $title) : F\Wrapper
    {
        $clone = clone $this;
        $clone->title = $title;
        return $clone;
    }

    /**
     * @inheritdoc
     */
    public function getTitle() : string
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function getContent() : array
    {
        return $this->components;
    }


    /**
     * Checks if the passed array contains at least one element, throws a LogicException otherwise.
     *
     * @throws LogicException if the passed in argument counts 0
     */
    private function checkEmptyArray(array $array) : void
    {
        if (count($array) === 0) {
            throw new LogicException("At least one component from the UI framework is required, otherwise
			the wrapper dropzone is not visible.");
        }
    }
}
