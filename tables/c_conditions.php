<?php
function read_directory($directory) {
	$the_ret = array();
	$dirhandler = opendir($directory);
	$i=0;
	while ($file = readdir($dirhandler)) {
		if ($file != '.' && $file != '..') {
			$i++;
			$the_ret[$i]=$file;                
		}   
	}
    closedir($dirhandler);
	return $the_ret;
	}

$theIcons = array();
$theDirectory = getcwd().'/rm/roadinfo_icons/';
$theIcons = read_directory($theDirectory); 
?>
		<FORM NAME="c" METHOD="post" ACTION="<?php print $_SERVER['PHP_SELF']; ?>" /><!-- 1/21/09 - APRS moved to responder schema  -->
		<INPUT TYPE="hidden" NAME="func" 		VALUE="pc"/>
		<INPUT TYPE="hidden" NAME="tablename" 	VALUE="<?php print $tablename;?>" />
		<INPUT TYPE="hidden" NAME="indexname" 	VALUE="id" />
		<INPUT TYPE="hidden" NAME="sortby" 		VALUE="id" />
		<INPUT TYPE="hidden" NAME="sortdir"		VALUE=0 />
		<INPUT TYPE="hidden" NAME="frm__by" 	VALUE="<?php print $_SESSION['user_id']; ?>" />
		<INPUT TYPE="hidden" NAME="frm__from" 	VALUE="<?php print $_SERVER['REMOTE_ADDR']; ?>" />
		<INPUT TYPE="hidden" NAME="frm__on" 	VALUE="<?php print mysql_format_date(time() - (get_variable('delta_mins')*60));?>" />
		<INPUT TYPE="hidden" NAME="frm_icon" 	VALUE="" />
	
		<TABLE BORDER="0" ALIGN="center">
		<TR CLASS="even" VALIGN="top"><TD COLSPAN="2" ALIGN="CENTER"><FONT SIZE="+1">Table 'Conditions' - Add New Entry</FONT></TD></TR>
		<TR><TD>&nbsp;</TD></TR>
	<TR VALIGN="baseline" CLASS="odd"><TD CLASS="td_label" ALIGN="right">Type name:</TD>
		<TD><INPUT ID="ID1" CLASS="dirty" MAXLENGTH="16" SIZE="16" type="text" NAME="frm_title" VALUE="" onFocus="JSfnChangeClass(this.id, 'dirty');" onChange = "this.value=JSfnTrim(this.value)"> <SPAN class='warn' >text</SPAN></TD></TR>
	<TR VALIGN="baseline" CLASS="even"><TD CLASS="td_label" ALIGN="right">Description:</TD>
		<TD><INPUT ID="ID2" CLASS="dirty" MAXLENGTH="48" SIZE="48" type="text" NAME="frm_description" VALUE="" onFocus="JSfnChangeClass(this.id, 'dirty');" onChange = "this.value=JSfnTrim(this.value)"> <SPAN class='warn' >text</SPAN></TD></TR>
	<TR VALIGN="top" CLASS="odd"><TD CLASS="td_label" ALIGN="right">Icon:</TD>
		<TD><IMG ID='ID3' SRC="theIcons[0]" STYLE="visibility:hidden;"></TD></TR>
	<TR CLASS="even"><TD></TD><TD ALIGN='center'>&nbsp;&nbsp;&nbsp;&nbsp;
<SCRIPT>
	var theIcons = new Array();
<?php
	$z = 0;
	foreach($theIcons AS $val) {
		print "\ttheIcons[" . $z . "] = '" . $val . "'\n";	
		$z++;
		}
?>
	var the_dir = "./rm/roadinfo_icons/";	
	
	function theicon_to_form(the_icon) {						// 12/31/08
		var the_img = $('ID3');
		document.forms[1].frm_icon.value=the_icon;			// icon index to form variable
		$('ID3').src = the_dir + the_icon;
		$('ID3').style.visibility = "visible";				// initially hidden for 'create'
		return;
		}				
	
	function gen_the_img(the_icon) {						// returns image string for nth icon
		var the_sm_image = the_icon;
		var the_image = the_dir + the_icon;
		var the_title = the_icon;	// extract color name
		return "<IMG SRC='" + the_image + "' onClick  = 'theicon_to_form(\"" + the_sm_image + "\")' TITLE='" + the_title +"' />";
		}

			for (i=0; i<theIcons.length-1; i++) {						// generate icons display
				document.write(gen_the_img(theIcons[i])+"&nbsp;&nbsp;\n");
				}
</SCRIPT>
			 	&laquo; <SPAN class='warn'>click to select icon </SPAN> &nbsp;

		</TD></TR>
		<tr><td colspan=99 align='center'>
		</td></tr
		<TR><TD COLSPAN="99" ALIGN="center">
		<BR />
		<INPUT TYPE="button"				VALUE="Cancel" onClick = "Javascript: document.retform.func.value='r';document.retform.submit();"/>&nbsp;&nbsp;&nbsp;&nbsp;
		<INPUT TYPE="button"				VALUE="Reset" onClick = "Javascript: $('ID3').style.visibility='hidden'; document.c.frm_icon.value = ''; document.c.reset();" />&nbsp;&nbsp;&nbsp;&nbsp;
		<INPUT TYPE="button" NAME="sub_but" VALUE="               Submit                " onclick="this.disabled=true; JSfnCheckInput(this.form, this );"/> 
		</TD></TR>
		</FORM>
		</TD></TR></TABLE>

<?php
