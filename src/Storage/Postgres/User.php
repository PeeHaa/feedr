<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Storage\Postgres;

use PeeHaa\AwesomeFeed\Authentication\User as UserInfo;

class User
{
    private $dbConnection;

    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function store(UserInfo $user): void
    {
        if (!$this->exists($user)) {
            $this->insert($user);

            return;
        }

        $this->update($user);
    }

    public function exists(UserInfo $user): bool
    {
        $query = '
            SELECT COUNT(id)
            FROM users
            WHERE id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id' => $user->getId(),
        ]);

        return (bool) $stmt->fetchColumn(0);
    }

    private function insert(UserInfo $user): void
    {
        $query = '
            INSERT INTO users
            (id, username, avatar)
            VALUES
            (:id, :username, :avatar)
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id'       => $user->getId(),
            'username' => $user->getUsername(),
            'avatar'   => $user->getAvatarUrl(),
        ]);
    }

    private function update(UserInfo $user): void
    {
        $query = '
            UPDATE users
            SET username = :username, avatar = :avatar
            WHERE id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id'       => $user->getId(),
            'username' => $user->getUsername(),
            'avatar'   => $user->getAvatarUrl(),
        ]);
    }
}
