<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Storage\Postgres;

use PeeHaa\AwesomeFeed\GitHub\Release\Collection;
use PeeHaa\AwesomeFeed\GitHub\Release\Release as ReleaseInfo;

class Release
{
    private $dbConnection;

    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function store(ReleaseInfo $release): void
    {
        if (!$this->exists($release)) {
            $this->insert($release);

            return;
        }

        $this->update($release);
    }

    public function storeCollection(Collection $releases): void
    {
        foreach ($releases as $release) {
            $this->store($release);
        }
    }

    public function exists(ReleaseInfo $release): bool
    {
        $query = '
            SELECT COUNT(id)
            FROM repository_releases
            WHERE id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id' => $release->getId(),
        ]);

        return (bool) $stmt->fetchColumn(0);
    }

    private function insert(ReleaseInfo $release): void
    {
        $query = '
            INSERT INTO repository_releases
              (id, repository_id, name, body, url, published_at)
            VALUES
              (:id, :repository_id, :name, :body, :url, :published_at)
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id'            => $release->getId(),
            'repository_id' => $release->getRepository()->getId(),
            'name'          => $release->getName(),
            'body'          => $release->getBody(),
            'url'           => $release->getUrl(),
            'published_at'  => $release->getPublishedDate()->format('Y-m-d H:i:s'),
        ]);
    }

    private function update(ReleaseInfo $release): void
    {
        $query = '
            UPDATE repository_releases
            SET repository_id = :repository_id,
              name = :name,
              body = :body,
              url = :url,
              published_at = :published_at
            WHERE id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id'            => $release->getId(),
            'repository_id' => $release->getRepository()->getId(),
            'name'          => $release->getName(),
            'body'          => $release->getBody(),
            'url'           => $release->getUrl(),
            'published_at'  => $release->getPublishedDate()->format('Y-m-d H:i:s'),
        ]);
    }
}
