<?
/**
 * admin
 */
class UFdao_SruAdmin_Penalty
extends UFdao {

	public function listAll($page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->active, true); 
		$query->order($mapping->endAt,  $query->ASC);
		
			
		return $this->doSelect($query);
	}

	public function listAllByUserId($id, $page=1, $perPage=10, $overFetch=0) {
		$mapping = $this->mapping('list');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->userId, $id);
		$query->order($mapping->endAt,  $query->DESC);
		
		return $this->doSelect($query);
	}
}
