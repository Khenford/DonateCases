<?php

namespace Khenford\database;

use function mkdir;
use function is_dir;
use function dirname;
use const SQLITE3_ASSOC;
use const SQLITE3_TEXT;
use const SQLITE3_INTEGER;

class SQLiteDataManager implements SQLiteData {
    private \SQLite3 $database;

    public function __construct(string $file) {
        if (!is_dir(dirname($file))) {
            mkdir(dirname($file));
        }
        $this->database = new \SQLite3($file);

        $this->initDatabase();
    }

    private function initDatabase(): void {
        $query = "CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user TEXT,
                    key TEXT
                )";

        $this->database->exec($query);
    }

    public function transferKey(string $fromUser, string $toUser, int $key): void {
        $query = "UPDATE users SET key = :key WHERE `user` = :fromUser;
                  UPDATE users SET key = :key WHERE `user` = :toUser;";

        $statement = $this->database->prepare($query);
        $statement->bindValue(':key', $key, SQLITE3_INTEGER);
        $statement->bindValue(':fromUser', $fromUser, SQLITE3_TEXT);
        $statement->bindValue(':toUser', $toUser, SQLITE3_TEXT);
        $statement->execute();
    }

    public function addUserData(string $user, int $key): void {
        if (!$this->isUser($user)) {
            $query = "INSERT INTO users(`user`, `key`) VALUES (:user, :key);";
            $statement = $this->database->prepare($query);
            $statement->bindValue(':key', $key, SQLITE3_INTEGER);
            $statement->bindValue(':user', $user, SQLITE3_TEXT);
            $statement->execute();
        }
        return;
    }

    public function addKey(string $user, int $key): void {
        $query = "UPDATE users SET key = :key WHERE `user` = :user;";

        $value = $this->getKeys($user) + $key;
        $statement = $this->database->prepare($query);
        $statement->bindValue(':key', $value, SQLITE3_INTEGER);
        $statement->bindValue(':user', $user, SQLITE3_TEXT);
        $statement->execute();
    }

    public function removeKey(string $user, int $key): void {
        $query = "UPDATE users SET key = :key WHERE `user` = :user;";

        $value = $this->getKeys($user) - $key;
        if ($value <= 0) {
            $value = 0;
        }
        $statement = $this->database->prepare($query);
        $statement->bindValue(':key', $value, SQLITE3_INTEGER);
        $statement->bindValue(':user', $user, SQLITE3_TEXT);
        $statement->execute();
    }

    public function isUser(string $user): bool {
        $query = "SELECT * FROM users WHERE `user` = :user;";

        $statement = $this->database->prepare($query);
        $statement->bindValue(':user', $user, SQLITE3_TEXT);
        $result = $statement->execute();
        if (!($result->fetchArray(SQLITE3_ASSOC))) {
            return false;
        }

        return true;
    }

    public function getKeys(string $user): int {
        $query = "SELECT key FROM users WHERE `user` = :user;";

        $statement = $this->database->prepare($query);
        $statement->bindValue(':user', $user, SQLITE3_TEXT);
        $result = $statement->execute();
        $data = $result->fetchArray(SQLITE3_ASSOC);
        
        return (int) $data['key'];
    }
}
