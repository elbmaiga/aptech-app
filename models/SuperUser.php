<?php


namespace Model;

require_once 'Model.php';
require_once '../libraries/Mail.php';

use App\Mail;


class SuperUser extends Model
{
    /**
     * @var int
     */
    private int $id;

    /**
     * @var string
     */
    private string $first_name;

    /**
     * @var string
     */
    private string $last_name;

    /**
     * @var string
     */
    private string $birth_date;

    /**
     * @var string
     */
    private string $sexe;

    /**
     * @var string
     */
    private string $username;

    /**
     * @var string
     */
    private string $email;

    /**
     * @var string
     */
    private string $password;

    /**
     * @var int
     */
    private int $accreditation;

    /**
     * Default password charactere generate auto
     */
    private const PASSWORD_LENGTH = 8;

    /**
     * Default min charactere able to repeat on generate password
     */
    private const PASSWORD_CHARACTER_REPEAT_MIN = self::PASSWORD_LENGTH - self::PASSWORD_LENGTH;

    /**
     * Default max charactere able to repeat on generate password
     */
    private const PASSWORD_CHARACTER_REPEAT_MAX = self::PASSWORD_LENGTH - 2;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->table = "super_users";
        parent::__construct();
    }

    /**
     * @param string|null $query
     * @return array
     */
    public function findAll(?string $query = NULL, ?bool $use_foreign_key = true) {
        if($query) {
            if($use_foreign_key) {
                $sql = "SELECT user_category.id AS category_id, user_category.field, users.id AS id, users.user_category_id AS user_category_id, users.school_id AS school_id, users.last_name, users.first_name, users.birth_date, users.sexe, users.username, users.email, users.profile FROM {$this->table}";
            } else {
                $sql = "SELECT * FROM {$this->table}";
            }
            $sql .= " ". $query;
        } else {
            $sql = "SELECT * FROM {$this->table}";
        }

        $req = $this->pdo->query($sql);

        return $req->fetchAll();
    }

    /**
     * @param int $user_category_id
     * @param int $school_id
     * @param string $last_name
     * @param string $first_name
     * @param string $birth_date
     * @param string $sexe
     * @param string $email
     * @throws \Exception
     */
    public function insert() {


        // Sending mail
        $mail = new Mail();
        $message = "
<html>
<body  style='background: #eeeeee;'>
<header>
    <div style='text-align: center; font-weight: bold'><a href='#'>aptechapp-com</a></div>
</header>
<h1 style='background: #009688; color: white; padding: 12px; font-size: 18px; text-align: center; font-weight: bold;'>Bravo $last_name $first_name Vous avez été inscris avec succès sur aptechapp.com</h1>
<section style='background: #FFFFFF; padding: 2rem;'>
    <p>Salut <b>$first_name</b> <b>$last_name</b>, <br> 
    vous venez d'être membre de la plateforme aptechapp. Ce mail est confidentiel, il contient vos identifiants de connexion, vous devez alors le gardé en sécurité !</p>
    <p>Vos identifiants sont les suivants: <br> Email: <b>$email</b> <br> Identifiant: <b>$username</b> <br> Mot de passe: <b>$passwordn</b></p>
    <p>Connectez-vous avec vos identifiants sur: <span style='background: #009688; padding: 2px; color: #FFFFFF'><a href='https://aptech-app-2.000webhostapp.com/' style='color: white'>aptech.com</a></span></p>
    <p>Vous pouvez changer votre mot de passe et votre identifiant une fois connecté.</p>
    <p>Sachez qu'il n'est pas nécessaire de répondre à ce mail.</p>
</section>
</body>
</html>
";
        $mail->sendMail("bmaiga08@gmail.com", "Votre inscription sur APTECHAPP.com a bien été pris en compte !", $message);
    }


    /**
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function login(string $username, string $password) {
        $username = htmlspecialchars($username);
        $password = sha1($password);
        $req = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE (username = :username OR email = :username) AND password = :password");
        $req->execute(compact('username', 'password'));
        $user = $req->rowCount();
        if($user === 1) {
            $user_info = $req->fetch();
            // Hydration
            $this->setId($user_info['id']);
            $this->setLastName($user_info['last_name']);
            $this->setFirstName($user_info['first_name']);
            $this->setBirthDate($user_info['birth_date']);
            $this->setSexe($user_info['sexe']);
            $this->setUsername($user_info['username']);
            $this->setEmail($user_info['email']);
            $this->setPassword($user_info['password']);
            $this->setAccreditation($user_info['accreditation']);
            return true;
        } else {
            return false;
        }

    }

    /**
     * @param string $first_name
     * @param string $last_name
     * @param string $email
     * @return string
     */
    public function getDefaultUsername(string $first_name, string $last_name, string $email):string {
        $username = explode("@", $email);
        $username = $username[0];
        $username = $username. "@_". $last_name.$first_name;
        return $username;
    }

    /**
     * @param string $username
     * @return bool
     */
    public function verifyUsername(string $username):bool {
        $req = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE username = :username");
        $req->execute(compact('username'));
        if($req->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $email
     * @return bool
     */
    public function verifyEmail(string $email):bool {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $req = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE email = :email");
            $req->execute(compact('email'));
            if($req->rowCount() > 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function changeUsername(int $id, string $username) {
        $req = $this->pdo->prepare("UPDATE {$this->table} SET username = :username WHERE id = :id");
        $req->execute(compact('username', 'id'));
    }

    public function changePassword(int $id, string $old_password, string $new_password) {
        $user = $this->find($id);
        $old_password = sha1($old_password);
        $new_password = sha1($new_password);
        if($user[0]['password'] == $old_password) {
            $req = $this->pdo->prepare("UPDATE {$this->table} SET password = :new_password WHERE id = :id");
            $req->execute(compact('id', 'new_password'));
        } else {
            return false;
        }
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public static function generatePassword() {
        $letterUpper = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $letterLower = strtolower($letterUpper);
        $integer = "0123456789";
        $password = $letterUpper. $letterLower. $integer;
        return substr(str_repeat(str_shuffle($password), random_int(self::PASSWORD_CHARACTER_REPEAT_MIN, self::PASSWORD_CHARACTER_REPEAT_MAX)), 0, self::PASSWORD_LENGTH);
    }

    public function lastId() {
        return $this->pdo->lastInsertId();
    }

    public function search(string $find) {
        $req = $this->pdo->query("SELECT user_category.id AS category_id, user_category.field, users.id AS id, users.user_category_id, users.school_id, users.last_name, users.first_name, users.username, school.acronym FROM {$this->table} INNER JOIN user_category ON user_category.id = users.user_category_id INNER JOIN school ON school.id = users.school_id WHERE (username LIKE '%$find%') OR (last_name LIKE '%$find%') OR (first_name LIKE '%$find%') OR (last_name + ' ' + first_name LIKE '%$find%') OR (first_name + ' ' + last_name LIKE '%$find%')");
        return $req->fetchAll();
    }

    /**
     * @param int $id
     * @return array
     */
    public function online(int $id) {
        $session_time = 15;
        $now = date("U");

        $req_user_exist = $this->pdo->prepare("SELECT * FROM online WHERE users_id = :id");
        $req_user_exist->execute(compact("id"));
        $user_exist = $req_user_exist->rowCount();

        if($user_exist == 0) {
            $add_user = $this->pdo->prepare("INSERT INTO online(users_id, time_online) VALUES(:id, :now)");
            $add_user->execute(compact("id","now"));
        } else {
            $update_user = $this->pdo->prepare("UPDATE online SET time_online = :now WHERE users_id = :id");
            $update_user->execute(compact("now", "id"));
        }

        $session_delete_time = $now - $session_time;
        $delete_user = $this->pdo->prepare("DELETE FROM online WHERE time_online < :session_delete_time");
        $delete_user->execute(compact("session_delete_time"));

        $user_online = $this->pdo->query("SELECT * FROM online INNER JOIN users ON users.id = online.users_id");
        return $user_online->fetchAll();
    }

    /**
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFirstName() {
        return $this->first_name;
    }

    /**
     * @return string
     */
    public function getLastName() {
        return $this->last_name;
    }

    /**
     * @return int
     */
    public function getAccreditation() {
        return $this->accreditation;
    }


    /**
     * @return string
     */
    public function getBirthDate() {
        return $this->birth_date;
    }

    /**
     * @return string
     */
    public function getSexe() {
        return $this->sexe;
    }

    /**
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * @param int $id
     */
    public function setId(int $id) {
        $this->id = $id;
    }

    /**
     * @param string $last_name
     */
    public function setLastName(string $last_name) {
        $this->last_name = $last_name;
    }

    /**
     * @param string $first_name
     */
    public function setFirstName(string $first_name):void {
        $this->first_name = $first_name;
    }

    /**
     * @param int $accreditation
     */
    public function setAccreditation(int $accreditation) {
        $this->accreditation = $accreditation;
    }


    /**
     * @param string $birth_day
     */
    public function setBirthDate(string $birth_date) {
        $this->birth_date = $birth_date;
    }

    /**
     * @param string $sexe
     */
    public function setSexe(string $sexe) {
        $this->sexe = $sexe;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username) {
        $this->username = $username;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email) {
        $this->email = $email;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password) {
        $this->password = $password;
    }
}