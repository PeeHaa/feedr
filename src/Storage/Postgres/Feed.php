<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Storage\Postgres;

use Cocur\Slugify\Slugify;
use PeeHaa\AwesomeFeed\Authentication\Collection as UserCollection;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Feed\Collection;
use PeeHaa\AwesomeFeed\Feed\Feed as Entity;
use PeeHaa\AwesomeFeed\Form\Feed\Create;
use PeeHaa\AwesomeFeed\GitHub\Collection as RepositoryCollection;
use PeeHaa\AwesomeFeed\GitHub\Release\Collection as ReleaseCollection;
use PeeHaa\AwesomeFeed\GitHub\Release\Release;
use PeeHaa\AwesomeFeed\GitHub\Repository;

class Feed
{
    private $dbConnection;

    public function __construct(\PDO $dbConnection)
    {
        $this->dbConnection = $dbConnection;
    }

    public function create(Create $form, User $user): Entity
    {
        $query = '
            INSERT INTO feeds
              (name, created_by, slug)
            VALUES 
              (:name, :created_by, :slug)
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'name'       => $form['name']->getValue(),
            'created_by' => $user->getId(),
            'slug'       => (new Slugify())->slugify($form['name']->getValue()),
        ]);

        $id = (int) $this->dbConnection->lastInsertId('feeds_id_seq');

        $this->addAdmin($id, $user);

        return $this->getById($id);
    }

    public function addAdmins(int $feedId, UserCollection $users): void
    {
        foreach ($users as $user) {
            $this->addAdmin($feedId, $user);
        }
    }

    public function addAdmin(int $feedId, User $user): void
    {
        $query = '
            INSERT INTO feed_admins
              (feed_id, user_id)
            VALUES 
              (:feed_id, :user_id)
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'feed_id' => $feedId,
            'user_id' => $user->getId(),
        ]);
    }

    public function deleteAdmin(Entity $feed, User $user): void
    {
        $query = '
            DELETE FROM feed_admins
            WHERE feed_id = :feed_id
              AND user_id = :user_id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'feed_id' => $feed->getId(),
            'user_id' => $user->getId(),
        ]);
    }

    public function addRepositories(int $feedId, RepositoryCollection $repositories): void
    {
        foreach ($repositories as $repository) {
            $this->addRepository($feedId, $repository);
        }
    }

    public function addRepository(int $feedId, Repository $repository): void
    {
        $query = '
            INSERT INTO feeds_repositories
              (feed_id, repository_id)
            VALUES 
              (:feed_id, :repository_id)
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'feed_id'       => $feedId,
            'repository_id' => $repository->getId(),
        ]);
    }

    public function getById(int $id): ?Entity
    {
        $query = '
            SELECT feeds.id AS feed_id, feeds.name AS feed_name, feeds.slug,
              creator.id AS user_id, creator.username, creator.url, creator.avatar,
              administrators.id AS administrator_id, administrators.username AS administrator_username,
              administrators.url AS administrator_url, administrators.avatar AS administrator_avatar,
              repositories.id AS repository_id, repositories.name AS repository_name,
              repositories.full_name AS repository_full_name, repositories.url AS repository_url,
              owner.id AS owner_id, owner.username AS owner_username, owner.url AS owner_url,
              owner.avatar AS owner_avatar
            FROM feeds
              JOIN users AS creator ON creator.id = feeds.created_by
              JOIN feed_admins ON feed_admins.feed_id = feeds.id
              JOIN users AS administrators ON administrators.id = feed_admins.user_id
              LEFT JOIN feeds_repositories ON feeds_repositories.feed_id = feeds.id
              LEFT JOIN repositories ON repositories.id = feeds_repositories.repository_id
              LEFT JOIN users AS owner ON owner.id = repositories.owner_id
            WHERE feeds.id = :id
            ORDER BY feeds.name ASC,
              administrators.username ASC,
              repositories.full_name ASC
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id' => $id,
        ]);

        $recordset      = $stmt->fetchAll();
        $administrators = $this->getAdministratorsByFeedFeedsRecordset($recordset);
        $repositories   = $this->getRepositoriesByFeedFeedsRecordset($recordset);

        if (!$recordset) {
            return null;
        }

        return new Entity(
            $recordset[0]['feed_id'],
            $recordset[0]['feed_name'],
            $recordset[0]['slug'],
            new User($recordset[0]['user_id'], $recordset[0]['username'], $recordset[0]['url'], $recordset[0]['avatar']),
            $administrators[$recordset[0]['feed_id']],
            $repositories[$recordset[0]['feed_id']],
            $this->getReleasesForRepositories($repositories[$recordset[0]['feed_id']])
        );
    }

    public function getUserFeeds(User $user): Collection
    {
        $query = '
            SELECT feeds.id AS feed_id, feeds.name AS feed_name, feeds.slug,
              creator.id AS creator_id, creator.username AS creator_username, creator.url AS creator_url,
              creator.avatar AS creator_avatar,
              administrators.id AS administrator_id, administrators.username AS administrator_username,
              administrators.url AS administrator_url, administrators.avatar AS administrator_avatar,
              repositories.id AS repository_id, repositories.name AS repository_name,
              repositories.full_name AS repository_full_name, repositories.url AS repository_url,
              owner.id AS owner_id, owner.username AS owner_username, owner.url AS owner_url,
              owner.avatar AS owner_avatar
            FROM feeds
              JOIN users AS creator ON creator.id = feeds.created_by
              JOIN feed_admins ON feed_admins.feed_id = feeds.id
                AND feed_admins.user_id = :user_id
              JOIN users AS administrators ON administrators.id = feed_admins.user_id
              LEFT JOIN feeds_repositories ON feeds_repositories.feed_id = feeds.id
              LEFT JOIN repositories ON repositories.id = feeds_repositories.repository_id
              LEFT JOIN users AS owner ON owner.id = repositories.owner_id
            ORDER BY feeds.name ASC,
              administrators.username ASC
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'user_id' => $user->getId(),
        ]);

        $collection     = new Collection();
        $recordset      = $stmt->fetchAll();
        $administrators = $this->getAdministratorsByFeedFeedsRecordset($recordset);
        $repositories   = $this->getRepositoriesByFeedFeedsRecordset($recordset);

        foreach ($recordset as $record) {
            $collection->add(new Entity(
                $record['feed_id'],
                $record['feed_name'],
                $record['slug'],
                new User(
                    $record['creator_id'],
                    $record['creator_username'],
                    $record['creator_url'],
                    $record['creator_avatar']
                ),
                $administrators[$record['feed_id']],
                $repositories[$record['feed_id']],
                $this->getReleasesForRepositories($repositories[$record['feed_id']])
            ));
        }

        return $collection;
    }

    /**
     * @return UserCollection[]
     */
    private function getAdministratorsByFeedFeedsRecordset(array $feedRecordset): array
    {
        $currentFeedId  = null;
        $administrators = [];

        foreach ($feedRecordset as $feedRecord) {
            if ($feedRecord['feed_id'] !== $currentFeedId) {
                $administrators[$feedRecord['feed_id']] = new UserCollection();
            }

            $administrators[$feedRecord['feed_id']]->add(new User(
                $feedRecord['administrator_id'],
                $feedRecord['administrator_username'],
                $feedRecord['administrator_url'],
                $feedRecord['administrator_avatar']
            ));

            $currentFeedId = $feedRecord['feed_id'];
        }

        return $administrators;
    }

    /**
     * @return RepositoryCollection[]
     */
    private function getRepositoriesByFeedFeedsRecordset(array $feedRecordset): array
    {
        $currentFeedId = null;
        $repositories  = [];

        foreach ($feedRecordset as $feedRecord) {
            if ($feedRecord['feed_id'] !== $currentFeedId) {
                $repositories[$feedRecord['feed_id']] = new RepositoryCollection();
            }

            if ($feedRecord['repository_id'] === null) {
                continue;
            }

            $repositories[$feedRecord['feed_id']]->add(new Repository(
                $feedRecord['repository_id'],
                $feedRecord['repository_name'],
                $feedRecord['repository_full_name'],
                $feedRecord['repository_url'],
                new User(
                    $feedRecord['owner_id'],
                    $feedRecord['owner_username'],
                    $feedRecord['owner_url'],
                    $feedRecord['owner_avatar']
                )
            ));

            $currentFeedId = $feedRecord['feed_id'];
        }

        return $repositories;
    }

    private function getReleasesForRepositories(RepositoryCollection $repositories): ReleaseCollection
    {
        $repositoryIds = [];

        foreach ($repositories as $repository) {
            $repositoryIds[] = $repository->getId();
        }

        $repositoryIds = array_unique($repositoryIds);

        $releases = new ReleaseCollection();

        if (!count($repositoryIds)) {
            return $releases;
        }

        $preparedInQuery = implode(',', array_fill(0, count($repositoryIds), '?'));

        $query = '
            SELECT repository_releases.id AS release_id, repository_releases.repository_id AS repository_id,
              repository_releases.name AS release_name, repository_releases.body AS release_body,
              repository_releases.url AS release_url, repository_releases.published_at,
              repositories.name AS repository_name, repositories.full_name AS repository_full_name,
              repositories.url AS repository_url, users.id AS owner_id, users.username AS owner_username,
              users.url AS owner_url, users.avatar AS owner_avatar
            FROM repository_releases
            JOIN repositories ON repositories.id = repository_releases.repository_id
            JOIN users ON users.id = repositories.owner_id
            WHERE repository_releases.repository_id IN (' . $preparedInQuery . ')
            ORDER BY repository_releases.published_at DESC
            LIMIT 100
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute($repositoryIds);

        foreach ($stmt->fetchAll() as $release) {
            $releases->add(new Release(
                $release['release_id'],
                $release['release_name'],
                $release['release_body'],
                $release['release_url'],
                new Repository(
                    $release['repository_id'],
                    $release['repository_name'],
                    $release['repository_full_name'],
                    $release['repository_url'],
                    new User(
                        $release['owner_id'],
                        $release['owner_username'],
                        $release['owner_url'],
                        $release['owner_avatar']
                    )
                ),
                new \DateTimeImmutable($release['published_at'])
            ));
        }

        return $releases;
    }

    public function deleteFeed(Entity $feed): void
    {
        $query = '
            DELETE FROM feeds
            WHERE id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id' => $feed->getId(),
        ]);
    }

    public function deleteRepository(Entity $feed, Repository $repository): void
    {
        $query = '
            DELETE FROM feeds_repositories
            WHERE feed_id = :feed_id
              AND repository_id = :repository_id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'feed_id'       => $feed->getId(),
            'repository_id' => $repository->getId(),
        ]);

        $query = '
            SELECT COUNT(id)
            FROM feeds_repositories
            WHERE feeds_repositories.repository_id = :repository_id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'repository_id' => $repository->getId(),
        ]);

        if ($stmt->fetchColumn(0) === 0) {
            $query = '
                DELETE FROM repositories
                WHERE id = :id
            ';

            $stmt = $this->dbConnection->prepare($query);
            $stmt->execute([
                'id' => $repository->getId(),
            ]);
        }
    }
}
