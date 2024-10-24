<?php declare(strict_types=1);

/**
 * Class ilTableLock
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilTableLock implements ilTableLockInterface
{

    protected string $table_name = '';
    protected bool $lock_sequence = false;
    protected string $alias = '';
    protected int $lock_level = ilAtomQuery::LOCK_WRITE;
    protected bool $checked = false;
    protected \ilDBInterface $ilDBInstance;

    /**
     * ilTableLock constructor.
     * @param string $table_name
     */
    public function __construct($table_name, ilDBInterface $ilDBInterface)
    {
        $this->table_name = $table_name;
        $this->ilDBInstance = $ilDBInterface;
    }

    /**
     * @throws \ilAtomQueryException
     */
    public function check() : void
    {
        if (!in_array($this->getLockLevel(), [ilAtomQuery::LOCK_READ, ilAtomQuery::LOCK_WRITE], true)) {
            throw new ilAtomQueryException('', ilAtomQueryException::DB_ATOM_LOCK_WRONG_LEVEL);
        }
        if (!$this->getTableName() || !$this->ilDBInstance->tableExists($this->getTableName())) {
            throw new ilAtomQueryException('', ilAtomQueryException::DB_ATOM_LOCK_TABLE_NONEXISTING);
        }

        $this->setChecked(true);
    }

    public function lockSequence(bool $lock_bool) : ilTableLockInterface
    {
        $this->setLockSequence($lock_bool);

        return $this;
    }

    public function aliasName(string $alias_name) : ilTableLockInterface
    {
        $this->setAlias($alias_name);

        return $this;
    }

    public function getTableName() : string
    {
        return $this->table_name;
    }

    public function setTableName(string $table_name) : void
    {
        $this->table_name = $table_name;
    }

    public function isLockSequence() : bool
    {
        return $this->lock_sequence;
    }

    public function setLockSequence(bool $lock_sequence) : void
    {
        $this->lock_sequence = $lock_sequence;
    }

    public function getAlias() : string
    {
        return $this->alias;
    }

    public function setAlias(string $alias) : void
    {
        $this->alias = $alias;
    }

    public function getLockLevel() : int
    {
        return $this->lock_level;
    }

    public function setLockLevel(int $lock_level) : void
    {
        $this->lock_level = $lock_level;
    }

    public function isChecked() : bool
    {
        return $this->checked;
    }

    public function setChecked(bool $checked) : void
    {
        $this->checked = $checked;
    }
}
