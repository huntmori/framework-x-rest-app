<?php
declare(strict_types=1);

namespace Damoyo\Api\Domain\User\Repository;

use Damoyo\Api\Common\Database\DatabaseService;
use DateTime;
use Damoyo\Api\Domain\User\Entity\User;
use function React\Async\await;

/**
 * UserRepositoryImpl
 *
 * @author [Your Name] <[Your Email]>
 */
class UserRepositoryImpl implements UserRepository 
{
    private ?DatabaseService $db = null;

    public function __construct(DatabaseService $db)
    {
        $this->db = $db;
    }

    public function find(): array
    {
        $sql = <<<SQL
                SELECT  seq,
                        uid,
                        id,
                        email,
                        password,
                        created_at,
                        updated_at
                FROM    user
            SQL;

        /** @var \React\Mysql\MysqlResult */
        $result = await($this->db->getClient()
            ->query($sql, [])
        );

        if (empty($result)) {
            return [];
        }

        $data = [];
        for($i=0; $i<count($result->resultRows); $i++) {
            $data[] = User::fromMysqlResultRow($result->resultRows[$i]);
        }

        return $data;
    }

    public function findOneById(string $id): ?User
    {
        /** @var \React\Mysql\MysqlResult */
        $result = await($this->db->getClient()
            ->query(<<<SQL
                SELECT  seq,
                        uid,
                        id,
                        email,
                        password,
                        created_at,
                        updated_at
                FROM    user
                WHERE   id = ?
                LIMIT   1
            SQL, [$id])
        );

        if (empty($result)) {
            return null;
        }

        $userData = $result->resultRows[0];
        return User::init()
            ->setSeq($userData['seq'])
            ->setId($userData['id'])
            ->setUid($userData['uid'])
            ->setEmail($userData['email'])
            ->setPassword($userData['password'])
            ->setCreatedAt(new DateTime($userData['created_at']))
            ->setUpdatedAt(new DateTime($userData['updated_at']))
            ->build();
            
    }

    public function save(User $user): ?User
    {
        return null;
    }
}
