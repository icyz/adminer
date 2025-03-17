<?php

/** Add button ("distinct") in every columns
* @author Andrea Mariani, https://fasys.it
*/
class AdminerViewColumn {

    private $tableName;

    function tableName($tableStatus){
        $this->tableName = $tableStatus['Name'];
    }

	function selectColumnsPrint(/* array $select, array $columns */) {
        //var_dump($select);
        //var_dump($columns);
        //die("$this->tableName");
        //$x =query_redirect("SELECT DISTINCT `id_articolo_magazzino_chiusura_testa` FROM `articolo_magazzino_chiusura_riga`;");
        //Zend_Debug::dump($x);die;
        //global $connection, $error, $adminer;

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
                    //console.log(fieldname);
                    el.insertAdjacentHTML("beforeend",
                        "<a href='?username=<?php echo $_GET['username'] ?>&db=<?php echo $_GET['db'] ?>&sql=SELECT DISTINCT `"+ fieldname +"` FROM `<?= $this->tableName ?>`;'><?= Adminer\lang("Distinct") ?></a>");
                });

            });
        </script>
        <?php

	}
}
