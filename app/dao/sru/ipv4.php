<?
/**
 * ip
 */
class UFdao_Sru_Ipv4
extends UFdao {

	public function getFreeByDormitoryId($dormitory) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->dormitoryId, $dormitory);
		$query->where($mapping->host, null);

		return $this->doSelectFirst($query);
	}

	public function getByIp($ip) {
		$mapping = $this->mapping('get');

		$query = $this->prepareSelect($mapping);
		$query->where($mapping->ip, $ip);
		$query->where($mapping->host, null);

		return $this->doSelectFirst($query);
	}
}
