<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Storage\Postgres;

use PeeHaa\AwesomeFeed\Authentication\Collection;
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

    public function storeCollection(Collection $users): void
    {
        foreach ($users as $user) {
            $this->store($user);
        }
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
              (id, username, url, avatar)
            VALUES
              (:id, :username, :url, :avatar)
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id'       => $user->getId(),
            'username' => $user->getUsername(),
            'url'      => $user->getUrl(),
            'avatar'   => $user->getAvatarUrl(),
        ]);
    }

    private function update(UserInfo $user): void
    {
        $query = '
            UPDATE users
            SET username = :username,
              url = :url,
              avatar = :avatar
            WHERE id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id'       => $user->getId(),
            'username' => $user->getUsername(),
            'url'      => $user->getUrl(),
            'avatar'   => $user->getAvatarUrl(),
        ]);
    }

    public function getById(int $id): ?UserInfo
    {
        $query = '
            SELECT id, username, url, avatar
            FROM users
            WHERE id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id' => $id,
        ]);

        $record = $stmt->fetch();

        return new UserInfo($record['id'], $record['username'], $record['url'], $record['avatar']);
    }
}
