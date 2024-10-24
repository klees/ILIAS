<?php declare(strict_types=1);

/**
 * Class ilDBPdoManager
 *
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilDBPdoManagerPostgres extends ilDBPdoManager
{

    /**
     * @return \ilPostgresQueryUtils
     */
    public function getQueryUtils(): \ilMySQLQueryUtils
    {
        if ($this->query_utils === null) {
            $this->query_utils = new ilPostgresQueryUtils($this->db_instance);
        }

        return $this->query_utils;
    }


    public function createTable(string $name, array $fields, array $options = array()) : bool
    {
        return (bool) $this->pdo->exec($this->getQueryUtils()->createTable($name, $fields, $options));
    }


    /**
     * @return mixed[]
     */
    public function listTables(string $database = null) : array
    {
        $db = $this->db_instance;

        // gratuitously stolen from PEAR DB _getSpecialQuery in pgsql.php
        $query = 'SELECT c.relname AS "Name"' . ' FROM pg_class c, pg_user u' . ' WHERE c.relowner = u.usesysid' . " AND c.relkind = 'r'"
            . ' AND NOT EXISTS' . ' (SELECT 1 FROM pg_views' . '  WHERE viewname = c.relname)' . " AND c.relname !~ '^(pg_|sql_)'" . ' UNION'
            . ' SELECT c.relname AS "Name"' . ' FROM pg_class c' . " WHERE c.relkind = 'r'" . ' AND NOT EXISTS' . ' (SELECT 1 FROM pg_views'
            . '  WHERE viewname = c.relname)' . ' AND NOT EXISTS' . ' (SELECT 1 FROM pg_user' . '  WHERE usesysid = c.relowner)'
            . " AND c.relname !~ '^pg_'";
        $result = $db->queryCol($query, ilDBConstants::FETCHMODE_ASSOC);

        if ($db->options['portability']) {
            $result = array_map(($db->options['field_case'] == CASE_LOWER ? 'strtolower' : 'strtoupper'), $result);
        }
        sort($result); // FSX Patch
        return $result;
    }



    public function alterTable(string $name, array $changes, bool $check) : bool
    {
        /**
         * @var $db      ilDBPdoPostgreSQL
         * @var $reverse ilDBPdoReversePostgres
         */
        foreach (array_keys($changes) as $change_name) {
            switch ($change_name) {
                case 'add':
                case 'remove':
                case 'change':
                case 'name':
                case 'rename':
                    break;
                default:
                    throw new ilDatabaseException('change type "' . $change_name . '\" not yet supported');
            }
        }

        if ($check) {
            return true;
        }

        if (!empty($changes['add']) && is_array($changes['add'])) {
            foreach ($changes['add'] as $field_name => $field) {
                $query = 'ADD ' . $db->getFieldDefinition()->getDeclaration($field['type'], $field_name, $field);
                $result = $db->manipulate("ALTER TABLE $name $query");
            }
        }

        if (!empty($changes['remove']) && is_array($changes['remove'])) {
            foreach (array_keys($changes['remove']) as $field_name) {
                $field_name = $db->quoteIdentifier($field_name, true);
                $query = 'DROP ' . $field_name;
                $result = $db->manipulate("ALTER TABLE $name $query");
            }
        }

        if (!empty($changes['change']) && is_array($changes['change'])) {
            foreach ($changes['change'] as $field_name => $field) {
                $field_name = $db->quoteIdentifier($field_name, true);
                if (!empty($field['type'])) {
                    $server_info = $db->getServerVersion();

                    if (is_array($server_info) && $server_info['major'] < 8) {
                        /** @noinspection PhpUndefinedVariableInspection */
                        throw new ilDatabaseException('changing column type for "' . $change_name ?? 'undefined' . '\" requires PostgreSQL 8.0 or above');
                    }

                    $query = "ALTER $field_name TYPE " . $db->getFieldDefinition()->getTypeDeclaration($field);
                    $result = $db->manipulate("ALTER TABLE $name $query");
                }
                if (array_key_exists('default', $field)) {
                    $query = "ALTER $field_name SET DEFAULT " . $db->quote($field['definition']['default'], $field['definition']['type']);
                    $result = $db->manipulate("ALTER TABLE $name $query");
                }
                if (!empty($field['notnull'])) {
                    $query = "ALTER $field_name " . ($field['definition']['notnull'] ? "SET" : "DROP") . ' NOT NULL';
                    $result = $db->manipulate("ALTER TABLE $name $query");
                }
            }
        }

        if (!empty($changes['rename']) && is_array($changes['rename'])) {
            foreach ($changes['rename'] as $field_name => $field) {
                $field_name = $db->quoteIdentifier($field_name, true);
                $result = $db->manipulate("ALTER TABLE $name RENAME COLUMN $field_name TO " . $db->quoteIdentifier($field['name'], true));
            }
        }

        if (!empty($changes['name'])) {
            $result = $db->manipulate("ALTER TABLE " . $db->quoteIdentifier($name, true) . " RENAME TO " . $db->quoteIdentifier($changes['name']));

            $idx = array_merge($this->listTableIndexes($changes['name']), $this->listTableConstraints($changes['name']));
            foreach ($idx as $index_name) {
                $index_newname = preg_replace("/^$name/", $changes['name'], $index_name);
                $result = $db->manipulate("ALTER INDEX " . $this->getIndexName($index_name) . " RENAME TO " . $this->getIndexName($index_newname));
            }
        }

        return true;
    }


    /**
     * @return mixed[]
     */
    public function listTableFields(string $table) : array
    {
        $db = $this->db_instance;

        $table = $db->quoteIdentifier($table, true);
        $res = $this->pdo->query("select * from $table");
        $data = [];
        for ($i = 0; $i < $res->columnCount(); $i++) {
            $data[] = $res->getColumnMeta($i)["name"];
        }
        return $data;
    }


    /**
     * @return int[]|string[]
     */
    public function listTableIndexes(string $table) : array
    {
        $db = $this->db_instance;

        $table = $db->quote($table, 'text');
        $subquery = "SELECT indexrelid FROM pg_index, pg_class";
        $subquery .= " WHERE pg_class.relname=$table AND pg_class.oid=pg_index.indrelid AND indisunique != 't' AND indisprimary != 't'";
        $query = "SELECT relname FROM pg_class WHERE oid IN ($subquery)";
        $indexes = $db->queryCol($query, PDO::FETCH_ASSOC);

        $result = array();
        foreach ($indexes as $index) {
            $index = $this->fixIndexName($index);
            if (!empty($index)) {
                $result[$index] = true;
            }
        }

        if ($db->options['portability']) {
            $result = array_change_key_case($result, $db->options['field_case']);
        }

        return array_keys($result);
    }


    /**
     * @return int[]|string[]
     */
    public function listTableConstraints(string $table) : array
    {
        $db = $this->db_instance;

        $table = $db->quote($table, 'text');
        $subquery = "SELECT indexrelid FROM pg_index, pg_class";
        $subquery .= " WHERE pg_class.relname=$table AND pg_class.oid=pg_index.indrelid AND (indisunique = 't' OR indisprimary = 't')";
        $query = "SELECT relname FROM pg_class WHERE oid IN ($subquery)";
        $constraints = $db->queryCol($query);

        $result = array();
        foreach ($constraints as $constraint) {
            $constraint = $this->fixIndexName($constraint);
            if (!empty($constraint)) {
                $result[$constraint] = true;
            }
        }

        if ($db->options['portability']
            && $db->options['field_case'] == CASE_LOWER
        ) {
            $result = array_change_key_case($result, $db->options['field_case']);
        }

        return array_keys($result);
    }


    public function createSequence(string $seq_name, int $start = 1, array $options = []) : bool
    {
        $db = $this->db_instance;

        $sequence_name = $db->quoteIdentifier($db->getSequenceName($seq_name), true);

        return $db->manipulate("CREATE SEQUENCE $sequence_name INCREMENT 1" . ($start < 1 ? " MINVALUE $start" : '') . " START $start");
    }


    public function dropSequence(string $seq_name) : bool
    {
        $db = $this->db_instance;

        $sequence_name = $db->quoteIdentifier($db->getSequenceName($seq_name), true);

        return $db->manipulate("DROP SEQUENCE $sequence_name");
    }


    public function dropIndex(string $table, string $name) : bool
    {
        $db = $this->db_instance;

        $name = $this->getIndexName($name);
        $name = $db->quoteIdentifier($this->getDBInstance()->constraintName($table, $name), true);

        return $db->manipulate("DROP INDEX $name");
    }


    protected function fixIndexName(string $idx) : string
    {
        $idx_pattern = '/^' . preg_replace('/%s/', '([a-z0-9_]+)', ilDBPdoFieldDefinition::INDEX_FORMAT) . '$/i';
        $idx_name = preg_replace($idx_pattern, '\\1', $idx);
        if ($idx_name && !strcasecmp($idx, $this->db_instance->getIndexName($idx_name))) {
            return $idx_name;
        }

        return $idx;
    }


    /**
     * @param string|null $database
     * @return mixed[]
     */
    public function listSequences(string $database = null) : array
    {
        $db = $this->db_instance;

        $query = "SELECT relname FROM pg_class WHERE relkind = 'S' AND relnamespace IN";
        $query .= "(SELECT oid FROM pg_namespace WHERE nspname NOT LIKE 'pg_%' AND nspname != 'information_schema')";
        $table_names = $db->queryCol($query);

        $result = array();
        foreach ($table_names as $table_name) {
            $result[] = $this->fixSequenceName($table_name);
        }
        if ($db->options['portability']) {
            $result = array_map(($db->options['field_case'] == CASE_LOWER ? 'strtolower' : 'strtoupper'), $result);
        }
        sort($result); // FSX patch

        return $result;
    }


    public function dropConstraint(string $table, string $name, bool $primary = false) : bool
    {
        $table_quoted = $this->getDBInstance()->quoteIdentifier($table, true);
        $name = $this->getDBInstance()->quoteIdentifier($table . '_' . $this->getDBInstance()->getIndexName($name), true);

        return $this->pdo->exec("ALTER TABLE $table_quoted DROP CONSTRAINT $name");
    }
}
