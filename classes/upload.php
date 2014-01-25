<?php
namespace classes;
use classes\SimpleImage;
use application\core\config\config;
use PDO;

class Upload extends SimpleImage
{
    private $fileName;
    public $conn;

    public function __construct()
    {
        $this->conn = Config::GetInstance();
    }

    public function UploadImgTinyMce($path)
    {
        foreach ($_FILES as $key => $value)
        {
            $ext = "." . pathinfo($value['name'], PATHINFO_EXTENSION);
            $name = $path . "/" . md5(microtime() + rand(0, 10000));
            $fileName = $name . $ext;
            if($_POST['type-file'] == "img")
                $this->load($value['tmp_name'])->save($fileName);
            else
                $this->UploadFile($value['tmp_name'], $fileName);
        }
        echo $fileName;
    }

    public function UploadImg($path)
    {

        foreach ($_FILES as $key => $value)
        {
            $this->fileName = $key;
            $ext = "." . pathinfo($value['name'], PATHINFO_EXTENSION);
            $name = $path . "/" . md5(microtime() + rand(0, 10000));
            $fileName = $name . $ext;
            if($key == "source_img_s")
            {
                $this->ImgIconGame($value, $fileName, $ext);
            }
            else if($key == "source_img_b")
            {
                $this->ImgLicenceGame($value, $fileName, $ext);
            }
            else
            {
                if($_POST['type-file'] == "img")
                    $this->load($value['tmp_name'])->save($fileName);
                else
                    $this->UploadFile($value['tmp_name'], $fileName);
            }
        }
        echo json_encode(array($fileName, $this->fileName));
    }

    // Метод загрузки изображений в папку пользователя
    public function UploadUserGameImg()
    {
        foreach($_FILES as $k=>$v)
        {
            $path = "storage/user_img/" . $_SESSION["user-data"]["path"];
            $ext = "." . pathinfo($v["name"], PATHINFO_EXTENSION);
            $name = $path . "/" . md5(microtime() + rand(1, 10000));
            $fileName = $name . $ext;
            $smallFileName = $name . "_s" . $ext;
            $imgB = $this->UploadFile($v['tmp_name'], $fileName);
            $imgS = $this->load($fileName)->square_crop(200)->save($smallFileName);
            if($imgB && $imgS){
                $idUser = (int)$_SESSION['user-data']['id'];
                $idGame = (int)$_SESSION['id-game'];
                $gameImgB = "/".$fileName;
                $gameImgS = "/".$smallFileName;
                $test = $this->conn->dbh->query("INSERT INTO user_game_img(id_user, id_game, game_img_b, game_img_s) VALUES (".$idUser.", ".$idGame.", '".$gameImgB."', '".$gameImgS."')");
            }


        }
        echo json_encode(array($fileName, $k));
    }

    private function ImgIconGame($value, $fileName, $ext)
    {
        if($ext == ".png")
        {
            $this->load($value['tmp_name'])->square_crop(128)->save($fileName);
        }
        else
        {
            echo json_encode(array("Формат файла только PNG"));
            exit();
        }
    }

    private function ImgLicenceGame($value, $fileName, $ext)
    {
        if($ext == ".jpg")
        {
            $this->load($value['tmp_name'])->square_crop(400)->save($fileName);
        }
        else
        {
            echo json_encode(array("Формат файла только JPG"));
            exit();
        }
    }

    private function UploadFile($tmpName, $fileName)
    {
        return move_uploaded_file($tmpName, $fileName);
    }
}