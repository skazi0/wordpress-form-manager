<?php
include 'types.php';

class fm_display_class{


///////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////

//options:
//	class - 'class' attribute for the <form> tag
//	action - 'action' attribute for the <form> tag
//'params' is an associative array of hidden values inserted into the form
function displayForm($formInfo, $options=array(), $params=array()){
	global $msg;
	global $fmdb;
	global $fm_controls;	
	
	$validation_required = array();
		
	//default div id
	if(!isset($options['class'])) $options['class'] = 'fm-form';
	
	$str = "";
	$str.= "<form class=\"".$options['class']."\" method=\"post\" action=\"".$options['action']."\" name=\"fm-form-".$formInfo['ID']."\" id=\"fm-form-".$formInfo['ID']."\">\n";
	
	if($formInfo['show_border']==1)
		$str.= "<fieldset>\n";
	
	if($formInfo['show_title']==1)
		if($formInfo['show_border']==1)
			$str.= "<legend>".$formInfo['title']."</legend>\n";
		else
			$str.= "<h3>".$formInfo['title']."</h3>\n";
	
	$str.= "<ul>\n";
	
		foreach($formInfo['items'] as $item){
			$str.= "<li class=\"".$item['type']."\">";
			
			////////////////////////////////////////////////////////////////////////////////////////
			
			//if($formInfo['labels_on_top']==1) $str.="<p>";
			
			if($formInfo['labels_on_top']==0 || $item['type']=='checkbox')	//display width adjusted labels for checkboxes, or if labels are on the left
				$str.= '<label for="'.$item['unique_name'].'" style="width:'.$formInfo['label_width'].'px">'.$item['label'];
			else
				$str.= '<label for="'.$item['unique_name'].'">'.$item['label'];
				
			if($item['required']=='1')	$str.= '<em>*</em>';
			$str.= "</label>\n";			
			if($formInfo['labels_on_top']==1 && $item['type']!='checkbox') $str.="<br />";
			
			if(isset($fm_controls[$item['type']])) $str.=$fm_controls[$item['type']]->showItem($item['unique_name'], $item);
			else $str.=$fm_controls['default']->showItem($item['unique_name'], $item);
			
			////////////////////////////////////////////////////////////////////////////////////////
			
			$str.= "</li>\n";
		}
	
	$str.= "</ul>\n";
	
	///// show the submit button //////
	$str.= "<input type=\"submit\" ".
			"name=\"fm_form_submit\" ".
			"class=\"submit\" ".
			"value=\"".$formInfo['submit_btn_text']."\" ".
			"onclick=\"return fm_validate(".$formInfo['ID'].")\" ".
			" />\n";
	
	if($formInfo['show_border']==1)	
		$str.= "</fieldset>\n";		
	
	//// echo the nonce ////	
	//$str.= "<input type=\"hidden\" name=\"fm_nonce\" value=\"".$fmdb->getNonce()."\" />\n";
	$str.=  wp_nonce_field('fm-submit', 'fm-submit-nonce', true, false );
	$str.= "<input type=\"hidden\" name=\"fm_id\" value=\"".$formInfo['ID']."\" />\n";
	
	$str.= "</form>\n";
	
	
	////// show the validation scripts /////
	$str.="<!-- validation -->\n";
	$str.="<script type=\"text/javascript\">\n";
	foreach($formInfo['items'] as $item){
		if($item['required'] == '1'){
		 	$callback = $fm_controls[$item['type']]->getRequiredValidatorName();
			if($callback != "")
				$str.="fm_val_register_required('".$formInfo['ID']."', ".
					"'".$item['unique_name']."', ".
					"'".$callback."', ".
					"'".format_string_for_js(sprintf($formInfo['required_msg'], $item['label']))."');\n";
		}
	}	
	$str.="</script>\n";
	$str.="<!-- /validation -->\n";
	return $str;
}
///////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////

function getEditorItem($uniqueName, $type, $itemInfo){
	global $fm_controls;
	
	if(isset($fm_controls[$type]))
		$control = $fm_controls[$type];
	else
		$control = $fm_controls['default'];
	// a new item
	if($itemInfo == null) $itemInfo = $control->itemDefaults();
	
	$itemInfo['type'] = $type;
	$itemInfo['unique_name'] = $uniqueName;
	
	$str = "<table class=\"editor-item-table\">".
			"<tr>".
			"<td class=\"editor-item-buttons\"><a href=\"#\" class=\"handle\">move</a></td>".			
			"<td class=\"editor-item-buttons\"><a href=\"#\" onclick=\"fm_showEditDivCallback('{$uniqueName}','".$control->getShowHideCallbackName()."')\" id=\"{$uniqueName}-edit\"/>edit</a></td>"		.
			"<td class=\"editor-item-container\">".$control->showEditorItem($uniqueName, $itemInfo)."</td>".
			"<td class=\"editor-item-buttons\">"."<a href=\"#\" onclick=\"fm_deleteItem('{$uniqueName}')\">delete</a>"."</td>".
			"</tr>".
			"</table>".
			"<input type=\"hidden\" id=\"{$uniqueName}-type\" value=\"{$type}\" />";
	
	return $str;
}

///////////////////////////////////////////////////////////////////////////////////////////////
}

//some helper functions 

//shortens a string to a specified width; if $useEllipse is true (default), three of these characters will be '...'
function fm_restrictString($string, $length, $useEllipse = true){
	if(strlen($string)<=$length) return $string;
	if($length > 3 && $useEllipse)	return substr($string, 0, $length-3)."...";
	else return substr($string, 0, $length);
}
?>