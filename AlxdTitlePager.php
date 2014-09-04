<?php
class AlxdTitlePager extends CLinkPager
{
    public $fields = array();
    public $length = null;

    protected function createPageButtons()
    {
        if(($pageCount=$this->getPageCount())<=1)
            return array();

        list($beginPage,$endPage)=$this->getPageRange();
        $currentPage=$this->getCurrentPage(false); // currentPage is calculated in getPageRange()
        $buttons=array();

        // first page
        $buttons[]=$this->createPageButton($this->firstPageLabel,0,$this->firstPageCssClass,$currentPage<=0,false);

        // prev page
        if(($page=$currentPage-1)<0)
            $page=0;
        $buttons[]=$this->createPageButton($this->prevPageLabel,$page,$this->previousPageCssClass,$currentPage<=0,false);

        // internal pages
        $directions = $this->owner->dataProvider->sort->directions;
        $field = is_array($directions) && in_array(key($directions), $this->fields) ? key($directions) : null;
        unset($directions);

        if (is_null($field))
            for($i=$beginPage;$i<=$endPage;++$i)
            {
                $buttons[]=$this->createPageButton($i+1, $i,$this->internalPageCssClass,false,$i==$currentPage);
            }
        else
        {
            if ($this->owner->dataProvider instanceof CActiveDataProvider)
            {
                $pagesize = $this->owner->dataProvider->pagination->pagesize;
                $criteria = $this->owner->dataProvider->criteria;
                $this->owner->dataProvider->sort->applyOrder($criteria);
                $criteria->limit = 1;

                for($i=$beginPage;$i<=$endPage;++$i)
                {
                    $criteria->offset = $i*$pagesize;
                    $m = $this->owner->dataProvider->model->find($criteria);
                    $buttons[]=$this->createPageButton(is_null($this->length) ? $m->{$field} : mb_substr($m->{$field}, 0, $this->length, 'utf-8'), $i,$this->internalPageCssClass,false,$i==$currentPage);
                }
            }
            else
                if ($this->owner->dataProvider instanceof CArrayDataProvider)
                {
                    $pagesize = $this->owner->dataProvider->pagination->pagesize;
                    $data = $this->owner->dataProvider->rawData;
                    for($i=$beginPage;$i<=$endPage;++$i)
                    {
                        $m = $data[$i*$pagesize];
                        $buttons[]=$this->createPageButton(is_null($this->length) ? $m[$field] : mb_substr($m[$field], 0, $this->length, 'utf-8'), $i,$this->internalPageCssClass,false,$i==$currentPage);
                    }
                }


        }
        // next page
        if(($page=$currentPage+1)>=$pageCount-1)
            $page=$pageCount-1;
        $buttons[]=$this->createPageButton($this->nextPageLabel,$page,$this->nextPageCssClass,$currentPage>=$pageCount-1,false);

        // last page
        $buttons[]=$this->createPageButton($this->lastPageLabel,$pageCount-1,$this->lastPageCssClass,$currentPage>=$pageCount-1,false);

        return $buttons;
    }
}
?>
