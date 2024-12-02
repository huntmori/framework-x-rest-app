<?php
declare(strict_types=1);

namespace Damoyo\Api\Domain\User\Repository;

use Damoyo\Api\Common\Database\DatabaseService;
use Damoyo\Api\Domain\User\Mapper\UserMapper;
use DateTime;
use Damoyo\Api\Domain\User\Entity\User;
use function React\Async\await;

class UserRepositoryImpl implements UserRepository 
{
    private ?DatabaseService $db = null;
    private UserMapper $mapper;

    public function __construct(
        DatabaseService $db,
        UserMapper $mapper
    ) {
        $this->db = $db;
        $this->mapper = $mapper;
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
        if ($userData === null) {
            return null;
        }

        return $this->mapper->dbRowToUser($userData);
            
    }

    public function save(User $user): int
    {
        $sql = <<<SQL
           INSERT INTO user
           SET  id = ?,
                uid = UPPER(UUID()),
                email = ?,
                password = ?,
                created_at = NOW(),
                updated_at = NOW() 
        SQL;

        $client = $this->db->getClient();
        $result = await($client->query(
            $sql, 
            [
                $user->id, 
                $user->email, 
                $user->password
            ]
        ));

        return $result->insertId ?? -1 ;
    }
    /**
     * @inheritDoc
     */
    public function findByUid(string $uid): ?User 
    {
        $db = $this->db->getClient();
        $sql = <<<SQL
            SELECT  seq,
                    uid,
                    id,
                    email,
                    password,
                    created_at,
                    updated_at
            FROM    user
            WHERE   uid = ?
            LIMIT   1
        SQL;

        $result = await($db->query($sql, [$uid]));
        
        if (empty($result)) {
            return null;
        }

        $userData = $result->resultRows[0];
        return $this->mapper->dbRowToUser($userData);
    }
    /**
     * @inheritDoc
     */
    public function findOneByEmail(string $email): ?User 
    {
        $db = $this->db->getClient();
        $sql = <<<SQL
            SELECT  seq,
                    uid,
                    id,
                    email,
                    password,
                    created_at,
                    updated_at
            FROM    user
            WHERE   email = ?
            LIMIT   1
        SQL;

        $result = await($db->query($sql, [$email]));
        
        if (empty($result)) {
            return null;
        }

        $userData = $result->resultRows[0];

        if($userData === null) {
            return null;
        }

        return $this->mapper->dbRowToUser($userData);
    }
}
