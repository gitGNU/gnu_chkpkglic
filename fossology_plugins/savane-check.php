<?php
/***********************************************************
 Copyright (C) 2009 Federico Gimenez Nieto - fgimenez@coit.es

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License along
 with this program; if not, write to the Free Software Foundation, Inc.,
 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 ***********************************************************/

/*************************************************
 Restrict usage: Every PHP file should have this
 at the very beginning.
 This supposedly prevents cracking attempts.
 *************************************************/
global $GlobalReady;
if (!isset($GlobalReady)) { exit; }

class savane_check extends FO_Plugin
{
  public $Name       = "savane_check";
  public $Title      = "Check package for Savannah inclusion";
  public $MenuList   = "Savane::Check";
  public $Version    = "0.1";
  public $Dependency = array("db");
  public $DBaccess   = PLUGIN_DB_ANALYZE;

  /*
    DoSearchModel($Packagename): searchs on the database for a packagename
  */
  function DoSearchModel($Packagename, $DB){
    $SQL="SELECT 
            uploadtree_pk, upload_pk, upload_filename 
          FROM 
            upload                     
          INNER JOIN 
            uploadtree 
            ON 
              upload_fk=upload_pk 
              AND parent IS NULL 
          WHERE 
            upload_filename LIKE '%$Packagename%';";

    return $DB->Action($SQL);
  }

  /*
    SearchResultsView($Results): returns html for the searched results with links
  */
  function SearchResultsView($Results,$search_string){
    if(count($Results)==0)
      $output="<p>Sorry, no entries found with <i>$search_string</i>!</p>";

    else{
      $output="<p>Results found with <i>$search_string</i></p>";
      foreach($Results as $result)
        $output.="<p><strong>".$result['upload_filename']."</strong></p>";
    }

    return $output;
  }

  /*
    CheckResultView($Packageid): 
  */
  function CheckResultView($Packageid){

  }

  /*
    FormView(): returns the html code for the search form
  */
  function FormView($Packagename=''){
    $V = "";
    $Uri = preg_replace("/&packagename=[^&]*/","",Traceback());

    $V .= "<hr/><form action='$Uri' method='POST'>\n";
    $V .= "<ul>\n";
    $V .= "<li>Enter the string to search for:<P>";
    $V .= "<INPUT type='text' name='packagename' size='40' value='" . htmlentities ($Packagename) . "'>\n";
    $V .= "</ul>\n";
    $V .= "<input type='submit' value='Search!'>\n";
    $V .= "</form>\n";

    return $V;
  }
  

  /*********************************************
   Output(): Generate the text for this plugin.
   *********************************************/
  function Output()
  {
    if ($this->State != PLUGIN_STATE_READY) { return; }
    global $DB;
    $V="";
    switch($this->OutputType)
    {
      case "XML":
        break;
      case "HTML":        
	#Controller code
	#Extract params
	$Packagename = GetParm("packagename",PARM_STRING);
	$Packageid = GetParm("packageid",PARM_STRING);

	if(!empty($Packagename))
	  #Do search and show results
	  $V .= $this->SearchResultsView($this->DoSearchModel($Packagename, $DB),$Packagename);

        else if(!empty($Packageid))
          #Show check result for this package		       
	  $V .= $this->CheckResultView($Packageid);

	$V .= $this->FormView($Packagename);
	
        break;
      case "Text":
        break;
      default:
        break;
    }
    if (!$this->OutputToStdout) { return($V); }
    print("$V");
    return;
  }
};
$NewPlugin = new savane_check;
$NewPlugin->Initialize();
