<?php 
global $fmdb;
global $fm_globalSettings;

/////////////////////////////////////////////////////////////////////////////////////
// Process settings changes

if(isset($_POST['submit-settings'])){
	//echo "<pre>".print_r($_POST,true)."</pre>";
	
	////////////////////////////////////////////////////////////////////////////////////
	//Process validators
	$count = 0;
	$validators = array();
	for($x=0;$x<$_POST['validator-list-count'];$x++){
		if(isset($_POST['validator-list-item-'.$x.'-name'])){
			$val = array();
			$val['name'] = $_POST['validator-list-item-'.$x.'-name'];
			$val['label'] = stripslashes($_POST['validator-list-item-'.$x.'-label']);
			$val['message'] = stripslashes($_POST['validator-list-item-'.$x.'-message']);
			$val['regexp'] = stripslashes($_POST['validator-list-item-'.$x.'-regexp']);
			
			if($val['name'] == "")
				$val['name'] = 'validator-'.$x;
				
			$validators[$val['name']] = $val;
		}		
	}
	
	$fmdb->setTextValidators($validators);
	////////////////////////////////////////////////////////////////////////////////////
	
	//echo "<pre>".print_r($validators,true)."</pre>";
}

/////////////////////////////////////////////////////////////////////////////////////
$fm_globalSettings = $fmdb->getGlobalSettings();

?>
<script type="text/javascript">
function fm_saveSettingsAdvanced(){
	document.getElementById('validator-list-count').value = fm_getManagedListCount('validator-list');
	return true;
}

/***************************************************************/

var fm_managedLists = [];
function fm_createManagedList(_ulID, _callback, _liClass){	
	var data = {
		ulID: _ulID,
		count: 0,
		createCallback: _callback,
		liClass: _liClass
	};
	fm_managedLists[_ulID] = data;
}
function fm_addManagedListItem(ulID, val){
	var UL = document.getElementById(ulID);	
	var newLI = document.createElement('li');
	var newItemID = ulID + '-item-' + fm_managedLists[ulID].count;	
	eval("var HTML = " + fm_managedLists[ulID].createCallback + "('" + ulID + "', '" + newItemID + "', val);");
	newLI.innerHTML = HTML;
	newLI.id = newItemID;
	newLI.className = fm_managedLists[ulID].liClass;
	UL.appendChild(newLI);
	fm_managedLists[ulID].count++;	
}
function fm_removeManagedListItem(itemID){
	var listItem = document.getElementById(itemID);
	listItem.parentNode.removeChild(listItem);
}
function fm_getManagedListCount(ulID){
	return fm_managedLists[ulID].count;
}

/***************************************************************/
</script>

<form name="fm-main-form" id="fm-main-form" action="" method="post">
<input type="hidden" value="1" name="message" id="message-post" />
<input type="hidden" name="validator-list-count" id="validator-list-count" value="" />

<div class="wrap">
<div id="icon-edit-pages" class="icon32"></div>
<h2>Form Manager Settings - Advanced</h2>

	<div id="message-container"><?php 
	if(isset($_POST['message']))
		switch($_POST['message']){
			case 1: ?><div id="message-success" class="updated"><p><strong>Settings Saved. </strong></p></div><?php break;
			case 2: ?><div id="message-error" class="error"><p>Save failed. </p></div><?php break;
			default: ?>
				<?php if(isset($_POST['message']) && trim($_POST['message']) != ""): ?>
				<div id="message-error" class="error"><p><?php echo stripslashes($_POST['message']);?></p></div>
				<?php endif; ?>
			<?php
		} 
	?></div>

<h3>Text Validators</h3>
<table class="form-table">
	<table border=0>
	<tr>
		<td style="width:200px;"><strong>Name</strong></td>
		<td style="width:200px;"><strong>Error Message</strong></td>
		<td style="width:400px;"><strong>Regular Expression</strong></td>
	</tr>
	</table>
	<ul id="validator-list" style="padding-bottom:20px;">		
	</ul>
	<script type="text/javascript" >	
	fm_createManagedList('validator-list', 'fm_new_validator', '');
	
	function fm_new_validator(ulID, itemID, value){
		return "<input type=\"text\" value=\"" + value.label + "\" name=\"" + itemID + "-label\" style=\"width:200px;\"/>" +
				"<input type=\"text\" value=\"" + value.message + "\" name=\"" + itemID + "-message\" style=\"width:200px;\" />" + 
				"<input type=\"text\" value=\"" + value.regexp + "\" name=\"" + itemID + "-regexp\" style=\"width:400px;\" />" + 
				"<input type=\"hidden\" value=\"" + value.name + "\" name=\"" + itemID + "-name\" />" +
				"&nbsp;&nbsp;<a onclick=\"fm_removeManagedListItem('" + itemID + "')\" style=\"cursor: pointer\">delete</a>";
	}
	<?php 
	$validators = $fmdb->getTextValidators(); 
	foreach($validators as $val){
		$val['label'] = htmlspecialchars(addslashes($val['label']));
		$val['message'] = htmlspecialchars(addslashes($val['message']));
		$val['regexp'] = htmlspecialchars(addslashes($val['regexp']));
		echo "var validator = { name: '".$val['name']."', label: '".$val['label']."', message: '".$val['message']."', regexp: '".$val['regexp']."' };\n";
		echo "fm_addManagedListItem('validator-list', validator);\n";
	}	
	?>
	var fm_blankItem = { name: "", label: "", message: "", regexp: "" };
	</script>
	</pre>
	<a class="button" onclick="fm_addManagedListItem('validator-list', fm_blankItem)" >Add </a>
</table>

</div>

<p class="submit"><input type="submit" name="submit-settings" id="submit" class="button-primary" value="Save Changes" onclick="return fm_saveSettingsAdvanced()" /></p>
</form>