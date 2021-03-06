<?php

namespace Dcat\Utils\Eloquent;

use Illuminate\Database\Eloquent;
use Illuminate\Database\Query;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Support\Arr;

class QueryExtension
{
    /**
     * Mysql 保存或新增功能.
     */
    public static function insertOrReplace()
    {
        $makeInsertOrUpdateSuffix = function ($grammar, array &$values) {
            /* @var Grammar $grammar */

            $sql = ' ON DUPLICATE KEY UPDATE ';

            $suffix = [];
            foreach (array_keys(reset($values)) as $index => $column) {
                $column = $grammar->wrap($column);

                $suffix[] = "{$column} = values({$column})";
            }

            return $sql.implode(', ', $suffix);
        };

        Eloquent\Builder::macro('insertOrReplace', function ($values) {
            return $this->query->insertOrReplace($values);
        });
        Query\Builder::macro('insertOrReplace', function (array $values) use ($makeInsertOrUpdateSuffix) {
            if (empty($values)) {
                return true;
            }

            if (! is_array(reset($values))) {
                $values = [$values];
            } else {
                foreach ($values as $key => $value) {
                    ksort($value);

                    $values[$key] = $value;
                }
            }

            $sql = $this->grammar->compileInsert($this, $values);

            return $this->connection->insert(
                $sql.$makeInsertOrUpdateSuffix($this->grammar, $values),
                Arr::flatten($values, 1)
            );
        });
    }
}
