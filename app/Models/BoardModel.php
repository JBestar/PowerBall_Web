public function getLatest($category)
{
    return $this->where('category',$category)
                ->orderBy('id','DESC')
                ->limit(7)
                ->find();
}