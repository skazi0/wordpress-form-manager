<?php
/*****

PHP classes for defining and displaying form elements

provides an instantiation $fe_formElements that converts from form definitions (arrays) to html of the form item

array definition:
'type' : type of element ('text', 'select', 'textarea', 'checkbox', 'radio', 'button', 'submit')
'default' : default value for select, radio, and textarea
'options' : for select/radio, an associative array of options (key=>value)
'attributes' : an associative array of attributes for the input/select/textarea tag
'separator' : for radio button list; html to separate radio options


*****/

////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

//textarea
function fe_getTextareaHTML($elementDef){
	return "<textarea ".fe_getAttributeString($elementDef['attributes']).">".$elementDef['default']."</textarea>";
}

////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

//radio buttons; can be a single button, or a group
function fe_getRadioHTML($elementDef){
	if(!isset($elementDef['options'])) //single radio button
		return "<input type=\"radio\" ".fe_getAttributeString($elementDef['attributes'])." />";
	//multiple radio options
	$arr=array();
	foreach($elementDef['options'] as $k=>$v){
		$arr[] = "<input type=\"radio\" ".fe_getAttributeString($elementDef['attributes'])." value=\"{$k}\"/>&nbsp;&nbsp;{$v}";
	}
	$str = implode($elementDef['separator'],$arr);
	return $str;
}

////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

//dropdown menus
//'value' : key of the default option
//'options' : associative array (key=>value) of options
function fe_getSelectHTML($elementDef){
	if(!(isset($elementDef['options']) && is_array($elementDef['options']))) return "";	
	$default = (isset($elementDef['value'])?$elementDef['value']:"");
	$str="<select ".fe_getAttributeString($elementDef['attributes'])." >";
	foreach($elementDef['options'] as $k=>$v){
		if($k==$default) 	$str.="<option value=\"{$k}\" selected=\"selected\">{$v}</option>";
		else				$str.="<option value=\"{$k}\">{$v}</option>";
	}
	$str.="</select>";	
	return $str;
}


////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

//an <input ... >

function fe_getInputHTML($elementDef){				
	return "<input type=\"".$elementDef['type']."\" ".fe_getAttributeString($elementDef['attributes'])." />";
}

////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

// checkbox
function fe_getCheckboxHTML($elementDef){
	return "<input type=\"checkbox\" ".fe_getAttributeString($elementDef['attributes'])." ".($elementDef['checked']?"checked":"")."/>";
}

// checkbox lists
function fe_getCheckboxListHTML($elementDef){
	if(!(isset($elementDef['options']) && is_array($elementDef['options']))) return "";
	$arr=array();
	foreach($elementDef['options'] as $k=>$v){
		$arr[] = "<input type=\"checkbox\" ".fe_getAttributeString($elementDef['attributes'])." id=\"{$k}\" name=\"{$k}\"/>&nbsp;&nbsp;{$v}";
	}
	$str = implode($elementDef['separator'],$arr);
	return $str;
}

////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////

//helper function to compile an 'attribute' string from an associative array
function fe_getAttributeString($atts){
	if(!is_array($atts)) return "";
	$arr = array();
	foreach($atts as $k=>$v)
		$arr[]= "{$k}=\"{$v}\"";
	return implode(" ",$arr);
}

function fe_getElementHTML($elementDef){
	switch($elementDef['type']){
		case 'radio': return fe_getRadioHTML($elementDef);
		case 'select': return fe_getSelectHTML($elementDef);
		case 'textarea': return fe_getTextareaHTML($elementDef);
		case 'checkbox': return fe_getCheckboxHTML($elementDef);
		case 'checkbox_list': return fe_getCheckboxListHTML($elementDef);
		case 'text':		
		case 'button':
		case 'submit':
		default: return fe_getInputHTML($elementDef);
	}
}

?>