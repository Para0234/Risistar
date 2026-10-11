{include file="main.header.tpl" bodyclass="popup"}
<script>
if (window.frameElement && window.frameElement.id === 'fancybox-frame') {
	document.body.classList.add('in-dialog');
}
</script>
<header class="dialog-chrome">
	<h1 class="dialog-chrome-title" id="dialogChromeTitle"></h1>
</header>
<div id="content">{block name="content"}{/block}
</div>
<script>
(function () {
	if (!document.body.classList.contains('in-dialog')) {
		return;
	}
	var content = document.getElementById('content');
	var root = content.querySelector(':scope > form') || content;
	var table = null;
	var i;
	for (i = 0; i < root.children.length; i++) {
		if (root.children[i].tagName === 'TABLE') {
			table = root.children[i];
			break;
		}
	}
	var row = table && table.tBodies[0] && table.tBodies[0].rows[0];
	if (!row || row.cells.length !== 1 || row.cells[0].tagName !== 'TH') {
		return;
	}
	document.getElementById('dialogChromeTitle').textContent = row.cells[0].textContent.replace(/\s+/g, ' ').trim();
	row.parentNode.removeChild(row);
})();
</script>
{include file="main.footer.tpl" nocache}