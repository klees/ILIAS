<?php declare(strict_types=1);

/**
 * Class ilAtomQueryTransaction
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 *
 *         Implements Atom-Queries with Transactions, currently used in ilDbPdoGalery
 */
class ilAtomQueryTransaction extends ilAtomQueryBase implements ilAtomQuery
{

    /**
     * Fire your Queries
     *
     * @throws \ilAtomQueryException
     */
    public function run() : void
    {
        $this->checkBeforeRun();
        $this->runWithTransactions();
    }


    /**
     * @throws \ilAtomQueryException
     */
    protected function runWithTransactions(): void
    {
        $i = 0;
        do {
            $e = null;
            try {
                $this->ilDBInstance->beginTransaction();
                $this->runQueries();
                $this->ilDBInstance->commit();
            } catch (ilDatabaseException $e) {
                $this->ilDBInstance->rollback();
                if ($i >= self::ITERATIONS - 1) {
                    throw $e;
                }
            }
            $i++;
        } while ($e instanceof ilDatabaseException);
    }
}
