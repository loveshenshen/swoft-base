<?php declare(strict_types=1);


namespace App\Model\Entity;

use Swoft\Db\Annotation\Mapping\Column;
use Swoft\Db\Annotation\Mapping\Entity;
use Swoft\Db\Annotation\Mapping\Id;
use Swoft\Db\Eloquent\Model;


/**
 * 
 * Class Region
 *
 * @since 2.0
 *
 * @Entity(table="region")
 */
class Region extends Model
{
    /**
     * 编号
     * @Id()
     * @Column()
     *
     * @var int
     */
    private $id;

    /**
     * 父id
     *
     * @Column(name="parent_id", prop="parentId")
     *
     * @var int|null
     */
    private $parentId;

    /**
     * 简称
     *
     * @Column()
     *
     * @var string|null
     */
    private $shortname;

    /**
     * 名称
     *
     * @Column(name="region_name", prop="regionName")
     *
     * @var string|null
     */
    private $regionName;

    /**
     * 全称
     *
     * @Column(name="merger_name", prop="mergerName")
     *
     * @var string|null
     */
    private $mergerName;

    /**
     * 层级 0 1 2 省市区县
     *
     * @Column(name="region_type", prop="regionType")
     *
     * @var int|null
     */
    private $regionType;

    /**
     * 拼音
     *
     * @Column()
     *
     * @var string|null
     */
    private $pinyin;

    /**
     * 长途区号
     *
     * @Column()
     *
     * @var string|null
     */
    private $code;

    /**
     * 邮编
     *
     * @Column()
     *
     * @var string|null
     */
    private $zip;

    /**
     * 首字母
     *
     * @Column()
     *
     * @var string|null
     */
    private $first;

    /**
     * 经度
     *
     * @Column()
     *
     * @var string|null
     */
    private $lng;

    /**
     * 纬度
     *
     * @Column()
     *
     * @var string|null
     */
    private $lat;


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
     * @param int|null $parentId
     *
     * @return void
     */
    public function setParentId(?int $parentId): void
    {
        $this->parentId = $parentId;
    }

    /**
     * @param string|null $shortname
     *
     * @return void
     */
    public function setShortname(?string $shortname): void
    {
        $this->shortname = $shortname;
    }

    /**
     * @param string|null $regionName
     *
     * @return void
     */
    public function setRegionName(?string $regionName): void
    {
        $this->regionName = $regionName;
    }

    /**
     * @param string|null $mergerName
     *
     * @return void
     */
    public function setMergerName(?string $mergerName): void
    {
        $this->mergerName = $mergerName;
    }

    /**
     * @param int|null $regionType
     *
     * @return void
     */
    public function setRegionType(?int $regionType): void
    {
        $this->regionType = $regionType;
    }

    /**
     * @param string|null $pinyin
     *
     * @return void
     */
    public function setPinyin(?string $pinyin): void
    {
        $this->pinyin = $pinyin;
    }

    /**
     * @param string|null $code
     *
     * @return void
     */
    public function setCode(?string $code): void
    {
        $this->code = $code;
    }

    /**
     * @param string|null $zip
     *
     * @return void
     */
    public function setZip(?string $zip): void
    {
        $this->zip = $zip;
    }

    /**
     * @param string|null $first
     *
     * @return void
     */
    public function setFirst(?string $first): void
    {
        $this->first = $first;
    }

    /**
     * @param string|null $lng
     *
     * @return void
     */
    public function setLng(?string $lng): void
    {
        $this->lng = $lng;
    }

    /**
     * @param string|null $lat
     *
     * @return void
     */
    public function setLat(?string $lat): void
    {
        $this->lat = $lat;
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getParentId(): ?int
    {
        return $this->parentId;
    }

    /**
     * @return string|null
     */
    public function getShortname(): ?string
    {
        return $this->shortname;
    }

    /**
     * @return string|null
     */
    public function getRegionName(): ?string
    {
        return $this->regionName;
    }

    /**
     * @return string|null
     */
    public function getMergerName(): ?string
    {
        return $this->mergerName;
    }

    /**
     * @return int|null
     */
    public function getRegionType(): ?int
    {
        return $this->regionType;
    }

    /**
     * @return string|null
     */
    public function getPinyin(): ?string
    {
        return $this->pinyin;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getZip(): ?string
    {
        return $this->zip;
    }

    /**
     * @return string|null
     */
    public function getFirst(): ?string
    {
        return $this->first;
    }

    /**
     * @return string|null
     */
    public function getLng(): ?string
    {
        return $this->lng;
    }

    /**
     * @return string|null
     */
    public function getLat(): ?string
    {
        return $this->lat;
    }

}
