<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class UserOauth
 *
 * @since 2.0
 *
 * @Entity(table="user_oauth")
 */
class UserOauth extends Model
{
    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * oauth类型
     *
     * @Column()
     *
     * @var int
     */
    private $type;

    /**
     * 用户id
     *
     * @Column(name="user_id", prop="userId")
     *
     * @var int
     */
    private $userId;

    /**
     * 外部uid
     *
     * @Column(name="external_uid", prop="externalUid")
     *
     * @var string
     */
    private $externalUid;

    /**
     * 外部用户名
     *
     * @Column(name="external_name", prop="externalName")
     *
     * @var string
     */
    private $externalName;

    /**
     * 外部token
     *
     * @Column()
     *
     * @var string
     */
    private $token;

    /**
     * 刷新token
     *
     * @Column(name="refresh_token", prop="refreshToken")
     *
     * @var string|null
     */
    private $refreshToken;

    /**
     * 其他信息（主要用于unionid公众号还是app）
     *
     * @Column()
     *
     * @var string|null
     */
    private $other;


    /**
     * @param int $id
     *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param int $type
     *
     * @return void
     */
    public function setType(int $type): void
    {
        $this->type = $type;
    }

    /**
     * @param int $userId
     *
     * @return void
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @param string $externalUid
     *
     * @return void
     */
    public function setExternalUid(string $externalUid): void
    {
        $this->externalUid = $externalUid;
    }

    /**
     * @param string $externalName
     *
     * @return void
     */
    public function setExternalName(string $externalName): void
    {
        $this->externalName = $externalName;
    }

    /**
     * @param string $token
     *
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @param string|null $refreshToken
     *
     * @return void
     */
    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @param string|null $other
     *
     * @return void
     */
    public function setOther(?string $other): void
    {
        $this->other = $other;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getExternalUid(): ?string
    {
        return $this->externalUid;
    }

    /**
     * @return string
     */
    public function getExternalName(): ?string
    {
        return $this->externalName;
    }

    /**
     * @return string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    /**
     * @return string|null
     */
    public function getOther(): ?string
    {
        return $this->other;
    }

}
