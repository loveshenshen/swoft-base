<?php declare(strict_types=1);
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://swoft.org/docs
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace App\Http\Controller;

use App\Common\Response;
use App\Model\Dao\RegionDao;
use App\Model\Entity\Region;
use Swoft\Context\Context;
use Swoft\Db\Eloquent\Collection;
use Swoft\Http\Message\Request;
use Swoft\Http\Server\Annotation\Mapping\Controller;
use Swoft\Http\Server\Annotation\Mapping\RequestMapping;
use Swoft\Http\Server\Annotation\Mapping\RequestMethod;
use Swoft\Log\Helper\CLog;
use Swoft\Redis\Pool;
use Swoft\Redis\Redis;
use Swoft\Bean\Annotation\Mapping\Inject;

// use Swoft\Http\Message\Response;

/**
 * Class RegionController
 *
 * @Controller(prefix="/region")
 * @package App\Http\Controller
 */
class RegionController
{

    /**
     * @Inject()
     * @var Pool
     */
    public $redis;

    /**
     * Get data list. access uri path: region
     * @RequestMapping(route="list", method=RequestMethod::GET)
     * @return array
     * @throws
     */
    public function region(Request $request): array
    {
        $type = $request->get("type");
        $parentId = $request->get("parent_id",null);
        $page = $request->get("page", 0 );
        $num = $request->get("num",10);

        $key = RegionDao::getRegionKey($type,$parentId,$page,$num);
        $totalKey = RegionDao::getRegionTotalKey($type,$parentId,$page,$num);
        if($this->redis->exists($key)){
            $data = [
                'list'=>json_decode($this->redis->get($key),true),
                'total'=>$this->redis->get($totalKey)
            ];
        }else{
            $region = Region::where("region_type",$type);
            if(isset($parentId)){
                $region->where("parent_id",$parentId);
            }
            $total = $region->count();
            $regions = $region->offset($page * $num )->limit(intval($num))->get(["id","region_name"]);
            $this->redis->set($key,json_encode($regions));
            $this->redis->set($totalKey,$total);
            $data = [
                'list'=>$regions,
                'total'=>$total
            ];
        }
        return $data;
    }


    /**
     * Get one by ID. access uri path: /region/{id}
     * @RequestMapping(route="{id}", method=RequestMethod::GET)
     * @return array
     */
    public function get(): array
    {
        return ['item0'];
    }

    /**
     * Create a new record. access uri path: /region
     * @RequestMapping(route="/region", method=RequestMethod::POST)
     * @return array
     */
    public function post(): array
    {
        return ['id' => 2];
    }

    /**
     * Update one by ID. access uri path: /region/{id}
     * @RequestMapping(route="{id}", method=RequestMethod::PUT)
     * @return array
     */
    public function put(): array
    {
        return ['id' => 1];
    }

    /**
     * Delete one by ID. access uri path: /region/{id}
     * @RequestMapping(route="{id}", method=RequestMethod::DELETE)
     * @return array
     */
    public function del(): array
    {
        return ['id' => 1];
    }
}
