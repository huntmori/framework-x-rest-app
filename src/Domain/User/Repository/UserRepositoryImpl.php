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

        $userData = $result->resultRows[0] ?? null;
        if ($userData === null) {
            return null;
        }

        return $this->mapper->dbRowToUser($userData);
            
    }

    public function save(User $user): ?int
    {
        // 기존 사용자 확인 (uid로 조회)
        $existingUser = $this->findOneByUid($user->uid);

        if ($existingUser === null) {
            // INSERT 쿼리 (새로운 사용자)
            $sql = <<<SQL
                INSERT INTO user (
                    uid, 
                    id, 
                    email, 
                    password, 
                    created_at, 
                    updated_at
                ) VALUES (
                    UPPER(UUID()), 
                    ?, 
                    ?, 
                    ?, 
                    NOW(), 
                    NOW()
                )
            SQL;

            $params = [
                $user->id,
                $user->email,
                $user->password
            ];
        } else {
            // UPDATE 쿼리 (기존 사용자 업데이트)
            $sql = <<<SQL
                UPDATE user 
                SET 
                    id = ?, 
                    email = ?, 
                    password = ?,
                    updated_at = NOW()
                WHERE uid = ?
            SQL;

            $params = [
                $user->id,
                $user->email,
                $user->password,
                $user->uid
            ];
        }

        /** @var \React\Mysql\MysqlResult */
        $result = await($this->db->getClient()
            ->query($sql, $params)
        );

        // 마지막으로 삽입된 ID 또는 영향받은 행 수 반환
        return $existingUser === null ? $result->insertId : $result->affectedRows;
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

    public function findOneByUid(string $uid): ?User
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
            WHERE   uid = ?
        SQL;

        /** @var \React\Mysql\MysqlResult */
        $result = await($this->db->getClient()
            ->query($sql, [$uid])
        );

        if (empty($result->resultRows)) {
            return null;
        }

        return User::fromMysqlResultRow($result->resultRows[0]);
    }
}
