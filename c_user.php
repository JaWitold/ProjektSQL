<?php
	global $roles;
	$roles = ["Administrator", "Ksiegowy", "Pracownik"];

	class User {
        public string $name;
        public string $surname;
        private string $login;
        private string $pass;
        public string $role;
        public string $date;
        public bool $active;

        function __construct(string $login = "") {
            if (strlen($login) !== 0) {
                $this->getData($login);
            }
        }

        /**
         * @param string $login
         * gets data form database
         */
        private function getData(string $login = "") {
            require_once "connect.php";
            global $db;

            $query = $db->prepare("SELECT * FROM users WHERE login = :login");
            $query->bindValue(':login', $login, PDO::PARAM_STR);
            $query->execute();

            $result = $query->fetch(PDO::FETCH_ASSOC);

            $this->name = $result["userName"];
            $this->surname = $result["userSurname"];
            $this->login = $result["login"];
            $this->pass = $result["password"];
            $this->role = $result["role"];
            $this->date = $result["dateOfAdd"];
            $this->active = $result["activePassword"];
        }

        /**
         * @return string
         */
        function printUserData() : string {
            return '<table class="table table-dark table-striped">
                <tr><th>Imie</th><td>' . $this->name . "</td></tr>
                <tr><th>Nazwisko</th><td>" . $this->surname . "</td></tr>
                <tr><th>Login</th><td>" . $this->login . "</td></tr>
                <tr><th>Role</th><td>" . $this->role . "</td></tr>
                <tr><th>Data dodanie</th><td>" . $this->date. "</td></tr>
                <tr><th>Aktywne</th><td>" . $this->active . "</td></tr>
			</table>";
        }

        /**
         *  create new unique login
         */
        function generateLogin() {
            $this->login = "langner_" . substr(uniqid(), 7, 13);
        }

        /**
         *  generate new random password
         */
        function generatePass() {
            try {
                $this->pass = random_int(10000000, 99999999);
            } catch (Exception $e) {
                echo "Nie udało się wygenerować hasła.";
                exit();
            }
        }

        /**
         * @return string
         */
        function getLogin(): string
        {
            return $this->login;
        }

        /**
         * @return string
         */
        function getPassword(): string
        {
            return $this->pass;
        }

        /**
         * @param string $newPass
         */
        function setPassword(string $newPass): void
        {
            $this->pass = $newPass;
        }

        /**
         * @param string $login
         */
        public function setLogin(string $login): void
        {
            $this->login = $login;
        }

        /**
         * @return string
         */
        public function getName(): string {
            return $this->name;
        }

        /**
         * @return string
         */
        public function getSurname(): string {
            return $this->surname;
        }

        /**
         * @return string
         */
        public function getRole(): string {
            return $this->role;
        }

        /**
         * @return string
         */
        public function getDate() : string{
            return $this->date;
        }

        /**
         * @return bool
         */
        public function isActive() : bool {
            return $this->active;
        }

        /**
         *  class destructor
         */
        function __destruct() {
            unset($this->name);
            unset($this->surname);
            unset($this->login);
            unset($this->pass);
            unset($this->role);
            unset($this->date);
        }

        /**
         * @param string $name
         */
        public function setName(string $name): void
        {
            $this->name = $name;
        }

        /**
         * @param string $surname
         */
        public function setSurname(string $surname): void
        {
            $this->surname = $surname;
        }

        /**
         * @param string $role
         */
        public function setRole(string $role): void
        {
            $this->role = $role;
        }

        /**
         * @param string $date
         */
        public function setDate(string $date): void
        {
            $this->date = $date;
        }

        /**
         *  deactivate user
         */
        public function deactivate()
        {
            $this->active = 0;
        }
    }
