<?php


namespace app\common\model;


class Power extends App
{

    /**
     * 角色已有的权限
     * @param int $role_id
     * @return array
     */
    public function havePower($role_id = 0):array
    {
        if (!$role_id) return [];

        return $this->where('role_id', $role_id)->column('route_id');
    }



    /**
     * 设置权限
     * @param $data
     * @param $role_id
     * @return bool
     */
    public function setPower($data, $role_id)
    {
        //if (!$data) return false;
        $this->startTrans();
        try {
            $this->where('role_id', $role_id)->delete();
            $this->insertAll($data);
            $this->commit();
        } catch (\Exception $exception) {
            $this->rollback();
            return false;
        }
        return true;
    }

}