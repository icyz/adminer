<?php
/** Display PHP serialized values as table in edit. Using Jakub Vrana and Martin Zeman's JSON Adminer plugin (https://raw.githubusercontent.com/vrana/adminer/master/plugins/json-column.php) as a skeleton for this plugin.
* @link https://www.adminer.org/plugins/#use
* @author Don Wilson, https://pyxol.com/
* @author Jakub Vrana, https://www.vrana.cz/
* @author Martin Zeman (Zemistr), http://www.zemistr.eu/
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerPHPSerializedColumn {
	private function _testSerial($value) {
		if(false !== ($unserialized = @unserialize($value))) {
			return $unserialized;
		}
		return $value;
	}

	private function _buildTable($unserialized) {
		echo '<table cellspacing="0" style="margin:2px; font-size:100%;">';
		foreach ($unserialized as $key => $val) {
			echo '<tr>';
			echo '<th>' . Adminer\h($key) . '</th>';
			echo '<td>';
			if (is_scalar($val) || $val === null) {
				if (is_bool($val)) {
					$val = $val ? 'true' : 'false';
				} elseif ($val === null) {
					$val = 'null';
				} elseif (!is_numeric($val)) {
					$val = '"' . Adminer\h(addcslashes($val, "\r\n\"")) . '"';
				}
				echo '<code class="jush-js">' . $val . '</code>';
			} else {
				$this->_buildTable($val);
			}
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	function editInput($table, $field, $attrs, $value) {
		$serial = $this->_testSerial($value);
		if ($serial !== $value) {
			$this->_buildTable($serial);
		}
	}
}