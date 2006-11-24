<?php
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) 2004 Dokeos S.A.
	Copyright (c) 2003 University of Ghent (UGent)
	Copyright (c) 2001 Universite catholique de Louvain (UCL)
	Copyright (c) Olivier Brouckaert

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact: Dokeos, 181 rue Royale, B-1000 Brussels, Belgium, info@dokeos.com
==============================================================================
*/
/**
============================================================================== 
	@author Bart Mollet
*	@package dokeos.admin
============================================================================== 
*/
/*
==============================================================================
		INIT SECTION
==============================================================================
*/
$langFile = 'survey';

require ('../inc/global.inc.php');
require_once (api_get_path(LIBRARY_PATH)."/surveymanager.lib.php");
$status = surveymanager::get_status();
if($status==5)
{
api_protect_admin_script();
}
//api_protect_admin_script();
require_once (api_get_path(LIBRARY_PATH)."/course.lib.php");
$cidReq = $_REQUEST['cidReq'];
$curr_dbname = $_REQUEST['curr_dbname'];
$table_survey = Database :: get_course_table('survey');
$table_group =  Database :: get_course_table('survey_group');
$table_question = Database :: get_course_table('questions');
$table_course_survey_rel = Database :: get_main_table(TABLE_MAIN_COURSE_SURVEY);
//$interbredcrump[] = array ("url" => "index.php", "name" => get_lang('AdministrationTools'));
$interbredcrump[] = array ("url" => "survey_list.php", "name" => get_lang('Survey'));
//$interbredcrump[] = array ("url" => "survey.php", "name" => get_lang('a_survey'));
$n='e';
$tool_name = get_lang('ImportFromExisting');
$tool_name1 = get_lang('SurveysOfAllCourses');
$surveyid=$_GET['surveyid'];
$sname = surveymanager::get_surveyname_display($surveyid);
Display :: display_header($tool_name);
api_display_tool_title($tool_name1);
?>
<SCRIPT LANGUAGE="JavaScript">
function displayTemplate(url) {
	window.open(url, 'popup', 'width=600,height=600,toolbar = no, status = no');
}
</script>
<table>
<tr>
<td>
<?echo get_lang('SurveyName');?>&nbsp;<?echo $sname;?>
</td>
</tr>
<tr>
<td>

</td>
</tr>
</table>		
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>?cidReq=<?php echo $cidReq; ?>">
<input type="hidden" name="action" value="add_survey">
<input type="hidden" name="surveyid" value="<?php echo $surveyid; ?>">
<?php 	
   		$surveyid=$_REQUEST['surveyid'];
		$nameTools=get_lang('CreateFromExistingSurveys');
		$table_group = Database :: get_course_table('survey_group');
		$sql = "SELECT * FROM $table_course_survey_rel";
		$parameters = array ();
		$parameters['surveyid']=$surveyid;
		$parameters['newgroupid']=$groupid;
		$parameters['cidReq']=$cidReq;
		$parameters['curr_dbname']=$curr_dbname;
		$res = api_sql_query($sql,__FILE__,__LINE__);
	if (mysql_num_rows($res) > 0)
	{		
		$surveys = array ();
		while ($obj = mysql_fetch_object($res))
		{
			$db_name = $obj->db_name;
			$course_name = $obj->course_code;
			$survey_id = $obj->survey_id;
			if($survey_id==$surveyid&&$course_name==$cidReq)
			{continue;}
			$sql_survey = "SELECT * FROM $db_name.survey WHERE survey_id = '$survey_id' AND is_shared='1'";
			$res_survey = api_sql_query($sql_survey,__FILE__,__LINE__);
			$survey = array ();
			while($object=mysql_fetch_object($res_survey))
			{
				//$survey[] = '<input type="checkbox" name="course[]" value="'.$obj->group_id.'">';
				$survey[] = $object->title;
				//$surveyid = $object->survey_id;
				//$groupid=$obj->group_id;
				//$surveyid=surveymanager::get_surveyid($groupid);
				$authorid=surveymanager::get_author($db_name,$survey_id);
				$author=surveymanager::get_survey_author($authorid);
				//$NoOfQuestion=surveymanager::no_of_question($groupid);
				$survey[] = $author;
				$survey[] = $course_name;
				$survey[] = $object->lang;
				$survey[] = $object->avail_from ;
				$survey[] = $object->avail_till ;	
				$survey[] = "<a href=group_list.php?cidReq=$cidReq&sid=$survey_id&db_name=$db_name&surveyid=$surveyid&curr_dbname=$curr_dbname><img src=\"../img/info_small.gif\" border=\"0\" align=\"absmiddle\" alt=view></a>";
				$surveys[] = $survey;				
			}
        }
		$table_header[] = array (get_lang('SurveyName1'), true);
		$table_header[] = array (get_lang('author'), true);
		$table_header[] = array (get_lang('CourseName'), true);
		$table_header[] = array (get_lang('Language'), true);
		$table_header[] = array (get_lang('AvailableFrom'), true);
		$table_header[] = array (get_lang('AvailableTill'), true);		
		$table_header[] = array (' ', false);
		if(!empty($surveys))
		{
		Display :: display_sortable_table($table_header, $surveys, array (), array (), $parameters);
		}
		else
		{$flag=1;}
		?>		
		</form>
<?	
    }
	else
	{
		echo get_lang('NoSearchResults');
	}
	if($flag=='1')
	{echo get_lang('SurveyNotShared');}
?>
<form action="select_question_group.php?cidReq=<?php echo $cidReq; ?>&db_name=<?php echo $db_name; ?>&surveyid=<?php echo $surveyid; ?>&curr_dbname=<?php echo $curr_dbname; ?>" method="post">
<input type="submit" name="back1" value="<?php echo get_lang('Back'); ?>">
</form>
<?
Display :: display_footer();
?> 