<?php declare(strict_types=1);

/**
 * Interface ilAtomQuery
 * Use ilAtomQuery to fire Database-Actions which have to be done without beeing influenced by other queries or which can influence other queries as
 * well. Depending on the current Database-engine, this can be done by using transaction or with table-locks
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
interface ilAtomQuery
{

    // Lock levels
    const LOCK_WRITE = 1;
    const LOCK_READ = 2;
    // Isolation-Levels
    const ISOLATION_READ_UNCOMMITED = 1;
    const ISOLATION_READ_COMMITED = 2;
    const ISOLATION_REPEATED_READ = 3;
    const ISOLATION_SERIALIZABLE = 4;
    // Anomalies
    const ANO_LOST_UPDATES = 1;
    const ANO_DIRTY_READ = 2;
    const ANO_NON_REPEATED_READ = 3;
    const ANO_PHANTOM = 4;

    /**
     * Add table-names which are influenced by your queries, MyISAm has to lock those tables.
     * You get an ilTableLockInterface with further possibilities, e.g.:
     * $ilAtomQuery->addTableLock('my_table')->lockSequence(true)->aliasName('my_alias');
     * the lock-level is determined by ilAtomQuery
     */
    public function addTableLock(string $table_name) : ilTableLockInterface;

    /**
     * Every action on the database during this isolation has to be passed as Callable to ilAtomQuery.
     * An example (Closure):
     * $ilAtomQuery->addQueryClosure( function (ilDBInterface $ilDB) use ($new_obj_id, $current_id) {
     *        $ilDB->doStuff();
     *    });
     * An example (Callable Class):
     * class ilMyAtomQueryClass {
     *      public function __invoke(ilDBInterface $ilDB) {
     *          $ilDB->doStuff();
     *      }
     * }
     * $ilAtomQuery->addQueryClosure(new ilMyAtomQueryClass());
     * @param \Callable $query
     * @throws ilAtomQueryException
     */
    public function addQueryCallable(callable $query) : void;

    /**
     * Every action on the database during this isolation has to be passed as Callable to ilAtomQuery.
     * An example (Closure):
     * $ilAtomQuery->addQueryClosure( function (ilDBInterface $ilDB) use ($new_obj_id, $current_id) {
     *        $ilDB->doStuff();
     *    });
     * An example (Callable Class):
     * class ilMyAtomQueryClass {
     *      public function __invoke(ilDBInterface $ilDB) {
     *          $ilDB->doStuff();
     *      }
     * }
     * $ilAtomQuery->addQueryClosure(new ilMyAtomQueryClass());
     * @param \Callable $query
     * @throws ilAtomQueryException
     */
    public function replaceQueryCallable(callable $query) : void;

    /**
     * Fire your Queries
     * @throws \ilAtomQueryException
     */
    public function run() : void;

    /**
     * @throws \ilAtomQueryException
     */
    public static function checkIsolationLevel(int $isolation_level) : void;

    /**
     * Returns the current Isolation-Level
     */
    public function getIsolationLevel() : int;

    /**
     * Provides a check if your callable is ready to be used in ilAtomQuery
     */
    public function checkCallable(callable $query) : bool;
}
