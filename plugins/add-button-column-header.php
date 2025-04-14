<?php

/** Add a quick link under each header column
* @author Andrea Mariani, https://fasys.it
*/
class AdminerAddButtonColumnHeader extends Adminer\Plugin {

    const DISTINCT = "DISTINCT";
    private $tableName;
    private $buttons;

    function __construct($buttons = [self::DISTINCT]) {
        $this->buttons = $buttons;
    }

    function tableName($tableStatus){
        $this->tableName = $tableStatus['Name'];
    }

	function selectColumnsPrint() {
        ?>
        <script<?php echo Adminer\nonce() ?> type="text/javascript">
            function domReady(fn) {
                document.addEventListener("DOMContentLoaded", fn);
                if (document.readyState === "interactive" || document.readyState === "complete" ) {
                    fn();
                }
            }
            function closest(el, tag) {
                while (el && el.nodeName !== tag) {
                    el = el.parentElement;
                }
                return el;
            }

            domReady(() => {
                document.querySelectorAll("table#table thead th .column").forEach(el => {
                    const fieldname = closest(el, 'TH').innerText;

                    <?php
                    foreach($this->buttons as $button) {
                        switch($button){
                            case self::DISTINCT:
                                echo "el.insertAdjacentHTML(\"beforeend\", \"<div class='AdminerAddButtonColumnHeader'><a href='?username=". $_GET['username'] ."&db=" . $_GET['db'] ."&sql=SELECT DISTINCT `\"+ fieldname +\"` FROM `". $this->tableName ."`;'>Distinct</a></div>\");";
                                break;

                            //TODO: waiting for other ideas...
                        }
                    }
                    ?>
                });

            });
        </script>
        <?php

	}
}
