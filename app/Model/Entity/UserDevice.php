<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class UserDevice
 *
 * @since 2.0
 *
 * @Entity(table="user_device")
 */
class UserDevice extends Model
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
     * 用户id
     *
     * @Column(name="user_id", prop="userId")
     *
     * @var int
     */
    private $userId;

    /**
     * 设备名称
     *
     * @Column()
     *
     * @var string
     */
    private $device;

    /**
     * token
     *
     * @Column(name="access_token", prop="accessToken")
     *
     * @var string
     */
    private $accessToken;

    /**
     * 第三方个推id
     *
     * @Column(name="push_cid", prop="pushCid")
     *
     * @var string
     */
    private $pushCid;

    /**
     * 是否已退出1已退出
     *
     * @Column()
     *
     * @var int
     */
    private $loggedout;

    /**
     * 最后活动时间
     *
     * @Column(name="last_active", prop="lastActive")
     *
     * @var int|null
     */
    private $lastActive;


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
     * @param int $userId
     *
     * @return void
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @param string $device
     *
     * @return void
     */
    public function setDevice(string $device): void
    {
        $this->device = $device;
    }

    /**
     * @param string $accessToken
     *
     * @return void
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @param string $pushCid
     *
     * @return void
     */
    public function setPushCid(string $pushCid): void
    {
        $this->pushCid = $pushCid;
    }

    /**
     * @param int $loggedout
     *
     * @return void
     */
    public function setLoggedout(int $loggedout): void
    {
        $this->loggedout = $loggedout;
    }

    /**
     * @param int|null $lastActive
     *
     * @return void
     */
    public function setLastActive(?int $lastActive): void
    {
        $this->lastActive = $lastActive;
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
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getDevice(): ?string
    {
        return $this->device;
    }

    /**
     * @return string
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @return string
     */
    public function getPushCid(): ?string
    {
        return $this->pushCid;
    }

    /**
     * @return int
     */
    public function getLoggedout(): ?int
    {
        return $this->loggedout;
    }

    /**
     * @return int|null
     */
    public function getLastActive(): ?int
    {
        return $this->lastActive;
    }

}
