<?
/**
 * wyświetlenie uploaderow
 */
class UFview_SruApi_Uploaders
extends UFview_SruApi {

	public function fillData() {
		$box = UFra::shared('UFbox_SruApi');

		$this->append('body', $box->uploaders());
	}
}