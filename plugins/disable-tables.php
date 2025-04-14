<?php

/** Disable tables
 * @link https://www.adminer.org/plugins/#use
 * @author Andrea Mariani, https://www.fasys.it/
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
 */
class AdminerDisableTables extends Adminer\Plugin {
    private $disabledTables;

    function __construct(array $disabledTables = []) {
        $this->disabledTables = array_map('strtolower', $disabledTables);
    }

    function tableName($tableStatus) {

        $select = isset($_GET['table']) ? strtolower(htmlspecialchars($_GET['table'], ENT_QUOTES, 'UTF-8')) : '';

        if($select) {
            if (in_array($select, $this->disabledTables) === true) {
                die($this->lang('Access Denied') . '.');
            }
        }

        if (in_array(strtolower($tableStatus['Name']), $this->disabledTables) === true) {
            return false;
        }

        return Adminer\h($tableStatus['Name']);
    }

    protected $translations = [
        'it' => [
            'Access Denied' => 'Accesso Negato',
        ]
    ];

}
