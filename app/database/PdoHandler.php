<?php

namespace App\Database;

require_once "app/enums/CrudEnum.php";

use PDO;
use PDOStatement;
use App\Enums\CrudEnum;

class PdoHandler
{
    private PDO $pdo;

    public function __construct(array $configDb)
    {
        $this->pdo = new PDO(
            "mysql:host=" . $configDb['host'] . ";dbname=" . $configDb['database'],
            $configDb['user'],
            $configDb['password']
        );
    }

    public function select(string $tableName, $arrayData)
    {
        $query = $this->prepareQuery($tableName, $arrayData,  CrudEnum::SELECT);
        $results = $this->pdo->query($query);

        return $results->fetchAll(PDO::FETCH_ASSOC);
    }
    public function selectOne(string $tableName, $arrayData)
    {
        $query = $this->prepareQuery($tableName, $arrayData,  CrudEnum::SELECT);

        $stmt = $this->pdo->prepare($query);
        $this->bindParams($stmt, $arrayData['constraint']);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert(string $tableName, array $arrayData)
    {
        $query = $this->prepareQuery($tableName, $arrayData,  CrudEnum::INSERT);
        $stmt = $this->pdo->prepare($query);
        $this->bindParams($stmt, $arrayData);

        return $stmt->execute();
    }

    public function update(string $tableName, array $arrayData)
    {
        $query = $this->prepareQuery($tableName, $arrayData, CrudEnum::UPDATE);
        $stmt = $this->pdo->prepare($query);
        $this->bindParams($stmt, $arrayData['constraint']);

        return $stmt->execute();
    }

    public function delete(string $tableName, array $arrayData)
    {
        $query = $this->prepareQuery($tableName, $arrayData, CrudEnum::DELETE);
        $stmt = $this->pdo->prepare($query);
        $this->bindParams($stmt, $arrayData['constraint']);

        return $stmt->execute();
    }

    // private function convertArrayToString($array, $separator = ",")
    // {
    //     $string = "";
    //     foreach ($array as $item) {
    //         $string .= "$item" . $separator . "";
    //     }
    //     return $string;
    // }

    #query
    private function bindParams(PDOStatement $stmt, $arrayData)
    {
        foreach ($arrayData as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
    }

    private function formatQuery(array $arrayData, CrudEnum $crudEnum)
    {
        $queryArray = [];
        $queryFormatted = "";
        $constraintRequired = false;

        switch ($crudEnum) {
            case CrudEnum::SELECT:
                $columnsQuery = "";

                foreach ($arrayData['columns'] as $key) {
                    $columnsQuery .= $key;
                    if (!(end($arrayData['columns']) == $key)) {
                        $columnsQuery .= ",";
                    }
                }

                $queryArray['columns'] = $columnsQuery;
                $constraintRequired = key_exists('constraint', $arrayData);
                break;

            case CrudEnum::INSERT:
                $queryFormatted .= "(";
                $columns = "";
                $binds = "";

                foreach ($arrayData as $key => $value) {
                    $columns .= $key;
                    $binds .= ":" . $key;
                    if (!(array_key_last($arrayData) == $key)) {
                        $columns .= ",";
                        $binds .= ",";
                    }
                }

                $queryFormatted .= $columns . ") VALUES (" . $binds . ")";
                $queryArray['body'] = $queryFormatted;
                break;

            case CrudEnum::UPDATE:
                $queryFormatted .= " SET ";

                foreach (array_keys($arrayData['data']) as $key) {
                    $queryFormatted .= "$key=:$key";
                    if (!(array_key_last($arrayData['data']) == $key)) {
                        $queryFormatted .= ",";
                    }
                }
                $queryArray['body'] = $queryFormatted;
                $constraintRequired = true;

                break;

            case CrudEnum::DELETE:
                $constraintRequired = true;
                break;
        }


        if ($constraintRequired) {
            $queryConstraint = " WHERE ";
            foreach (array_keys($arrayData['constraint']) as $key) {
                $queryConstraint .= "$key=:$key";
                if (!(array_key_last($arrayData['constraint']) == $key)) {
                    $queryConstraint .= " && ";
                }
            }
            $queryArray['constraint'] = $queryConstraint;
        }

        return $queryArray;
    }

    // SELECT [columns] FROM table WHERE col_key=:col_value
    // INSERT INTO table (col, col) VALUES (:value, :value)
    // UPDATE table SET col=:value,col=:value WHERE col_key=:col_value
    // DELETE FROM table WHERE col_key=:col_value

    private function prepareQuery($tableName, $arrayData, CrudEnum $crudEnum): string
    {
        $query = "";

        switch ($crudEnum) {
            case CrudEnum::SELECT:
                $action = CrudEnum::SELECT->value;
                $queryFormatted = $this->formatQuery($arrayData, $crudEnum);
                $query = $action . " " . $queryFormatted['columns'] . ' FROM ' . $tableName;
                $query .= key_exists('constraint', $queryFormatted) ? $queryFormatted['constraint'] : '';
                break;

            case CrudEnum::INSERT:
                $action = CrudEnum::INSERT->value;
                $queryFormatted = $this->formatQuery($arrayData, $crudEnum);
                $query = $action . " " . $tableName . $queryFormatted['body'];
                break;

            case CrudEnum::UPDATE:
                $action = CrudEnum::UPDATE->value;
                $queryFormatted = $this->formatQuery($arrayData, $crudEnum);
                $query = $action . " " . $tableName;
                $query .= $queryFormatted['body'];
                $query .= key_exists('constraint', $queryFormatted) ?  $queryFormatted['constraint'] : '';
                break;

            case CrudEnum::DELETE:
                $action = CrudEnum::DELETE->value;
                $queryFormatted = $this->formatQuery($arrayData, $crudEnum);
                $query = $action . " " . $tableName . $queryFormatted['constraint'];
                break;
        }
        return $query;
    }

    #tables
    public function checkTable(string $tableName)
    {
        $stmt = $this->pdo->prepare('SHOW TABLES LIKE :table');
        $stmt->execute(['table' => $tableName]);
        return $stmt->fetch() !== false;
    }

    public function createTable(string $tableName, array $structure)
    {
        $query = "CREATE TABLE $tableName (";

        foreach ($structure as $key => $value) {
            $query .= "$key $value";
            if (!(array_key_last($structure) == $key)) {
                $query .= ",";
            }
        }

        $query .= ")";

        return $this->pdo->query($query);
    }
}
