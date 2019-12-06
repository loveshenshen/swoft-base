<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 明细
 * Class ConsumeDetail
 *
 * @since 2.0
 *
 * @Entity(table="consume_detail")
 */
class ConsumeDetail extends Model
{
    const  TYPE_CHARGE = 1;
    const  TYPE_CONSUME = 2;
    const  TYPE_QRCODE = 3;
    const  TYPE_END_MONTH = 4;
    const  TYPE_END_BACK_OUT = 5;
    public static $TYPE = [
        self::TYPE_CHARGE=> '充值',
        self::TYPE_CONSUME=> '消费',
        self::TYPE_QRCODE=> '二维码收款',
        self::TYPE_END_MONTH=> '月末结余',
        self::TYPE_END_BACK_OUT=> '撤销',
    ];

    const  CHILD_TYPE_ONLINE_PAY = 1;
    const  CHILD_TYPE_BACKEND = 2;
    public static $CHILD_TYPE = [
        self::CHILD_TYPE_ONLINE_PAY=> '微信充值|钱包支付',
        self::CHILD_TYPE_BACKEND=> '后台充值|线下支付',
    ];

    const REDIS_LIST_PAY = "redis_list_pay_consume_detail";


    const REDIS_LIST_PAY_SCAN = "redis_list_pay_scan";


    const REDIS_LIST_HISTORY_MESSAGE = "redis_list_history_message";
    const REDIS_LIST_HISTORY_NUM = 300;

    const REDIS_USER_SADD_KEY = 'redis_user_sAdd_key';

    const REDIS_TYPE_SCAN = 'scan';
    const REDIS_TYPE_PAY = 'pay';


    /**
     * @param string $type
     * @param $userId
     * @return string
     */
    public static function getRedisKey($userId,$type = self::REDIS_TYPE_PAY){
        return self::REDIS_LIST_HISTORY_MESSAGE.'_'.$type.'_'.$userId;
    }

    /**
     * 
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 用户
     *
     * @Column(name="user_id", prop="userId")
     *
     * @var int
     */
    private $userId;

    /**
     * 是否积分明细
     *
     * @Column(name="is_integral", prop="isIntegral")
     *
     * @var int|null
     */
    private $isIntegral;

    /**
     * 类型;1-充值(charge);2-消费(consume)
     *
     * @Column()
     *
     * @var int|null
     */
    private $type;

    /**
     * 充值类型|消费类型;1-微信充值|钱包支付(online_pay);2-后台充值|线下支付(backend)
     *
     * @Column(name="child_type", prop="childType")
     *
     * @var int|null
     */
    private $childType;

    /**
     * 备注
     *
     * @Column()
     *
     * @var string
     */
    private $remark;
    /**
     * 内容
     *
     * @Column()
     *
     * @var string
     */
    private $content;

    /**
     * 原价积分或钱
     *
     * @Column(name="origin_price", prop="originPrice")
     *
     * @var float|null
     */
    private $originPrice;

    /**
     * 积分或钱
     *
     * @Column()
     *
     * @var float|null
     */
    private $price;

    /**
     * 是否推送
     *
     * @Column(name="is_push", prop="isPush")
     *
     * @var int|null
     */
    private $isPush;

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
     * @Column(name="updated_at", prop="updatedAt")
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
     * @param int $userId
     *
     * @return void
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @param int|null $isIntegral
     *
     * @return void
     */
    public function setIsIntegral(?int $isIntegral): void
    {
        $this->isIntegral = $isIntegral;
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
     * @param int|null $childType
     *
     * @return void
     */
    public function setChildType(?int $childType): void
    {
        $this->childType = $childType;
    }

    /**
     * @param string $remark
     *
     * @return void
     */
    public function setRemark(string $remark): void
    {
        $this->remark = $remark;
    }
    /**
     * @param string $content
     *
     * @return void
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @param float|null $originPrice
     *
     * @return void
     */
    public function setOriginPrice(?float $originPrice): void
    {
        $this->originPrice = $originPrice;
    }

    /**
     * @param float|null $price
     *
     * @return void
     */
    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    /**
     * @param int|null $isPush
     *
     * @return void
     */
    public function setIsPush(?int $isPush): void
    {
        $this->isPush = $isPush;
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
     * @return int
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return int|null
     */
    public function getIsIntegral(): ?int
    {
        return $this->isIntegral;
    }

    /**
     * @return int|null
     */
    public function getType(): ?int
    {
        return $this->type;
    }

    /**
     * @return int|null
     */
    public function getChildType(): ?int
    {
        return $this->childType;
    }

    /**
     * @return string
     */
    public function getRemark(): ?string
    {
        return $this->remark;
    }
    /**
     * @return string
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @return float|null
     */
    public function getOriginPrice(): ?float
    {
        return $this->originPrice;
    }

    /**
     * @return float|null
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @return int|null
     */
    public function getIsPush(): ?int
    {
        return $this->isPush;
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
