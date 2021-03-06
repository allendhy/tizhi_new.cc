<?php
namespace Home\Model;
use Think\Model;
class ImportDetailNewModel extends Model {
        public function get_details($school_year,$town_id,$import_id,$is_error=''){
                $where = array();

                $where['detail.partition_field'] = intval($town_id . $school_year);
                $where['detail.import_id'] = $import_id;

                $nums = $this->alias('detail')->field('sum(1) total_rows,sum(CASE is_error when 1 then 1 else 0 end) error_rows')->where($where)->find();

                if($is_error!=''){
                        $where['detail.is_error'] = $is_error;
                }

        	$count = $this->alias('detail')->join('LEFT JOIN import_log log ON log.import_id = detail.import_id')->join('LEFT JOIN school s ON s.school_id = log.user_id')->where($where)->count();
        	//分页
        	$page = new \Think\Page($count,C('PAGE_LISTROWS'));
        	$limit = ($page->firstRow . ',' . $page->listRows);

                $list = $this->alias('detail')->field('detail.*,s.school_name,t.town_name')->join('LEFT JOIN import_log log ON log.import_id = detail.import_id')->join('LEFT JOIN school s ON s.school_id = log.user_id')->join('LEFT JOIN town t ON t.town_id = s.town_id')->where($where)->order('detail_id')->limit($limit)->select();
                $show = $page->show();
                return array('list'=>$list,'page'=>$show,'nums'=>$nums);
	}

        public function get_detail_info($partition_field,$detail_id){
                $where['idd.partition_field'] = intval($partition_field);
                $where['idd.detail_id'] = $detail_id;
                return $this->alias('idd')->field('log.user_id,idd.*')->join('LEFT JOIN import_log log ON log.partition_field = idd.partition_field AND log.import_id = idd.import_id ')->where($where)->find();
        }

}