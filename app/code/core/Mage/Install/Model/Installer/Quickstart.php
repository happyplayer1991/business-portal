<?php
/**
 * Sample DB of Quickstart Installer
 *
 * @category   Mage
 * @package    Mage_Install
 * @author     venustheme.com
 */
class Mage_Install_Model_Installer_Quickstart extends Mage_Install_Model_Installer_Db
{
    /**
     * Install quickstart database
     *
     * @param array $data
     */
	var $quickstart_db_name = "sample_data.sql";
	
	public function installQuickstartDB ($data) {
		$config = array(
	            'host'      => $data['db_host'],
	            'username'  => $data['db_user'],
	            'password'  => $data['db_pass'],
	            'dbname'    => $data['db_name']
	        );

		$tablePrefix = $data['db_prefix'];
		$base_url = $data['unsecure_base_url'];
		$base_surl = $base_url;
		if (!empty($data['use_secure'])) $base_surl = $data['secure_base_url'];	
		$file = Mage::getConfig()->getBaseDir().'/sql/'.$this->quickstart_db_name;

		if (is_file($file)) {
        	$read = Mage::getSingleton('core/resource')->createConnection('core_setup', $this->_getConnenctionType(), $config);
			
			$contents = file_get_contents ($file);
			$sqls = self::parseSQL ($contents);
			foreach ($sqls as $sql) {
				$sql = trim(str_replace ('#__', $tablePrefix, $sql));
				
				if ($sql) {
					$read->query($sql);
				}
			}
			return true;
		}
		return false;
	}

	/**
	 * Using php mysql to install sample data
	 */
	public function installSampleDB ($data) {
		//Get content from sample data
		//Default sample data
		//$tablePrefix = (string)Mage::getConfig()->getTablePrefix();
		$tablePrefix = $data['db_prefix'];
		$base_url = $data['unsecure_base_url'];
		$base_surl = $base_url;
		if (!empty($data['use_secure'])) $base_surl = $data['secure_base_url'];
		
		/* Run sample_data.sql if found, by pass default sample data from Magento */		
		$file = Mage::getConfig()->getBaseDir().'/sql/'.$this->quickstart_db_name;

		if (is_file($file)) { //echo $file; die();
			//connect to DB
			$link = mysql_connect($data['db_host'], $data['db_user'], $data['db_pass']);
			if (!$link) {
				//echo  "Please <a href=\"javascript:history.back(-1)\">Go back</a> and update Config<br /><br />";
				//die ("Cannot connect to mysql server.");
				return false;
			}
			if (!mysql_select_db($data['db_name'], $link)) { 
				//close DB connection
				mysql_close ($link);
				//echo  "Please <a href=\"javascript:history.back(-1)\">Go back</a> and update Config<br /><br />";
				//die ("Cannot Connect to Database [{$data['db_name']}]");
				return false; 
			}
			$contents = file_get_contents ($file);
			$sqls = self::parseSQL ($contents);
			foreach ($sqls as $sql) {
				$sql = trim(str_replace ('#__', $tablePrefix, $sql));
				//Excute this sql				
				if ($sql && !mysql_query($sql, $link)) {
					//close DB connection
					mysql_close ($link);
					//echo  "Please <a href=\"javascript:history.back(-1)\">Go back</a> and update Config<br /><br />";
					//echo "Cannot excute Statment <br />[$sql]<br />Error: [".mysql_errno()."]<br />Error Msg: [".mysql_error()."]";
					//die();
					return false;
				}
			}
			//close DB connection
			mysql_close ($link);
			return true;
		}
		return false;
	}
	
	function parseSQL ($contents) {
		$comment_patterns = array('/\/\*.*(\n)*.*(\*\/)?/', 
								  '/^\s*--.*\n/',
								  '/^\s*#.*\n/',
								  );
		$contents = preg_replace($comment_patterns, "\n", $contents);
		$statements = explode(";\n", $contents);

		return $statements;
	}
}