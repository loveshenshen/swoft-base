<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class User
 *
 * @since 2.0
 *
 * @Entity(table="user")
 */
class User extends Model
{

    const REDIS_USER_FD = "redis_hash_user_fd";

    const REDIS_USER_MOBILE_CODE = "redis_hash_user_mobile_code";

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    const USER_TYPE_NORMAL = 1;
    const USER_TYPE_MERCHANT = 2;
    public static $USER_TYPE = [
        self::USER_TYPE_NORMAL=>'普通用户',
        self::USER_TYPE_MERCHANT=>'商家用户',
    ];

    const  LEVEL_GOLD = 1;
    const  LEVEL_PLATINUM = 2;
    const  LEVEL_DIAMOND = 3;
    public static $LEVEL = [
        self::LEVEL_GOLD=> '黄金会员',
        self::LEVEL_PLATINUM=> '铂金会员',
        self::LEVEL_DIAMOND=> '钻石会员',
    ];

    const IS_ONLINE_ON = 1;
    const IS_ONLINE_OFF = 0;




    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 登录名(手机号)
     *
     * @Column()
     *
     * @var string
     */
    private $username;

    /**
     * 登录密码
     *
     * @Column(name="password_hash", prop="passwordHash",hidden=true)
     *
     * @var string
     */
    private $passwordHash;

    /**
     * 用户昵称
     *
     * @Column()
     *
     * @var string
     */
    private $nickname;

    /**
     * 重置密码
     *
     * @Column(name="password_reset_token", prop="passwordResetToken",hidden=true)
     *
     * @var string|null
     */
    private $passwordResetToken;

    /**
     * cookie
     *
     * @Column(name="auth_key", prop="authKey",hidden=true)
     *
     * @var string
     */
    private $authKey;

    /**
     * 激活状态
     *
     * @Column()
     *
     * @var int
     */
    private $status;

    /**
     * 用户身份;1-用户;2-商家
     *
     * @Column()
     *
     * @var int|null
     */
    private $type;

    /**
     * 用户头像
     *
     * @Column()
     *
     * @var string|null
     */
    private $avatar;

    /**
     * 性别
     *
     * @Column()
     *
     * @var int
     */
    private $gender;

    /**
     * 封禁截止日期
     *
     * @Column(name="block_until", prop="blockUntil",hidden=true)
     *
     * @var int|null
     */
    private $blockUntil;

    /**
     * 推荐人id
     *
     * @Column(name="referee_id", prop="refereeId")
     *
     * @var int|null
     */
    private $refereeId;

    /**
     * 生日
     *
     * @Column()
     *
     * @var int|null
     */
    private $birthday;

    /**
     * 钱包余额
     *
     * @Column()
     *
     * @var float|null
     */
    private $balance;

    /**
     * 积分
     *
     * @Column()
     *
     * @var float|null
     */
    private $integral;

    /**
     * 支付密码
     *
     * @Column(name="pay_password", prop="payPassword",hidden=true)
     *
     * @var string|null
     */
    private $payPassword;

    /**
     * 商家二维码
     *
     * @Column()
     *
     * @var string|null
     */
    private $qrcode;

    /**
     * 等级;1-黄金会员(gold);2-铂金会员(platinum);3-钻石会员(diamond)
     *
     * @Column()
     *
     * @var int|null
     */
    private $level;

    /**
     * 折扣
     *
     * @Column()
     *
     * @var float|null
     */
    private $discount;



    /**
     * 在线状态
     *
     * @Column(name="is_online", prop="isOnline")
     *
     * @var int|null
     *
     */
    private $is_online;

    /**
     * 创建时间
     *
     * @Column(name="created_at", prop="createdAt")
     *
     * @var int|null
     */
    private $createdAt;

    /**
     * 最后修改时间
     *
     * @Column(name="updated_at", prop="updatedAt",hidden=true)
     *
     * @var int|null
     */
    private $updatedAt;

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
     * @param string $username
     *
     * @return void
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @param string $passwordHash
     *
     * @return void
     */
    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    /**
     * @param string $nickname
     *
     * @return void
     */
    public function setNickname(string $nickname): void
    {
        $this->nickname = $nickname;
    }

    /**
     * @param string|null $passwordResetToken
     *
     * @return void
     */
    public function setPasswordResetToken(?string $passwordResetToken): void
    {
        $this->passwordResetToken = $passwordResetToken;
    }

    /**
     * @param string $authKey
     *
     * @return void
     */
    public function setAuthKey(string $authKey): void
    {
        $this->authKey = $authKey;
    }

    /**
     * @param int $status
     *
     * @return void
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @param int|null $type
     *
     * @return void
     */
    public function setType(?int $type): void
    {
        $this->type = $type;
    }

    /**
     * @param string|null $avatar
     *
     * @return void
     */
    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    /**
     * @param int $gender
     *
     * @return void
     */
    public function setGender(int $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @param int|null $blockUntil
     *
     * @return void
     */
    public function setBlockUntil(?int $blockUntil): void
    {
        $this->blockUntil = $blockUntil;
    }

    /**
     * @param int|null $refereeId
     *
     * @return void
     */
    public function setRefereeId(?int $refereeId): void
    {
        $this->refereeId = $refereeId;
    }

    /**
     * @param int|null $birthday
     *
     * @return void
     */
    public function setBirthday(?int $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @param float|null $balance
     *
     * @return void
     */
    public function setBalance(?float $balance): void
    {
        $this->balance = $balance;
    }

    /**
     * @param float|null $integral
     *
     * @return void
     */
    public function setIntegral(?float $integral): void
    {
        $this->integral = $integral;
    }

    /**
     * @param string|null $payPassword
     *
     * @return void
     */
    public function setPayPassword(?string $payPassword): void
    {
        $this->payPassword = $payPassword;
    }

    /**
     * @param string|null $qrcode
     *
     * @return void
     */
    public function setQrcode(?string $qrcode): void
    {
        $this->qrcode = $qrcode;
    }

    /**
     * @param int|null $level
     *
     * @return void
     */
    public function setLevel(?int $level): void
    {
        $this->level = $level;
    }

    /**
     * @param float|null $discount
     *
     * @return void
     */
    public function setDiscount(?float $discount): void
    {
        $this->discount = $discount;
    }



    /**
     * @param int|null $is_online
     */
    public function setIsOnline(?int $is_online): void
    {
        $this->is_online = $is_online;
    }



    /**
     * @param int|null $createdAt
     *
     * @return void
     */
    public function setCreatedAt(?int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param int|null $updatedAt
     *
     * @return void
     */
    public function setUpdatedAt(?int $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    /**
     * @return string
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @return string|null
     */
    public function getPasswordResetToken(): ?string
    {
        return $this->passwordResetToken;
    }

    /**
     * @return string
     */
    public function getAuthKey(): ?string
    {
        return $this->authKey;
    }

    /**
     * @return int
     */
    public function getStatus(): ?int
    {
        return $this->status;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @return int
     */
    public function getGender(): ?int
    {
        return $this->gender;
    }

    /**
     * @return int|null
     */
    public function getBlockUntil(): ?int
    {
        return $this->blockUntil;
    }

    /**
     * @return int|null
     */
    public function getRefereeId(): ?int
    {
        return $this->refereeId;
    }

    /**
     * @return int|null
     */
    public function getBirthday(): ?int
    {
        return $this->birthday;
    }

    /**
     * @return float|null
     */
    public function getBalance(): ?float
    {
        return $this->balance;
    }

    /**
     * @return float|null
     */
    public function getIntegral(): ?float
    {
        return $this->integral;
    }

    /**
     * @return string|null
     */
    public function getPayPassword(): ?string
    {
        return $this->payPassword;
    }

    /**
     * @return string|null
     */
    public function getQrcode(): ?string
    {
        return $this->qrcode;
    }

    /**
     * @return int|null
     */
    public function getLevel(): ?int
    {
        return $this->level;
    }

    /**
     * @return float|null
     */
    public function getDiscount(): ?float
    {
        return $this->discount;
    }


    /**
     * @return int|null
     */
    public function getIsOnline(): ?int
    {
        return $this->is_online;
    }

    /**
     * @return int|null
     */
    public function getCreatedAt(): ?int
    {
        return $this->createdAt;
    }

    /**
     * @return int|null
     */
    public function getUpdatedAt(): ?int
    {
        return $this->updatedAt;
    }

}
