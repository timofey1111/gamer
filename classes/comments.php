<?php
namespace classes;
use application\core\config\config;
use PDO;

class Comments
{
    private $_tableName;
    public $conn;
    public $_p, $_g = array();

    public function __construct()
    {
        $this->_p = $_POST;
        $this->_g = $_GET;
        $this->conn = Config::GetInstance();
        if(!empty($_POST['table-id'])){
            switch($_POST['table-id']){
                case 1:
                    $this->_tableName="comments_tournament";
                    break;
                case 3:
                    $this->_tableName="comments_user_completed_games";
                    break;
                default:
                    $this->tableName=false;
            }
        }
    }
    public function AddComment()
    {
        // todo сделать проверку на существование элемента и не соответствии текущему user
        // todo сделать проверку на id турнира
        $stmt = $this->conn->dbh->prepare("INSERT INTO ".$this->_tableName." (`date`,`id_user`,`id_user_answer`, `comment`, `id_section`) VALUES(UNIX_TIMESTAMP(NOW()), :id_user, :id_user_answer, :comment, :id_section)");
        $stmt->bindParam(":id_user", $_SESSION['user-data']['id'], PDO::PARAM_INT);
        $stmt->bindParam(":id_user_answer", $_POST['id-user-answer'], PDO::PARAM_INT);
        $stmt->bindParam(":comment", $_POST['comment'], PDO::PARAM_STR);
        $stmt->bindParam(":id_section", $_POST['id-section'], PDO::PARAM_INT);
        $stmt->execute();
        $id = $this->conn->dbh->lastInsertId();
        return json_encode($this->LastComment($id));
    }

    public function ListComments()
    {
        $result = $this->conn->dbh->query("SELECT  ".$this->_tableName.".*, u.nick, u.img_avatar, u2.nick as nick_answer, u2.img_avatar as img_avatar_answer  FROM ".$this->_tableName."
                                                LEFT JOIN users u ON ".$this->_tableName.".id_user = u.id
                                                LEFT JOIN users u2 ON ".$this->_tableName.".id_user_answer = u2.id WHERE ".$this->_tableName.".id_section = ". $_POST['id-section']." LIMIT 0, 1000")->fetchAll(PDO::FETCH_OBJ);
        $result[] = $_SESSION['user-data']['id']."-".$_SESSION['auth'];
        return json_encode($result);
    }

    public function RemoveComment()
    {
        $this->conn->dbh->query("DELETE FROM ".$this->_tableName." WHERE id = ".(int)$_POST['id']. " AND id_user = ". $_SESSION['user-data']['id'] . " AND id_section = ".$_POST['id-section']);
    }

    public function MarkSpam()
    {
        $stmt = $this->conn->dbh->exec("INSERT INTO spam_materials (date, id_user, id_comment, id_section, comment_product) VALUES (UNIX_TIMESTAMP(NOW()), :id_user, :id_comment, :id_section, :comment_product)");
        $stmt->bindParam(":id_user", $_POST['id-user'], PDO::PARAM_INT);
        $stmt->bindParam(":id_comment", $_POST['id-comment'], PDO::PARAM_INT);
        $stmt->bindParam(":id_section", $_POST['id-section'], PDO::PARAM_INT);
        $stmt->bindParam(":comment_product", $_POST['comment-product'], PDO::PARAM_INT);
        $stmt->execute();
    }

    private function LastComment($id)
    {
        $result = array();
        $result[] = $this->conn->dbh->query("SELECT ".$this->_tableName.".*, u.nick, u.img_avatar, u2.nick as nick_answer, u2.img_avatar as img_avatar_answer  FROM ".$this->_tableName."
                                                LEFT JOIN users u ON ".$this->_tableName.".id_user = u.id
                                                LEFT JOIN users u2 ON ".$this->_tableName.".id_user_answer = u2.id WHERE ".$this->_tableName.".id = ". (int)$id. " AND ".$this->_tableName.".id_section =".$_POST['id-section'])->fetchAll(PDO::FETCH_OBJ)[0];
        $result[] = $_SESSION['user-data']['id']."-".$_SESSION['auth'];
        return $result;
    }

}


