<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/11 19:49
 * Describe：
 */

class BaseHasSubActiveRecord extends BaseActiveRecord
{
    public function __set($name, $value)
    {
        $relations= $this->relations();
        if(isset($relations["sub"]) && $this->checkIsSub($name))
        {
            $newName=$this->getSubName($name);
            if(empty($this->sub))
            {
                $this->sub=new $relations["sub"][1];
            }
            $this->sub[$newName]=$value;
        }
        else
            parent::__set($name, $value); // TODO: Change the autogenerated stub
    }

    public function __get($name)
    {
        /*if(!empty($this->sub) && $this->checkIsSub($name))
        {
            $newName=$this->getSubName($name);
            return $this->sub[$newName];
        }*/

        if($this->checkIsSub($name))
        {
            $newName=$this->getSubName($name);
            if(!empty($this->sub)){
                return $this->sub[$newName];
            }else{
                return parent::__get($newName);
            }
            
            
        }
        return parent::__get($name); // TODO: Change the autogenerated stub
    }


    protected function getSubName($name)
    {
        return str_replace("_sub","",$name);
    }

    protected function checkIsSub($name)
    {
        return substr($name,strpos($name,"_sub"))=="_sub";
    }

    protected function afterSave()
    {
        if(!empty($this->sub) && !empty($this->sub["unit"]) && $this->unit!=$this->sub["unit"])
        {
            $res=true;
            if($this->sub->isNewRecord)
            {
                $this->sub->setPrimaryKey($this->getPrimaryKey());
                $this->sub->update_user_id = $this->update_user_id;
                $this->sub->update_time = $this->update_time;
                $res=$this->sub->save();
            }
            else
            {
                $this->sub->setDiffAttributes();
                $diff= $this->sub->getDiffAttributes();
                if(is_array($diff) && count($diff)>0)
                    $res=$this->sub->save();
            }
            if(!$res)
            {
                throw new Exception("保存Sub信息出错");
            }
        }

        parent::afterSave(); // TODO: Change the autogenerated stub
    }


    protected function beforeFind()
    {
        $relations= $this->relations();
        if(isset($relations["sub"]))
            $this->with("sub");
        parent::beforeFind(); // TODO: Change the autogenerated stub
    }


}