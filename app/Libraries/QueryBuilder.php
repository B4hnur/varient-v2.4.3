<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseBuilder;

class QueryBuilder extends BaseBuilder
{
    /**
     * Variable to store USE INDEX statements.
     */
    protected array $useIndexes = [];

    /**
     * Method to add a USE INDEX statement.
     *
     * @param string $index The index name to be used.
     * @return self
     */
    public function addUseIndex(string $index): self
    {
        $this->useIndexes[] = $index;
        return $this;
    }

    /**
     * Builds the FROM part of the query, including USE INDEX if applicable.
     *
     * @return string
     */
    protected function _fromTables(): string
    {
        $table = parent::_fromTables();

        if (FORCE_DB_INDEXES && !empty($this->useIndexes)) {
            $table .= ' USE INDEX (' . implode(', ', $this->useIndexes) . ')';
        }
        $this->useIndexes = [];
        return $table;
    }
}