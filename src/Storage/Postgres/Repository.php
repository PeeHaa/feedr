<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Storage\Postgres;

use PeeHaa\AwesomeFeed\GitHub\Collection;
use PeeHaa\AwesomeFeed\GitHub\Repository as RepositoryInfo;

class Repository
{
    private $dbConnection;

    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function store(RepositoryInfo $repository): void
    {
        if (!$this->exists($repository)) {
            $this->insert($repository);

            return;
        }

        $this->update($repository);
    }

    public function storeCollection(Collection $repositories): void
    {
        foreach ($repositories as $repository) {
            $this->store($repository);
        }
    }

    public function exists(RepositoryInfo $repository): bool
    {
        $query = '
            SELECT COUNT(id)
            FROM repositories
            WHERE id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id' => $repository->getId(),
        ]);

        return (bool) $stmt->fetchColumn(0);
    }

    private function insert(RepositoryInfo $repository): void
    {
        $query = '
            INSERT INTO repositories
              (id, owner_id, name, full_name, url)
            VALUES
              (:id, :owner_id, :name, :full_name, :url)
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id'        => $repository->getId(),
            'owner_id'  => $repository->getOwner()->getId(),
            'name'      => $repository->getName(),
            'full_name' => $repository->getFullName(),
            'url'       => $repository->getUrl(),
        ]);
    }

    private function update(RepositoryInfo $repository): void
    {
        $query = '
            UPDATE repositories
            SET owner_id = :owner_id,
              name = :name,
              full_name = :full_name,
              url = :url
            WHERE id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id'        => $repository->getId(),
            'owner_id'  => $repository->getOwner()->getId(),
            'name'      => $repository->getName(),
            'full_name' => $repository->getFullName(),
            'url'       => $repository->getUrl(),
        ]);
    }
}
