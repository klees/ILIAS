<?php

declare(strict_types=1);

/**
 * Message for the user. Mostly they are stacked, to be shown on rendering to the user all at once.
 */
class ilSystemStyleMessage
{
    public const TYPE_INFO = 0;
    public const TYPE_SUCCESS = 1;
    public const TYPE_ERROR = 2;

    protected string $message = '';
    protected int $type_id = self::TYPE_SUCCESS;

    public function __construct(string $message, int $type_id = self::TYPE_SUCCESS)
    {
        $this->setMessage($message);
        $this->setTypeId($type_id);
    }

    public function getMessageOutput() : string
    {
        return $this->message . '</br>';
    }

    public function getMessage() : string
    {
        return $this->message;
    }

    public function setMessage(string $message) : void
    {
        $this->message = $message;
    }

    public function getTypeId() : int
    {
        return $this->type_id;
    }

    /**
     * @throws ilSystemStyleMessageStackException
     */
    public function setTypeId(int $type_id) : void
    {
        if ($this->isValidTypeId($type_id)) {
            $this->type_id = $type_id;
        } else {
            throw new ilSystemStyleMessageStackException(ilSystemStyleMessageStackException::MESSAGE_STACK_TYPE_ID_DOES_NOT_EXIST);
        }
    }

    protected function isValidTypeId(int $type_id) : bool
    {
        switch ($type_id) {
            case self::TYPE_ERROR:
            case self::TYPE_INFO:
            case self::TYPE_SUCCESS:
                return true;
            default:
                return false;
        }
    }
}
