<?php

/**
 * Suggests fields and tablenames
 * @author Andrea Mariani, fasys.it
 */
class AdminerSuggestTableField extends Adminer\Plugin {
	public function head(){
		if (!isset($_GET['sql'])) {
			return;
		}

		$suggests = [
            '___mysql___' => [
                'DELETE FROM', 'DISTINCT', 'EXPLAIN', 'FROM', 'GROUP BY', 'HAVING', 'INSERT INTO', 'INNER JOIN', 'IGNORE',
                'LIMIT', 'LEFT JOIN', 'NULL', 'ORDER BY', 'ON DUPLICATE KEY UPDATE', 'SELECT', 'UPDATE', 'WHERE',
            ]
        ];

		foreach (array_keys(Adminer\tables_list()) as $table) {
			$suggests['___tables___'][] = $table;
			foreach (Adminer\fields($table) as $field => $foo) {
				$suggests[$table][] = $field;
			}
		}

    ?>
    <style>
        #suggest_tablefields_container{min-width:200px;margin:0;padding:0;overflow-y:auto;position:absolute;background-color:#fff;}
        #suggest_tablefields{list-style:none;}
        #suggest_tablefields dt{font-weight:bold;}
        #suggest_tablefields dd{margin:0;}
        #suggest_tablefields dd strong{background-color:#ff0;}
        #suggest_search{width:110px;}
        #suggest_tablefields_drag{cursor:move;}
        #suggest_tablefields_stick{cursor:pointer;}
        .noselect {-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}
        .xborder{border: 1px inset rgb(204, 204, 204);}
        /*textarea.sqlarea {display: block!important;}*/
    </style>
    <script<?php echo Adminer\nonce() ?> type="text/javascript">

        function domReady(fn) {
            document.addEventListener("DOMContentLoaded", fn)
            if (document.readyState === "interactive" || document.readyState === "complete" ) {
                fn()
            }
        }

        function insertNodeAtCaret(node) {
            if (typeof window.getSelection != "undefined") {
                const sel = window.getSelection();
                if (sel.rangeCount) {
                    let range = sel.getRangeAt(0);
                    range.collapse(false)
                    range.insertNode(node)
                    range = range.cloneRange()
                    range.selectNodeContents(node)
                    range.collapse(false)
                    sel.removeAllRanges()
                    sel.addRange(range)
                }
            }
            else if (typeof document.selection != "undefined" && document.selection.type != "Control") {
                let html = (node.nodeType == 1) ? node.outerHTML : node.data;
                const id = "marker_" + ("" + Math.random()).slice(2);
                html += '<span id="' + id + '"></span>'
                const textRange = document.selection.createRange();
                textRange.collapse(false)
                textRange.pasteHTML(html)
                const markerSpan = document.getElementById(id);
                textRange.moveToElementText(markerSpan)
                textRange.select()
                markerSpan.parentNode.removeChild(markerSpan)
            }
        }

        function getTable(suggests, tableName){
            let table = "<dt>" + tableName + "</dt>";
            for(let k in suggests[tableName]){
                table += "<dd><a href='#' data-text='"+ suggests[tableName][k] +"'>"+ suggests[tableName][k] +"</a></dd>"
            }
            return table
        }

        function compile(data){
            document.getElementById('suggest_tablefields').innerHTML = data
            document.getElementById('suggest_search').value = '';
            //console.log(data)
        }

        domReady(() => {
            let k;
            const suggests = JSON.parse('<?php echo json_encode($suggests) ?>')
            const form = document.getElementById('form')
            const sqlarea = document.getElementsByClassName('sqlarea')[0]
            form.style.position = "relative"

            let suggests_mysql = "";

            suggests_mysql += "<dt><?php echo $this->lang('Tables') ?></dt>"
            for(k in suggests['___tables___']){
                suggests_mysql += "<dd><a href='#' data-table='1'>"+ suggests['___tables___'][k] +"</a></dd>"
            }
            suggests_mysql += "<dt><?php echo $this->lang('SQL command') ?></dt>"
            for(k in suggests['___mysql___']){
                suggests_mysql += "<dd><a href='#' data-nobt='1'>"+ suggests['___mysql___'][k] +"</a></dd>"
            }

            const posLeft = (sqlarea.offsetWidth + 3);
            form.insertAdjacentHTML('afterbegin',
                '<div id="suggest_tablefields_container" style="height:'+ sqlarea.offsetHeight +'px;top:0;left:'+ posLeft +'px">'+
                '<span class="noselect" id="suggest_tablefields_drag"><?php echo $this->lang("drag") ?></span>|'+
                '<span class="noselect" id="suggest_tablefields_stick" data-pos-left="'+ posLeft +'px"><?php echo $this->lang("stick") ?></span>&nbsp;'+
                '<input autocomplete="off" id="suggest_search" type="text" placeholder="<?php echo $this->lang('Search') ?>..."/><dl id="suggest_tablefields" class="noselect"></dl></div>')
            compile(suggests_mysql)


            document.addEventListener('click', function (event) {
                if(event.target.getAttribute('id') === 'suggest_search'){
                    return
                }
                if (event.target.matches('.jush-custom')) {
                    const table = getTable(suggests, event.target.textContent);
                    compile(table)
                    return
                }

                if (!event.target.matches('#suggest_tablefields') && !event.target.matches('a') && !event.target.matches('strong') && !event.target.matches('.sqlarea') && !event.target.matches('.jush-sql_code') && !event.target.matches('.jush-bac') && !event.target.matches('.jush-op')){
                    compile(suggests_mysql)
                    return
                }

            }, false)

            document.getElementById('suggest_tablefields').addEventListener('click', function (event){
                if(event.target.matches('a') || event.target.matches('strong')){
                    let target, text, bt = "`";
                    if(event.target.matches('strong')) {
                        target = event.target = event.target.parentElement
                    }
                    else{
                        target = event.target
                    }

                    text = target.textContent
                    sqlarea.focus()

                    if(target.getAttribute("data-text")){
                        text = target.getAttribute("data-text")
                    }
                    if(target.getAttribute("data-nobt")){
                        bt = ""
                    }

                    insertNodeAtCaret(document.createTextNode(bt + text + bt + " "))

                    if(target.getAttribute("data-table")){
                        const table = getTable(suggests, target.textContent);
                        compile(table)
                    }

                    sqlarea.dispatchEvent(new KeyboardEvent('keyup'))
                }
            }, false)


            document.getElementById('suggest_search').addEventListener('keyup', function () {
                let reg;
                const value = this.value.toLowerCase();

                if (value != '') {
                    reg = (value + '').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:])/g, '\\$1');
                    reg = new RegExp('('+ reg + ')', 'gi')
                }

                const tables = qsa('dd a', qs('#suggest_tablefields'));
                for (let i = 0; i < tables.length; i++) {
                    const a = tables[i];
                    const text = tables[i].textContent;
                    if (value == '') {
                        tables[i].className = ''
                        a.innerHTML = text
                    }
                    else {
                        tables[i].className = (text.toLowerCase().indexOf(value) == -1 ? 'hidden' : '')
                        a.innerHTML = text.replace(reg, '<strong>$1</strong>')
                    }
                }

            }, false)


            //drag / stick
            document.getElementById('suggest_tablefields_stick').addEventListener('click', function () {
                const obj = document.getElementById('suggest_tablefields_container');
                obj.style.position = "absolute"
                obj.style.left = this.getAttribute('data-pos-left')
                obj.style.top = 0
                obj.classList.remove("xborder")
            })

            window.onload = function(){
                draggable('suggest_tablefields_container')
            }

            let dragObj = null;
            function draggable(id) {
                const obj = document.getElementById(id);
                const m = document.getElementById('suggest_tablefields_drag');
                m.onmousedown = function(){
                    obj.style.position = "fixed"
                    obj.classList.add("xborder")
                    dragObj = obj
                }
            }

            document.onmouseup = function(){
                dragObj = null
            }

            document.onmousemove = function(e){
                const x = e.pageX;
                const y = e.pageY;

                if(dragObj == null) return

                dragObj.style.left = x +"px"
                dragObj.style.top= y +"px"
            }

        })

    </script>
    <?php
	}

    /*
    protected $translations = [
        'it' => [
            'drag' => 'drag',
            'stick' => 'stick',
        ]
    ];
    */

}
