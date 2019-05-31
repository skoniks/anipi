<?php
// namespace Core;
class DB {
    private $mysqli;
    function __construct() {
        $this->mysqli = new \mysqli(config('mysql.host'), config('mysql.user'), config('mysql.password'), config('mysql.base'), config('mysql.port'));
        if($this->mysqli->connect_errno || !$this->mysqli->set_charset("utf8")) throw new \Exception('MySQL connect error');
    }
    function __destruct() {
        $this->mysqli->close();
    }
    function query($query, $mode = false) {
        $result = $this->mysqli->query($query);
        switch ($mode) {
            case 'select': if($result) {
                    while ($row = $result->fetch_assoc()) $data[] = $row;
                    return $data ?? [];
                } else return false;
            case 'insert': return $this->mysqli->insert_id;
            default: return $result;
        }
    }
    function select($query) {
        return $this->query($query, 'select');
    }
    function insert($query) {
        return $this->query($query, 'insert');
    }
    function update($query) {
        return $this->query($query);
    }
    function delete($query) {
        return $this->query($query);
    }
    function begin($write = false) {
        return $this->mysqli->begin_transaction($write ? MYSQLI_TRANS_START_READ_ONLY : MYSQLI_TRANS_START_READ_WRITE);
    }
    function commit() {
        return $this->mysqli->commit();
    }
    function rollback() {
        return $this->mysqli->rollback();
    }
    function transaction($function, $write = false) {
        $data = false;
        $this->begin($write);
        try {
            $data = $function();
        } catch (\Exception $e) {
            $this->rollback();
        }
        $this->commit();
        return $data;
    }
}
function db() {
    return $GLOBALS['DB'] = $GLOBALS['DB'] ?? new DB();
}
?>
