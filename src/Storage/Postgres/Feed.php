<?php declare(strict_types=1);

namespace PeeHaa\AwesomeFeed\Storage\Postgres;

use Cocur\Slugify\Slugify;
use PeeHaa\AwesomeFeed\Authentication\User;
use PeeHaa\AwesomeFeed\Feed\Collection;
use PeeHaa\AwesomeFeed\Feed\Feed as Entity;
use PeeHaa\AwesomeFeed\Form\Feed\Create;

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

    public function getById(int $id): ?Entity
    {
        $query = '
            SELECT feeds.id AS feed_id, feeds.name AS feed_name, feeds.slug,
              creator.id AS user_id, creator.username, creator.avatar
            FROM feeds
            JOIN users AS creator ON creator.id = feeds.created_by
            WHERE feeds.id = :id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'id' => $id,
        ]);

        $record = $stmt->fetch();

        if (!$record) {
            return null;
        }

        return new Entity(
            $record['feed_id'],
            $record['feed_name'],
            $record['slug'],
            new User($record['user_id'], $record['username'], $record['avatar'])
        );
    }

    public function getUserFeeds(User $user): Collection
    {
        $query = '
            SELECT feeds.id AS feed_id, feeds.name AS feed_name, feeds.slug,
              creator.id AS user_id, creator.username, creator.avatar
            FROM feeds
            JOIN users AS creator ON creator.id = feeds.created_by
            JOIN feed_admins ON feed_admins.feed_id = feeds.id
              AND feed_admins.user_id = :user_id
        ';

        $stmt = $this->dbConnection->prepare($query);
        $stmt->execute([
            'user_id' => $user->getId(),
        ]);

        $collection = new Collection();

        foreach ($stmt->fetchAll() as $record) {
            $collection->add(new Entity(
                $record['feed_id'],
                $record['feed_name'],
                $record['slug'],
                new User($record['user_id'], $record['username'], $record['avatar'])
            ));
        }

        return $collection;
    }
}
