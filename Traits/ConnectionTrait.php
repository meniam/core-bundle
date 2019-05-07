<?php

namespace Meniam\Bundle\CoreBundle\Traits;

use \PDO;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\FetchMode;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @property ContainerInterface $container
 */
trait ConnectionTrait
{
    use LoggerTrait;

    /**
     * @var EntityManager|object
     */
    private $connectionTraitEm;

    /**
     * @param EntityManagerInterface $em
     */
    public function setConnectionTraitEm(EntityManagerInterface $em)
    {
        $this->connectionTraitEm = $em;
    }

    /**
     * @return EntityManager|object
     */
    protected function getEm()
    {
        if ($this->connectionTraitEm) {
            return $this->connectionTraitEm;
        }

        if (!$this->container->has(EntityManagerInterface::class)) {
            throw new LogicException('The EntityManagerInterface is not registered in your application.');
        }

        $this->connectionTraitEm = $this->container->get(EntityManagerInterface::class);
        return $this->connectionTraitEm;
    }

    /**
     * Shortcut to return the Doctrine Registry service.
     *
     * @throws LogicException If DoctrineBundle is not available
     *
     * @final
     */
    protected function getDoctrine(): ManagerRegistry
    {
        if (!$this->container->has('doctrine')) {
            throw new LogicException('The DoctrineBundle is not registered in your application. Try running "composer require symfony/orm-pack".');
        }

        return $this->container->get('doctrine');
    }

    /**
     * @return Connection
     */
    public function getConn()
    {
        return $this->getEm()->getConnection();
    }

    /**
     * Starts a transaction by suspending auto-commit mode.
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->getConn()->beginTransaction();
    }

    /**
     * Commits the current transaction.
     *
     * @return void
     */
    public function commit()
    {
        try {
            $this->getConn()->commit();
        } catch (ConnectionException $e) {
            $this->getLogger()->error('SQL Commit Failed', ['e' => $e->getMessage()]);
        }
    }

    /**
     * Cancels any database changes done during the current transaction.
     */
    public function rollBack()
    {
        try {
            $this->getConn()->rollBack();
        } catch (ConnectionException $e) {
            $this->getLogger()->error('SQL RollBack Failed', ['e' => $e->getMessage()]);
        }
    }

    /**
     * Prepares and executes an SQL query and returns the result as an associative array.
     *
     * @param string $sql    The SQL query.
     * @param array  $params The query parameters.
     * @param array  $types  The query parameter types.
     * @return array
     */
    public function fetchAll($sql, array $params = [], $types = [])
    {
        return $this->executeQuery($sql, $params, $types)->fetchAll();
    }

    /**
     * Prepares and executes an SQL query and returns the first row of the result
     * as an associative array.
     *
     * @param string $statement The SQL query.
     * @param array  $params    The query parameters.
     * @param array  $types     The query parameter types.
     * @return array|bool False is returned if no rows are found.
     */
    public function fetchAssoc($statement, array $params = [], array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetch(FetchMode::ASSOCIATIVE);
    }

    /**
     * Prepares and executes an SQL query and returns the value of a single column
     * of the first row of the result.
     *
     * @param string $statement The SQL query to be executed.
     * @param array  $params    The prepared statement params.
     * @param int    $column    The 0-indexed column number to retrieve.
     * @param array  $types     The query parameter types.
     * @return mixed|bool False is returned if no rows are found.
     */
    public function fetchColumn($statement, array $params = [], $column = 0, array $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetchColumn($column);
    }

    public function prepareMultipleValues($data, $includeFields = [], $excludeFields = [], $cast = [])
    {
        $sql = '';
        $params = [];
        $i = 0;
        $conn = $this->getConn();
        foreach ($data as $item) {
            $sqlValue = '';
            if (!empty($includeFields)) {
                uksort(
                    $item,
                    function ($a, $b) use ($includeFields) {
                        return array_search($a, $includeFields) <=> array_search($b, $includeFields);
                    }
                );
            }

            foreach ($item as $key => $value) {
                $isFieldIncluded = (!empty($includeFields) && in_array($key, $includeFields)) || empty($includeFields);
                $isFieldExcluded = (!empty($excludeFields) && in_array($key, $excludeFields));

                if ($isFieldIncluded && !$isFieldExcluded) {
                    $castType = isset($cast[$key]) ? $cast[$key] : '';
                    if (is_bool($value)) {
                        $value = $value ? 'TRUE' : 'FALSE';
                    }
                    $castTypeStr = $i ? '' : $castType;
                    if ($castType) {
                        $sqlValue .= ",{$castTypeStr} ".$conn->quote($value, PDO::PARAM_STR);
                    } else {
                        $sqlValue .= ", :{$key}__{$i}";
                        $params["{$key}__{$i}"] = $value;
                    }
                }
            }

            $sqlValue = ltrim($sqlValue, ', ');
            $sql .= ",\n({$sqlValue})";
            $i++;
        }

        $sql = ltrim($sql, ', ');

        return [$sql, $params];
    }

    /**
     * Executes an, optionally parametrized, SQL query.
     * If the query is parametrized, a prepared statement is used.
     * If an SQLLogger is configured, the execution is logged.
     *
     * @param string                 $query  The SQL query to execute.
     * @param array                  $params The parameters to bind to the query, if any.
     * @param array                  $types  The types the previous parameters are in.
     * @param QueryCacheProfile|null $qcp    The query cache profile, optional.
     * @return Statement|false The executed statement.
     */
    public function executeQuery($query, array $params = [], $types = [], QueryCacheProfile $qcp = null)
    {
        $result = false;
        try {
            $result = $this->getConn()->executeQuery($query, $params, $types, $qcp);
        } catch (DBALException $e) {
            $message = $e->getMessage();
            $message = preg_replace('#VALUES(.*?)ON#usi', '{{VALUES}}', $message);
            $message = preg_replace('#with params\s*\[.*?\]#usi', 'with params [{{PARAMS}}]', $message);

            $this->getLogger()->error('SQL Execute Error', [$message, 'sql' => $query, 'params' => $params, 'types' => $types]);
        }

        return $result;
    }

    /**
     * Executes an, optionally parametrized, SQL query.
     * If the query is parametrized, a prepared statement is used.
     * If an SQLLogger is configured, the execution is logged.
     *
     * @param string                 $query  The SQL query to execute.
     * @param array                  $params The parameters to bind to the query, if any.
     * @param array                  $types  The types the previous parameters are in.
     * @param QueryCacheProfile|null $qcp    The query cache profile, optional.
     * @return array|Statement|false The executed statement.
     */
    public function fetchPairs($query, array $params = [], $types = [], QueryCacheProfile $qcp = null)
    {
        $result = false;
        try {
            $result = $this->getConn()->executeQuery($query, $params, $types, $qcp)->fetchAll(PDO::FETCH_KEY_PAIR);
        } catch (DBALException $e) {
            $this->getLogger()->error('SQL Execute Error', ['sql' => $query, 'params' => $params, 'types' => $types, 'e' => $e->getMessage()]);
        }

        return $result;
    }

    /**
     * Inserts a table row with specified data.
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression The expression of the table to insert data into, quoted or unquoted.
     * @param array  $data            An associative array containing column-value pairs.
     * @param array  $types           Types of the inserted data.
     * @return int The number of affected rows.
     */
    public function insert($tableExpression, array $data, array $types = [])
    {
        $result = false;
        try {
            $result = $this->getConn()->insert($tableExpression, $data, $types);
        } catch (DBALException $e) {
            $this->getLogger()->error('SQL Execute Error', ['table' => $tableExpression, 'data' => $data, 'types' => $types, 'e' => $e->getMessage()]);
        }

        return $result;
    }

    /**
     * Executes an SQL UPDATE statement on a table.
     * Table expression and columns are not escaped and are not safe for user-input.
     *
     * @param string $tableExpression The expression of the table to update quoted or unquoted.
     * @param array  $data            An associative array containing column-value pairs.
     * @param array  $identifier      The update criteria. An associative array containing column-value pairs.
     * @param array  $types           Types of the merged $data and $identifier arrays in that order.
     * @return int The number of affected rows.
     */
    public function update($tableExpression, array $data, array $identifier, array $types = [])
    {
        $result = false;
        try {
            $result = $this->getConn()->update($tableExpression, $data, $identifier, $types);
        } catch (DBALException $e) {
            $this->getLogger()->error('SQL Execute Error', ['table' => $tableExpression, 'data' => $data, 'identifier' => $identifier, 'types' => $types, 'e' => $e->getMessage()]);
        }

        return $result;
    }
}
