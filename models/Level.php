<?php


namespace Model;

require_once "Model.php";

class Level extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->table = "level";
    }

    public function insert(int $school_id, string $level) {
        $school_id = intval($school_id);
        $level = htmlspecialchars($level);
        $req = $this->pdo->prepare("INSERT INTO {$this->table}(school_id, `level`) VALUES(:school_id, :level)");
        $req->execute(compact('school_id', 'level'));
    }
}