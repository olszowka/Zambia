<?php
/**
 * Add useful methods to PDO class.  All methods throw an exception on error.
 */

class XPDO extends PDO
{
    var $stmt;

    /**
     * Perform a select and return all the data.
     *
     * @param string $sql        - SQL to execute
     * @param array  $params     - array of parameters for placeholders, if any
     * @param int    $fetchStyle - How each row is represented: PDO::FETCH_ASSOC (default),
     *                             PDO::FETCH_OBJ, PDO::FETCH_NUM, PDO::FETCH_COLUMN
     *
     * @return array - selected data as array of rows
     * @throws PDOException
     */
    public function selectAll($sql, array $params = array(), $fetchStyle = PDO::FETCH_ASSOC)
    {
        $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $stmt = $this->prepare($sql);
        if ($stmt->execute($params) === false) {
            throw new PDOException("Error executing '$sql'\n");
        }
        return $stmt->fetchAll($fetchStyle);
    }

    /**
     * Perform a select and return the first row.  To be used with queries
     * that always return one row.
     *
     * @param string $sql        - SQL to execute
     * @param array  $params     - array of parameters for placeholders, if any
     * @param int    $fetchStyle - PDO::FETCH_ASSOC, PDO::FETCH_OBJ, PDO::FETCH_NUM, etc.
     *
     * @return mixed - returned array, object or scalar
     * @throws PDOException
     */
    public function selectOne($sql, array $params = array(), $fetchStyle = PDO::FETCH_ASSOC)
    {
        $rows = $this->selectAll($sql, $params, $fetchStyle);
        if (count($rows) >= 1) {
            return $rows[0];
        } else {
            return null;
        }
    }

    /**
     * Prepare and execute the given parameterized SQL
     *
     * @param string $sql    - SQL to execute, with placeholders
     * @param array  $params - values for placeholders (optional)
     *
     * @return int - number of rows affected
     * @throws PDOException
     */
    public function queryParams($sql, array $params = array())
    {
        $this->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $stmt = $this->prepare($sql);
        if ($stmt->execute($params) === false) {
            throw new PDOException("Error executing '$sql'\n");
        }
	return $stmt->rowCount();
    }

    /**
     * Load entire table - shorthand
     *
     * @param string        $table      - table name
     * @param array(string) $cols       - column names, or '*' for all
     * @param int           $fetchStyle - (opt.) how to return rows, default FETCH_ASSOC
     *
     * @return array(rows) - contents of table, ordered by $cols[0]
     * @throws PDOException
     */
    public function loadTable($table, array $cols, $fetchStyle = PDO::FETCH_ASSOC)
    {
        $sql = 'select ' . join(',', $cols) . " from $table";
        return $this->selectAll($sql, array(), $fetchStyle);
    }

    /**
     * Insert one row into the given table.
     * The data should be an associative array; the keys are used as the column names.
     *
     * @param string $table - table name
     * @param array  $data  - associative array of data
     *
     * @return bool - true on success - throws exception on failure
     * @throws PDOException
     */
    public function insertRow($table, array $data)
    {
        $colnames = array_keys($data);
        $clist = join(',', $colnames);
        $plist = join(',', array_fill(0, count($data), '?'));
        $values = array_values($data);
        return $this->queryParams("insert into $table($clist) values($plist)", $values);
    }

    /**
     * Function to insert many rows of data into a table.  Uses extended INSERT.
     * Takes input as 2D numeric array to save RAM.
     *
     * @param string               $table    - table name
     * @param array(string)        $colnames - array of column names
     * @param array(array(scalar)) $data     - 2D array of data
     * @param int                  $max      - (opt) max # of items in one insert, default 5000
     *
     * @return string - returns error message on failure, empty string on success
     * @throws PDOException
     */
    public function insertRows($table, array $colnames, array $data, $max = 5000)
    {
        $n_rows = count($data);
        $n_cols = count($colnames);

        $err_prefix = "insertRows('$table', ...)";

        if ($n_rows == 0) {
            return 0;
        }
        if ($n_cols == 0) {
            throw PDOException("$err_prefix: empty array of column names");
        }
        if ($max < $n_cols) {
            throw PDOException("$err_prefix: invalid max batch size $max: must be at least number of columns ($n_cols)");
        }

        /**
         * MySQL can accept a lot of data in one insert, but it does have a limit
         * (max_packet_size).  We handle this by limiting the total number of fields
         * we insert.  (Ideally we would take field size into account as well.)
         * Split the insert into batches of no more than $max values at a time.
         * Each batch must insert complete rows.
         */
        $rows_per_batch = floor($max / $n_cols);
        $rows_this_batch = $rows_per_batch;  /* Suppress spurious Eclipse complaint */
        for ($row_i = 0; $row_i < $n_rows; $row_i += $rows_this_batch) {
            $rows_this_batch = min($n_rows - $row_i, $rows_per_batch);
            /**
             * Get the rows to insert this time into a flat array.
             */
            $flat = array();
            for ($i = $row_i; $i < $row_i + $rows_this_batch; $i++) {
                if (count($data[$i]) != $n_cols) {
                    throw PDOException("$err_prefix: data row $i should have $n_cols elements but has " . count($data[$i]));
                }
                foreach ($data[$i] as $datum) {
                    $flat[] = $datum;
                }
            }

            /**
             * Prepare the variable parts of the SQL.
             */
            $clist = join(',', $colnames);
            $plist = join(',', array_fill(0, $n_cols, '?'));
            $vlist = join(',', array_fill(0, $rows_this_batch, "($plist)"));

            /**
             * Do the insert!
             */
            $this->queryParams("insert into $table($clist) values $vlist", $flat);
        }
        return '';
    }
}
