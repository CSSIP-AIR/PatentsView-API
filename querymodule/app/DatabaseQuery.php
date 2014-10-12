<?php
require_once dirname(__FILE__) . '/config.php';
require_once dirname(__FILE__) . '/entitySpecs.php';
require_once dirname(__FILE__) . '/ErrorHandler.php';


class DatabaseQuery
{
    private $total_found=0;

    private $entitySpecs = array();
    private $entityGroupVars = array();

    private $selectFieldSpecs;
    private $sortFieldsUsed;

    private $db = null;
    private $errorHandler = null;

    public function getTotalFound()
    {
        return $this->total_found;
    }

    public function queryDatabase(array $entitySpecs, $whereClause, array $whereFieldsUsed, array $selectFieldSpecs, array $sortParam=null, array $options=null)
    {
        $memUsed = memory_get_usage();
        $this->errorHandler = ErrorHandler::getHandler();
        $page = 1;
        $perPage = 25;
        $getAll = false;
        $this->sortFieldsUsed = array();

        $this->entitySpecs = $entitySpecs;
        $this->setupGroupVars();

        if ($options != null) {
            if (array_key_exists('page', $options)) {
                $page = $options['page'];
            }
            if (array_key_exists('per_page', $options))
                if ($options['per_page'] == -1)
                    $getAll = true;
                else
                    $perPage = $options['per_page'];
        }

        $this->selectFieldSpecs = $selectFieldSpecs;
        $this->initializeGroupVars();
        $this->determineSelectFields();
        $from = $this->buildFrom($whereFieldsUsed, $this->selectFieldSpecs, $this->sortFieldsUsed);
        $sortString = $this->buildSortString($sortParam);

        // Get the QueryDefId for this where clause
        $stringToHash = "query::$whereClause::sort::$sortString";
        $whereHash = crc32($stringToHash);   // Using crc32 rather than md5 since we only have 32-bits to work with.
        $queryDefId = sprintf('%u', $whereHash);

        // If the query results for this where clause don't already exist, then we need to run the
        // query and cache the primary entity IDs.
        $results = $this->runQuery('QueryDefID, QueryString', 'PVSupport.QueryDef', "QueryDefID=$queryDefId", null);
        //TODO Need to handle a hash collision
        if (count($results) == 0) {
            // Ad an entry for the query
            $insertStatement ='PVSupport.QueryDef (QueryDefId, QueryString) VALUES (:queryDefId, :whereClause)';
            $this->runInsert($insertStatement, array(':queryDefId'=>$queryDefId, ':whereClause'=>$stringToHash));

            // Get all the primary entity IDs and insert into the cached results table
            $insertStatement = 'PVSupport.QueryResults (QueryDefId, Sequence, EntityId)';
            $selectPrimaryEntityIdsString =
                "distinct $queryDefId, @row_number:=@row_number+1 as sequence, " .
                getDBField($this->entityGroupVars[0]['keyId']) . " as " . $this->entityGroupVars[0]['keyId'];
            $this->runInsertSelect($insertStatement,
                $selectPrimaryEntityIdsString,
                $from . ',(select @row_number:=0) temprownum',
                $whereClause,
                $sortString);
        }

        // First find out how many there are in the complete set.
        $selectStringForEntity = 'count(*) as total_found';
        $fromEntity = $this->entitySpecs[0]['join'] .
            ' inner join PVSupport.QueryResults qr on ' . getDBField($this->entitySpecs[0]['keyId']) . '= qr.EntityId';
        $whereEntity = "qr.QueryDefId=$queryDefId";
        $countResults = $this->runQuery("distinct $selectStringForEntity", $fromEntity, $whereEntity, null);
        $this->total_found = intval($countResults[0]['total_found']);

        // Get the primary entities
        $results = array();
        $selectStringForEntity = $this->buildSelectStringForEntity($this->entitySpecs[0]);
        $fromEntity = $this->entitySpecs[0]['join'] .
            ' inner join PVSupport.QueryResults qr on ' . getDBField($this->entitySpecs[0]['keyId']) . '= qr.EntityId';
        $whereEntity = "qr.QueryDefId=$queryDefId";
        if (!$getAll)
            $whereEntity .= ' and ((qr.Sequence>=' . ((($page - 1)*$perPage)+1) . ') and (qr.Sequence<=' . $page*$perPage . '))';
        $sortEntity = 'qr.sequence';
        $entityResults = $this->runQuery("distinct $selectStringForEntity", $fromEntity, $whereEntity, $sortEntity);
        $results[$this->entitySpecs[0]['group_name']] = $entityResults;
        unset($entityResults);

        // Loop through the subentities and get them.
        foreach (array_slice($this->entitySpecs,1) as $entitySpec) {
            $tempSelect = $this->buildSelectStringForEntity($entitySpec);
            if ($tempSelect != '') { // If there aren't any fields to get back, then skip the group.
                $selectStringForEntity = getDBField($this->entitySpecs[0]['keyId']) . ' as ' . $this->entitySpecs[0]['keyId'];
                $selectStringForEntity .= ", $tempSelect";
                $fromEntity = $this->entitySpecs[0]['join'] .
                    ' inner join PVSupport.QueryResults qr on ' . getDBField($this->entitySpecs[0]['keyId']) . '= qr.EntityId';
                $fromEntity .= ' ' . $entitySpec['join'];
                $whereEntity = "qr.QueryDefId=$queryDefId";
                if (!$getAll)
                    $whereEntity .= ' and ((qr.Sequence>=' . ((($page - 1)*$perPage)+1) . ') and (qr.Sequence<=' . $page*$perPage . '))';
                $sortEntity = 'qr.sequence';
                $entityResults = $this->runQuery("distinct $selectStringForEntity", $fromEntity, $whereEntity, $sortEntity);
                $results[$entitySpec['group_name']] = $entityResults;
                unset($entityResults);
            }
        }

        return $results;
    }


    // This was the function that was used when we did one large all combined join with all fields.
    // GY 2014-10-11: Just keeping it here for reference for a short time. If we don't come back to it, then we can delete it.
/*
    public function queryDatabase_SingleQuery(array $entitySpecs, $whereClause, array $whereFieldsUsed, array $selectFieldSpecs, array $sortParam=null, array $options=null)
    {
        $page = 1;
        $perPage = 25;
        $getAll = false;
        $this->sortFieldsUsed = array();
        $this->entitySpecs = $entitySpecs;

        $this->setupGroupVars();

        $this->errorHandler = ErrorHandler::getHandler();

        $this->selectFieldSpecs = $selectFieldSpecs;
        $this->initializeGroupVars();
        $this->determineSelectFields();
        $selectString = $this->buildSelectString();
        $sortString = $this->buildSortString($sortParam);
        $from = $this->buildFrom($whereFieldsUsed, $this->selectFieldSpecs, $this->sortFieldsUsed);

        if ($options != null) {
            if (array_key_exists('page', $options)) {
                $page = $options['page'];
            }
            if (array_key_exists('per_page', $options))
                if ($options['per_page'] == -1)
                    $getAll = true;
                else
                    $perPage = $options['per_page'];
        }

        // If getAll, then just get all the data.
        if ($getAll) {
            $results = $this->runQuery("distinct $selectString", $from, $whereClause, $sortString);
            $primaryEntityIds = array();
            foreach ($results as $row)
                $primaryEntityIds[$row[$this->groupVars[0]['keyId']]] = 1;
            $this->total_found = count($primaryEntityIds);
        }
        // If get a range, then first get all the IDs, and then get the IDs in that range and use
        // as the WHERE to get the data rows
        else {
            // Get the primary entity IDs
            $selectPrimaryEntityIdsString = "distinct " . getDBField($this->groupVars[0]['keyId']) . " as " .
                $this->groupVars[0]['keyId'];
            $results = $this->runQuery("$selectPrimaryEntityIdsString", $from, $whereClause, $sortString);
            $this->total_found = count($results);

            // Make sure they asked for a valid range.
            if (($page-1)*$perPage > count($results))
                $results = null;
            else {
                $primaryEntityIds = array();
                foreach (array_slice($results, ($page - 1) * $perPage, $perPage) as $row)
                    $primaryEntityIds[] = $row[$this->groupVars[0]['keyId']];

                $whereClause = getDBField($this->groupVars[0]['keyId']) . ' in ("' . join('","', $primaryEntityIds) . '") ';
                $selectString = "distinct $selectString";
                $results = $this->runQuery("$selectString", $from, $whereClause, $sortString);
            }
        }

        return $results;
    }
*/

    private function runQuery($select, $from, $where, $order)
    {
        $this->connectToDB();

        if (strlen($where) > 0) $where = "WHERE $where ";
        if (strlen($order) > 0) $order = "ORDER BY $order";
        $sqlQuery = "SELECT $select FROM $from $where $order";
        $this->errorHandler->getLogger()->debug($sqlQuery);

        try {
            $st = $this->db->query("$sqlQuery", PDO::FETCH_ASSOC);
            $results = $st->fetchAll();
            $st->closeCursor();
        }
        catch (Exception $e) {
            $this->errorHandler->sendError(400, "Query execution failed.", $e);
            throw new $e;
        }

        return $results;
    }

    private function runInsertSelect($insert, $select, $from, $where, $order)
    {
        $this->connectToDB();

        if (strlen($where) > 0) $where = "WHERE $where ";
        if (strlen($order) > 0) $order = "ORDER BY $order";
        $sqlQuery = "INSERT INTO $insert SELECT $select FROM $from $where $order";
        $this->errorHandler->getLogger()->debug($sqlQuery);

        try {
            $st = $this->db->prepare($sqlQuery);
            $results = $st->execute();
            $st->closeCursor();
        }
        catch (Exception $e) {
            $this->errorHandler->sendError(400, "Insert select execution failed.", $e);
            throw new $e;
        }

        return $results;
    }

    private function runInsert($insert, $params)
    {
        $this->connectToDB();

        $sqlStatement = "INSERT INTO $insert";
        $this->errorHandler->getLogger()->debug($sqlStatement);
        $this->errorHandler->getLogger()->debug($params);

        try {
            $st = $this->db->prepare($sqlStatement);
            $results = $st->execute($params);
            $st->closeCursor();
        }
        catch (Exception $e) {
            $this->errorHandler->sendError(400, "Insert execution failed.", $e);
            throw new $e;
        }

        return $results;
    }

    private function initializeGroupVars()
    {
        foreach ($this->entityGroupVars as $group) {
            $this->{$group['hasId']} = false;
            $this->{$group['hasFields']} = false;
        }

        foreach ($this->selectFieldSpecs as $apiField => $dbInfo) {
            foreach ($this->entityGroupVars as $group) {
                if ($dbInfo['field_name'] == $group['keyId'])
                    $this->{$group['hasId']} = true;
                if ($dbInfo['table'] == $group['table'])
                    $this->{$group['hasFields']} = true;
            }
        }
    }


    private function determineSelectFields()
    {
        global $FIELD_SPECS;

        foreach ($this->entityGroupVars as $group) {
            if ($group['entity_name'] == $this->entityGroupVars[0]['entity_name']) {
                if (!$this->{$group['hasId']})
                    $this->selectFieldSpecs[$group['keyId']] = $FIELD_SPECS[$group['keyId']];
            } else {
                if ($this->{$group['hasFields']} and !$this->{$group['hasId']})
                    $this->selectFieldSpecs[$group['keyId']] = $FIELD_SPECS[$group['keyId']];
            }
        }
    }


    private function buildSelectString()
    {
        $selectString = '';

        foreach ($this->selectFieldSpecs as $apiField => $dbInfo) {
            if ($selectString != '')
                $selectString .= ', ';
            $selectString .= "$dbInfo[table].$dbInfo[field_name] as $apiField";
        }

        return $selectString;
    }

    private function buildSelectStringForEntity($entitySpec)
    {
        $selectString = '';

        foreach ($this->selectFieldSpecs as $apiField => $dbInfo) {
            if ($dbInfo['entity_name'] == $entitySpec['entity_name']) {
                if ($selectString != '')
                    $selectString .= ', ';
                $selectString .= "$dbInfo[table].$dbInfo[field_name] as $apiField";
            }
        }

        return $selectString;
    }


    private function buildSortString($sortParam)
    {
        global $FIELD_SPECS;
        $orderString = '';
        if ($sortParam != null) {
            foreach ($sortParam as $sortField) {
                $apiField = key($sortField);
                $direction = current($sortField);
                try {
                    $fieldSpec = $FIELD_SPECS[$apiField];
                }
                catch (ErrorException $e) {
                    ErrorHandler::getHandler()->sendError(400, "Invalid field for sorting: $apiField");
                    throw $e;
                }
                if ($fieldSpec['table'] == $this->entityGroupVars[0]['table']) {
                    if (($direction != 'asc') and ($direction != 'desc')) {
                        ErrorHandler::getHandler()->sendError(400, "Not a valid direction for sorting: $direction");
                        throw new ErrorException("Not a valid direction for sorting: $direction");
                    }
                    else {
                        if ($orderString != '')
                            $orderString .= ', ';
                        $orderString .= getDBField($apiField) . ' ' . $direction;
                        $this->sortFieldsUsed[] = $apiField;
                    }
                } else {
                    $msg = "Not a valid field for sorting, it must be a " . $this->entityGroupVars[0]['entity_name'] . " field: $apiField";
                    ErrorHandler::getHandler()->sendError(400, $msg);
                    throw new ErrorException($msg);
                }
            }
        }

        if ($orderString != '')
            $orderString .= ', ';
        $orderString .= getDBField($this->entityGroupVars[0]['keyId']);
        return $orderString;
    }

    private function buildFrom(array $whereFieldsUsed, array $selectFieldSpecs, array $sortFields)
    {
        global $FIELD_SPECS;
        // Smerge all the fields into one array
        $allFieldsUsed = array_merge($whereFieldsUsed, array_keys($selectFieldSpecs), $sortFields);
        $allFieldsUsed = array_unique($allFieldsUsed);

        $fromString = '';
        $joins = array();

        // We need to go through the entities in order so the joins are done in the same order as they appear
        // in the entity specs.
        foreach ($this->entityGroupVars as $group)
            foreach ($allFieldsUsed as $apiField)
                if ($group['table'] == $FIELD_SPECS[$apiField]['table'])
                    if (!in_array($group['join'], $joins))
                        $joins[] = $group['join'];

        foreach ($joins as $join) {
            $fromString .= ' ' . $join . ' ';
        }

        return $fromString;
    }

    private function setupGroupVars()
    {

        $this->entityGroupVars = $this->entitySpecs;
        foreach ($this->entityGroupVars as &$group) {
            $name = $group['entity_name'];
            $group['hasId'] = "alreadyHas{$name}Id";
            $group['hasFields'] = "has{$name}Fields";
        }
        unset($group);
   }

    private function connectToDB()
    {
        global $config;
        if ($this->db === null) {
            $dbSettings = $config->getDbSettings();
            try {
                $this->db = new PDO("mysql:host=$dbSettings[host];dbname=$dbSettings[database];charset=utf8", $dbSettings['user'], $dbSettings['password']);
                $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                $this->errorHandler->sendError(500, "Failed to connect to database: $dbSettings[database].", $e);
                throw new $e;
            }
        }
    }

}