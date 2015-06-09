<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
class es_cls_delivery
{
	public static function es_delivery_select($sentguid = "", $offset = 0, $limit = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."es_deliverreport` where 1=1";
		if($sentguid <> "")
		{
			$sSql = $sSql . " and es_deliver_sentguid='".$sentguid."'";
			$sSql = $sSql . " order by es_deliver_id desc limit $offset, $limit";
		}
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function es_delivery_count($sentguid = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($sentguid <> "")
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."es_deliverreport` WHERE `es_deliver_sentguid` = %s", array($sentguid));
		}
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function es_delivery_ins($guid = "", $dbid = 0, $email = "")
	{
		global $wpdb;
		$returnid = 0;
		$prefix = $wpdb->prefix;
		$currentdate = date('Y-m-d G:i:s'); 
		$sSql = $wpdb->prepare("INSERT INTO `".$prefix."es_deliverreport` (`es_deliver_sentguid`,`es_deliver_emailid`, `es_deliver_emailmail`,
								`es_deliver_sentdate`,`es_deliver_status`) VALUES (%s, %s, %s, %s, %s)", array($guid, $dbid, $email, $currentdate, "Nodata"));			
		$wpdb->query($sSql);
		$returnid = $wpdb->insert_id;
		return $returnid;
	}
	
	public static function es_delivery_ups($id = 0)
	{
		global $wpdb;
		$returnid = 0;
		$prefix = $wpdb->prefix;
		$currentdate = date('Y-m-d G:i:s'); 
		if(is_numeric($id))
		{
			$sSql = $wpdb->prepare("UPDATE `".$prefix."es_deliverreport` SET `es_deliver_status` = %s, 
						`es_deliver_viewdate` = %s WHERE es_deliver_id = %d LIMIT 1", array("Viewed", $currentdate, $id));	
			$wpdb->query($sSql);
		}
		return true;
	}
}
?>