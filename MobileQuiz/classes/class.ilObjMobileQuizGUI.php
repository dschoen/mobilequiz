<?php
/*
 +-----------------------------------------------------------------------------+
| MobileQuiz open source                                                      |
+-----------------------------------------------------------------------------+
| Copyright 2011 Stephan Schulz                                               |
|                                                                             |
| MobileQuiz is free software: you can redistribute it and/or modify          |
| it under the terms of the GNU General Public License as published by        |
| the Free Software Foundation, either version 3 of the License, or           |
| (at your option) any later version.                                         |
|                                                                             |
|                                                                             |
| MobileQuiz is distributed in the hope that it will be useful,               |
| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the                |
| GNU General Public License for more details.                                |
|                                                                             |
| You should have received a copy of the GNU General Public License           |
| along with MobileQuiz.  If not, see <http://www.gnu.org/licenses/>.         |
+-----------------------------------------------------------------------------+
*/


include_once("./Services/Repository/classes/class.ilObjectPluginGUI.php");
include_once("./Services/jQuery/classes/class.iljQueryUtil.php");
//include_once("./classes/class.ilCtrl.php");
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/classes/class.ilObjMobileQuizHelper.php");
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/configuration.php");

/**
 * User Interface class for MobileQuiz repository object.
 *
 * User interface classes process GET and POST parameter and call
 * application classes to fulfill certain tasks.
 *
 * @author Stephan Schulz
 * @author Daniel Schoen <schoen@uni-mannheim.de>
 *
 * $Id$
 *
 * Integration into control structure:
 * - The GUI class is called by ilRepositoryGUI
 * - GUI classes used by this class are ilPermissionGUI (provides the rbac
 *   screens) and ilInfoScreenGUI (handles the info screen).
 *
 * @ilCtrl_isCalledBy ilObjMobileQuizGUI: ilRepositoryGUI, ilAdministrationGUI, ilObjPluginDispatchGUI, ilCommonActionDispatcherGUI
 * @ilCtrl_Calls ilObjMobileQuizGUI: ilPermissionGUI, ilInfoScreenGUI, ilObjectCopyGUI, ilCommonActionDispatcherGUI
 *
 */
class ilObjMobileQuizGUI extends ilObjectPluginGUI{

    /**
     * Initialisation
     */
    protected function afterConstructor(){
        // anything needed after object has been constructed
        // - MobileQuiz: append my_id GET parameter to each request
        //   $ilCtrl->saveParameter($this, array("my_id"));
    }

    /**
     * Get type.
     */
    final function getType(){
        return "xuiz";
    }

    /**
     * Handles all commmands of this class, centralizes permission checks
     */
    function performCommand($cmd){
        switch ($cmd){
            // list all commands that need write permission here
            case "editProperties":		
            case "editQuiz":
            case "getPropertiesValues":
            case "updateProperties":
            case "initPropertiesForm":
            case "addQuestion":
            case "initAddQuestionForm":
            case "showQuestions":
            case "initQuestionTable":
            case "createQuestion":
            case "deleteQuestion":
            case "editQuestion":
            case "initQuestionEditForm":
            case "changeQuestion":
            case "addChoice":
            case "initAddChoiceForm":
            case "createChoice":
            case "deleteChoice":
            case "showChoices":
            case "initChoicesTable":
            case "editChoice":
            case "initChoiceForm":
            case "changeChoice":
            case "endCurrentRound":
            case "beginCurrentRound":
            case "info":

            case "addQuestionAndAnswers":
            case "initAddQuestionAndAnswersForm":
            case "createQuestionAndAnswers":
            case "showQuestionAndAnswers":
            case "initQuestionAndAnswersTable":
            case "deleteQuestionAndAnswers":
            case "editQuestionAndAnswers":
            case "initQuestionAndAnswersEditForm":
            case "changeQuestionAndAnswers":
            case "switchUp":
            case "switchDown":		

            case "showResults":
            case "initResultsTable":
            case "showRoundResults":
            case "deleteRound":
            case "changeRoundStatus":
            case "exportResultData":

                $this->checkPermission("write");
                $this->$cmd();
                break;

            case "showCurrentRound":			
                // list all commands that need read permission here
                
                //no commands so far
                
                $this->checkPermission("read");
                $this->$cmd();
                break;
        }
    }

    /**
     * After object has been created -> jump to this command
     */
    function getAfterCreationCmd(){
        return "editProperties";
    }

    /**
     * Get standard command
     */
    function getStandardCmd(){
        return "showCurrentRound";
    }

    /**
     * Set tabs. Here all the tabs have to be listed in the order that has to be displayed.
     */
    function setTabs(){
        global $ilTabs, $ilCtrl, $ilAccess, $ilLocator;

        // show current quiz round inlcuding link and QR code (deadline)
        if ($ilAccess->checkAccess("read", "", $this->object->getRefId())){
            $ilTabs->addTab("showCurrentRound", $this->txt("tabmenu_showCurrentRound"), $ilCtrl->getLinkTarget($this, "showCurrentRound"));
        }

        // tab for the "edit quiz" command
        if ($ilAccess->checkAccess("write", "", $this->object->getRefId())){
            $ilTabs->addTab("editQuiz", $this->txt("tabmenu_edit_quiz"), $ilCtrl->getLinkTarget($this, "editQuiz"));
        }

        // show round results
        if ($ilAccess->checkAccess("write", "", $this->object->getRefId())){
            $ilTabs->addTab("showResults", $this->txt("tabmenu_show_result"), $ilCtrl->getLinkTarget($this, "showResults"));
        }

        // a "properties" tab
        if ($ilAccess->checkAccess("write", "", $this->object->getRefId())){
            $ilTabs->addTab("properties", $this->txt("tabmenu_properties"), $ilCtrl->getLinkTarget($this, "editProperties"));
            $this->addPermissionTab();
        }

        // information Tab
        if ($ilAccess->checkAccess("write", "", $this->object->getRefId())){
            $ilTabs->addTab("info", $this->txt("tabmenu_info"), $ilCtrl->getLinkTarget($this, "info"));
        }

    }

    //--------------------------------------------------------------------------
    //                            Properties
    //--------------------------------------------------------------------------

    /**
     * Edit Properties
     */
    function editProperties(){
        global $tpl, $ilTabs;

        $ilTabs->activateTab("properties");
        $this->initPropertiesForm();
        $this->getPropertiesValues();
        $tpl->setContent($this->form->getHTML());
    }

    /**
     * Get Properties Values
     */
    function getPropertiesValues(){
        $values["title"] = $this->object->getTitle();
        $values["description"] = $this->object->getDescription();
        $this->form->setValuesByArray($values);
    }

    /**
     * Update Properties
     */
    public function updateProperties(){
        global $tpl, $lng, $ilCtrl;

        $this->initPropertiesForm();
        if ($this->form->checkInput()){
            $this->object->setTitle($this->form->getInput("title"));
            $this->object->setDescription($this->form->getInput("description"));
            $this->object->update();
            ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
            $ilCtrl->redirect($this, "editProperties");
        }
        $this->form->setValuesByPost();
        $tpl->setContent($this->form->getHtml());
    }

    /**
     * Init Properties Form;
     */
    public function initPropertiesForm(){
        global $ilCtrl;

        include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
        $this->form = new ilPropertyFormGUI();

        // title of the quiz
        $title = new ilTextInputGUI($this->txt("properties_title"), "title");
        $title->setRequired(true);
        $this->form->addItem($title);

        //description of the quiz
        $description = new ilTextAreaInputGUI($this->txt("properties_description"), "description");
        $description->setRequired(f);
        $this->form->addItem($description);

        $this->form->addCommandButton("updateProperties", $this->txt("save"));
        $this->form->setTitle($this->txt("edit_properties"));
        $this->form->setFormAction($ilCtrl->getFormAction($this));
    }


    //-------------------------------------------------------------------------
    //                            Info
    //-------------------------------------------------------------------------

    /**
     * Open Tab with information about the application.
     */
    function info(){
        global $tpl, $ilTabs;

        $ilTabs->activateTab("info");

        $my_tpl = new ilTemplate("tpl.info.html", true, true,
            "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");

        // info text
        $my_tpl->setVariable("INFO_TEXT", $this->txt("info_text"));
        // mail to
        $my_tpl->setVariable("MAIL_TO", $this->txt("info_mailto"));
        // Instructions
        $my_tpl->setVariable("INSTRUCTIONS",$this->txt("info_instructions"));

        $html = $my_tpl->get();

        $tpl->setContent($html);
    }

    //--------------------------------------------------------------------------


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////// Rounds    ///////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * End current round
     */
    public function endCurrentRound(){
        global $tpl, $lng, $ilCtrl, $ilTabs;

        $ilTabs->activateTab("showCurrentRound");

        $this->object->endCurrentRound();
        $ilCtrl->redirect($this, "showCurrentRound");
    }

    /**
     * Begin current round
     */
    public function beginCurrentRound(){
        global $tpl, $lng, $ilCtrl, $ilTabs;

        $ilTabs->activateTab("showCurrentRound");

        $this->object->beginCurrentRound();
        $ilCtrl->redirect($this, "showCurrentRound");
    }


    // Get the Short URL for a Quiz
    function getShortURL($quiz_id, $round_id) {

        global $ilDB, $ilUser;

        $ilDB->setLimit(1);
        $set = $ilDB->query("
                   			  SELECT tiny_url
                   	          FROM rep_robj_xuiz_rounds
                   	          WHERE round_id = ".$ilDB->quote($round_id, "integer")." ORDER BY round_id DESC");

        while ($rec = $ilDB->fetchAssoc($set)){
            if ( !empty( $rec["tiny_url"] ) ) return $rec["tiny_url"]; else {
                // get hostname and check if proxy is used
				$hostname = (!empty($_SERVER['HTTP_X_FORWARDED_HOST'])) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['SERVER_NAME'];
                $url = (!empty($_SERVER['HTTPS'])) ? "https://".$hostname.$_SERVER['REQUEST_URI'] : "http://".$hostname.$_SERVER['REQUEST_URI'];

                // please change this if you move your frontend installation out of your ILIAS plugin directory:
                $frontend_url = "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/frontend/";
                // TODO: Workaround for shorten

                // crafting quiz url:
                $tmp = explode('/',$url);
                $dmy = array_pop($tmp);
                $server_url = (implode('/',$tmp) . '/');
                $quiz_url = $server_url.$frontend_url."index.php?quiz_id=".$quiz_id."&round_id=".$round_id;
                //$quiz_url = $server_url.$frontend_url."quiz_id=".$quiz_id."&round_id=".$round_id;
                // TODO: Workaround for shorten

                // short url
                if (SHORTENER) {
                    include_once('class.ilObjMobileQuizUrlShorter.php');
                    $url_shorter = new ilObjMobileQuizUrlShorter();
                    $shorted_url = $url_shorter->shortURL($quiz_url);
               
                }
                //If shortner is false or does not function properly then make the URL optional.
                if($shorted_url == NULL)
                    {
                  //short url
                  $shorted_url = $quiz_url;
              }

                $ilDB->manipulate("UPDATE rep_robj_xuiz_rounds SET tiny_url = '".$shorted_url."' WHERE ".
                                " round_id = ".$ilDB->quote($round_id, "integer")
                        );

                return $shorted_url;
            }

        }
    }

    /**
     * Show Current Round. This displays the link to the quiz page and its the QR Code.
     */
    function showCurrentRound(){
        global $ilLocator, $tpl, $ilTabs,$ilAccess;
        $ilTabs->activateTab("showCurrentRound");

        $quiz_id = $this->object->getId();
        $currentRound = $this->object->getCurrentRound($quiz_id);
        $round_id = $currentRound[round_id];

        // does not have end_date --> current round still running
        if ($currentRound && !$currentRound['end_date']){

            $action_edit = $this->ctrl->getLinkTarget($this,'endCurrentRound');
            $action_edit = $this->ctrl->appendRequestTokenParameterString($action_edit);

            $shorted_url = $this->getShortURL($quiz_id, $round_id);

            $my_tpl = new ilTemplate("tpl.startpage_stop.html", true, true,
                "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");

            // link to stop a new round
            if ($ilAccess->checkAccess("write", "", $this->object->getRefId())){
                $my_tpl->setVariable("STOP_LINK",'<a href="'.$action_edit.'" class="stop_button">'.$this->txt("round_stop").'</a><br> <br />');
            }

            $my_tpl->setVariable("IMAGE_URL",$server_url.ilUtil::getWebspaceDir()."/MobileQuiz_data/".$round_id."/qrcode.png");
            $my_tpl->setVariable("QUIZ_URL",$shorted_url);

            $html = $my_tpl->get();

            $this->ctrl->clearParameters($this);
        }
        //no currently running round
        else{

            $action_edit = $this->ctrl->getLinkTarget($this,'beginCurrentRound');
            $action_edit = $this->ctrl->appendRequestTokenParameterString($action_edit);

            $my_tpl = new ilTemplate("tpl.startpage_start.html", true, true,
                "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");

            // link to start a new round
            if ($ilAccess->checkAccess("write", "", $this->object->getRefId())){
                $my_tpl->setVariable("START_LINK",'<a href="'.$action_edit.'" class="start_button">'.$this->txt("round_start").'</a>');
            }
            // info text
            $my_tpl->setVariable("INFO_TEXT",$this->txt("round_start_info"));

            $html = $my_tpl->get();

            $this->ctrl->clearParameters($this);
        }

        $tpl->setContent($html);
    }

    /**
     * Delete Round
     */
    public function deleteRound(){
        global $ilUser, $tpl, $ilTabs, $ilCtrl;
        $ilTabs->activateTab("showResults");

        if(isset($_GET['round_id']) && is_numeric($_GET['round_id'])){
            $round_id = $_GET['round_id'];

            $this->object->deleteRound($round_id);
            $ilTabs->activateTab("showResults");

            ilUtil::sendSuccess($this->txt("successful_delete"), true);
            $ilCtrl->redirect($this, "showResults");
        }
    }

    /**
     * Change status of round
     */
    public function changeRoundStatus(){
        global $ilUser, $tpl, $ilTabs, $ilCtrl;
        $ilTabs->activateTab("showResults");

        if(isset($_GET['round_id']) && is_numeric($_GET['round_id'])){
            $round_id = $_GET['round_id'];

            $this->object->changeRoundStatus($round_id);
            $ilTabs->activateTab("showResults");


            ilUtil::sendSuccess($this->txt("results_round_status_change_success"), true);
            $ilCtrl->redirect($this, "showResults");
        }
    }


///////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////  RESULTS - EXPORT ///////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Is called when the "Export Result" Button is pressed.
     * The function calles the Export Function.
     */
    public function exportResultData() {

        $this->exportResults();
        $this->ctrl->redirect($this, 'showResult');
    }

    function exportResults() {
        $format_bold = "";
        $format_percent = "";
        $format_datetime = "";
        $format_title = "";
        // 	$surveyname = ilUtil::getASCIIFilename(preg_replace("/\s/", "_", $this->object->getTitle()));
        $surveyname = "mobilequiz";

        include_once "./Services/Excel/classes/class.ilExcelWriterAdapter.php";
        $excelfile = ilUtil::ilTempnam();
        $adapter = new ilExcelWriterAdapter($excelfile, FALSE);
        $workbook = $adapter->getWorkbook();
        $workbook->setVersion(8); // Use Excel97/2000 Format
        // Creating a worksheet
        $format_bold =& $workbook->addFormat();
        $format_bold->setBold();
        $format_percent =& $workbook->addFormat();
        $format_percent->setNumFormat("0.00%");
        $format_datetime =& $workbook->addFormat();
        $format_datetime->setNumFormat("DD/MM/YYYY hh:mm:ss");
        $format_title =& $workbook->addFormat();
        $format_title->setBold();
        $format_title->setColor('black');
        $format_title->setPattern(1);
        $format_title->setFgColor('silver');
        $format_title->setAlign('center');

        // Creating a worksheet
        include_once ("./Services/Excel/classes/class.ilExcelUtils.php");

        // Get rounds from the Database
        $rounds = $this->object->getRounds();

        if(!count($rounds) == 0) {
            foreach($rounds as $round){

                $mainworksheet =& $workbook->addWorksheet("Runde ".$round['round_id']);
                $column = 0;
                $row = 0;

                // write header
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Frage", "excel", $format_bold));
                $column++;
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Fragentyp", "excel", $format_bold));
                $column++;
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Antwort", "excel", $format_bold));
                $column++;
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Antworttyp", "excel", $format_bold));
                $column++;
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Anzahl", "excel", $format_bold));

                $round_id = $round['round_id'];
                $answers = $this->object->getAnswers($round_id);
                $questions = $this->object->getQuestions($this->object->getId());

                if(!count($questions) == 0) {
                    foreach ($questions as $question){

                        $choices = $this->object->getChoices($question['question_id']);

                        switch ($question['type']) {
                            case QUESTION_TYPE_SINGLE :
                            case QUESTION_TYPE_MULTI:
                                if(!count($choices) == 0) {
                                    foreach($choices as $choice){
                                        $count = 0;

                                        foreach ($answers as $answer){
                                            if (($answer['choice_id'] == $choice['choice_id'])&&($answer['value'] != 0)){
                                                $count++;
                                            }
                                        }
                                        // write into sheet
                                        $column = 0;
                                        $row++;
                                        $mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($question['text'], "excel", $format_bold));
                                        $column++;
                                        $mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($question['type'], "excel", $format_bold));
                                        $column++;
                                        $mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($choice['text'], "excel", $format_bold));
                                        $column++;
                                        $mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($choice['correct_value'], "excel", $format_bold));
                                        $column++;
                                        $mainworksheet->writeNumber($row, $column, ilExcelUtils::_convert_text($count, "excel", 0));
                                    }
                                }
                                break;
                            case ilObjMobileQuizConstants::QUESTION_TYPE_NUM:
                                if(!count($choices) == 0) {
                                    foreach($choices as $choice){ // Theres always only one Choice with numeric questions
                                        foreach ($answers as $answer){
                                            // write into sheet
                                            $column = 0;
                                            $row++;
                                            $mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($question['text'], "excel", $format_bold));
                                            $column++;
                                            $mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($question['type'], "excel", $format_bold));
                                            $column++;
                                            $mainworksheet->writeNumber($row, $column, ilExcelUtils::_convert_text($answer['value'], "excel", 0));
                                            $column++;
                                            $mainworksheet->writeNumber($row, $column, ilExcelUtils::_convert_text($choice['correct_value'], "excel", 0));
                                        }
                                    }
                                }
                                break;
                        }
                        // write empty line after question
                        $row++;
                    }
                }
            }
        }
        // Let's send the file
        $workbook->close();
        ilUtil::deliverFile($excelfile, "$surveyname.xls", "application/vnd.ms-excel");
        exit();
    }

///////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////  END - RESULTS - EXPORT /////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////


    //--------------------------------------------------------------------------
    //                         RESULTS - SHOW
    //--------------------------------------------------------------------------

    /**
     * Show results
     */
    function showResults(){
        global $ilUser, $tpl, $ilTabs, $ilLocator, $ilToolbar;

        $ilTabs->activateTab("showResults");

        $ilToolbar->setFormAction($this->ctrl->getFormAction($this));
        $ilToolbar->addFormButton($this->txt("results_export"), 'exportResultData');

        $tpl->setContent($this->initResultsTable());
    }

    //--------------------------------------------------------------------------
    
    /**
     * Init Results Table
     */
    public function initResultsTable(){
        global $ilAccess, $ilUser, $ilDB;
        include_once('./Services/Table/classes/class.ilTable2GUI.php');
        $tbl = new ilTable2GUI($this);

        $tbl->setId('ResultsTable');

        $tbl->setRowTemplate('tpl.rounds_row.html', 'Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz');
        $tbl->setTitle($this->txt("results_table_head"));
        $tbl->setLimit($_GET[$tbl->getId()."_trows"], 20);

        $tbl->setOrderColumn('text');

        $tbl->addColumn($this->txt("results_round"), 'text', '25%');
        $tbl->addColumn($this->txt("results_no_of_answers"), 'type', '15%');
        $tbl->addColumn($this->txt("results_start_date"), 'text', '20%');
        $tbl->addColumn($this->txt("results_status"), 'text', '15%');
        $tbl->addColumn($this->txt("results_round_options"), 'text', '25%');

        // Get the rounds from the database
        $rounds = $this->object->getRounds();
        $result = array();

        // use $round for displaying to get rounds starting from 1 and not to use round_id
        $round_name = count($rounds);

        if(!count($rounds) == 0) {
            foreach($rounds as $round){
                $round["quiz_url"] = $this->getShortURL($round['quiz_id'], $round['round_id']);
                $round["image_url"] = ilUtil::getWebspaceDir()."/MobileQuiz_data/".$round['round_id']."/qrcode.png";
                $round["show_qr"] = $this->txt("results_show_qr");

                $this->ctrl->setParameter($this,'round_id',$round['round_id']);
                $action_status = $this->ctrl->getLinkTarget($this,'changeRoundStatus');
                $action_status = $this->ctrl->appendRequestTokenParameterString($action_status);
                $round["status"] = '<select class="change-status" onchange="javascript:if ( $(this).val() != \'no-follow\' ) window.location.href = $(this).val();"><option value="no-follow">'.$this->txt("results_status_inactive").'</option>';

                $round["change_status"] = ''.$this->txt("results_status_passive")."</option><option value='".$action_status."&activate=true'>".$this->txt("results_status_activate")."</option>";
                $action_show_round_results = $this->ctrl->getLinkTarget($this,'showRoundResults');
                $action_show_round_results = $this->ctrl->appendRequestTokenParameterString($action_show_round_results);
                $round['show_round_results_href'] = "<a href=".$action_show_round_results.">";

                $round["answer_count"] = count($this->object->getDistinctAnswers($round['round_id']));

                // delete link
                $this->ctrl->setParameter($this,'round_id',$round['round_id']);
                $action_delete = $this->ctrl->getLinkTarget($this,'deleteRound');
                $action_delete = $this->ctrl->appendRequestTokenParameterString($action_delete);
                $round["delete_round_href"] = "<a href=".$action_delete.">";
                $round["delete_round_txt"] = $this->txt("results_round_delete");

                // date format depends on the user's language
                $user_language = $ilUser->getLanguage();
                $endDate = strtotime($round["end_date"]);
                if($user_language == 'de') {
                    // for german user dd.MM.YYYY
                    $round["date"] = date("d.m.Y",strtotime($round["start_date"]));
                    $round["enddate"] = date("d.m.Y",strtotime($round["end_date"]));
                } else {
                    // for all other users MM.dd.YYYY
                    $round["date"] = date("m.d.Y",strtotime($round["start_date"]));
                    $round["enddate"] = date("m.d.Y",strtotime($round["end_date"]));
                }

                $round["round_name"] = $this->txt("results_round")." ".$this->txt("results_round_on")." ".
                    $round["enddate"]." ".$this->txt("results_round_at")." ".
                    date("H:i",strtotime($round["end_date"]));

                $action_status = $this->ctrl->getLinkTarget($this,'changeRoundStatus');
                $action_status = $this->ctrl->appendRequestTokenParameterString($action_status);
                $round["change_status_href"] = '<option value="'.$action_status.'">';

                // Zeigt "Aktuelle Runde" an, wenn Runde noch nicht beendet wurde
                if ( empty( $endDate ) ){
                    $round["status"] = '<select class="change-status" onchange="javascript:if ( $(this).val() != \'no-follow\' ) window.location.href = $(this).val();"><option value="no-follow">'.$this->txt("results_status_active").'</option>';
                    $round["change_status"] = $this->txt("results_status_passive")."</option>  <option value='".$action_status."&deactivate=true'>".$this->txt("results_status_deactivate").'</option>';
                    $round["round_name"] = $this->txt("current_round");
                    $round["date"] = $this->txt("current")." (".$round["date"].")";
                }
                if ( $round["type"] == "passive" ) {
                    $round["change_status_href"] = "<option value='".$action_status."&activate=true'>";
                    $round["status"] = "<select class=\"change-status\" onchange=\"javascript:if ( $(this).val() != 'no-follow' ) window.location.href = $(this).val();\"><option value=\no-follow\">".$this->txt("results_status_passive")."</option>";
                    $round["change_status"] = $this->txt("results_status_activate")."</option> <option value='".$action_status."&deactivate=true'>".$this->txt("results_status_deactivate")."</option>";
                }
                $this->ctrl->clearParameters($this);
                $result[] = $round;
                $round_name--;
            }
        }

        $tbl->setData($result);
        return $tbl->getHTML();
    }

    //--------------------------------------------------------------------------
    
    /**
     * Show round results. 
     * This calculates the results shown on the results page and refers to the 
     * diagrams.
     */
    public function showRoundResults(){
        global $ilAccess, $ilUser, $tpl, $ilTabs;
        include_once('./Services/Table/classes/class.ilTable2GUI.php');

        $ilTabs->activateTab("showResults");
        $round_id = $_GET["round_id"];;
        $html;
        $answers = $this->object->getAnswers($round_id);
        $answer_count = count($this->object->getDistinctAnswers($round_id));
        $questions = $this->object->getQuestions($this->object->getId());

        
        // For every question render the answers and add them
        if(!count($questions) == 0) {
            foreach ($questions as $question){
                switch($question["type"]) {
                    case QUESTION_TYPE_MULTI:	// Multiple choice
                        $html = $html.$this->showRoundResultsForMultipleChoice($question, $answers, $answer_count, $round_id);
                        break;
                    case QUESTION_TYPE_SINGLE:	// Single choice
                        $html = $html.$this->showRoundResultsForSingleChoice($question, $answers, $answer_count, $round_id);
                        break;
                    case QUESTION_TYPE_NUM:	// Numeric
                        $html = $html.$this->showRoundResultsForNumeric($question, $answers, $answer_count, $round_id);
                        break;
                }
            }
        }

        $tpl->setContent($html);
    }

    //--------------------------------------------------------------------------
    
    /**
     * Get result data for multiple choice
     * This method us for a multiple choice question.
     * It is used by showRoundResultsForMultipleChoice().
     * @param unknown_type $question
     */
    public function getResultDataForMultipleChoice($question, $answers, $answer_count, $round_id) {
        // Get the questions' choices from the database
        $choices = $this->object->getChoices($question['question_id']);
        $highChartJS = array("labels" => "", "data" => ""); // JS-Daten für Highchart Plugin

        if(!count($choices) == 0) {
            $return = array();
            foreach($choices as $choice){
                $count = 0;
                foreach ($answers as $answer){
                    if (($answer['choice_id'] == $choice['choice_id'])&&($answer['value'] != 0)){
                        $count++;
                    }
                }

                // calculating percentage
                $count1 = empty($answer_count)? 0 : ($count / $answer_count);
                $count2 = $count1 * 100;
                $percent = number_format($count2, 0);

                // display correct choices green and others red
                // TODO: adjust for other question types
                $GLOBALS['ilLog']->write(__METHO__.': MobileQuiz, correct_value: '.$choice['correct_value']);
                $GLOBALS['ilLog']->write(__METHO__.': MobileQuiz, type: '.$question['type']);

                if ($choice['correct_value'] == 2){ // neutral
                    $choice['color'] = "0"; // blue
                    $choice['colorName'] = "blue"; // blue
                } else if ($choice['correct_value'] == 0){
                    $choice['color'] = "1"; // red
                    $choice['colorName'] = "red"; // red
                } else {
                    $choice['color'] = "2"; // green
                    $choice['colorName'] = "green"; // red
                }
                //$choice['percentdesign'] = str_replace(",",".",0.9*$percent);
                //$choice['percent'] = $percent;
                $return[] = array(
                    "data" => array("y" => $count, "color" => $choice["color"]),
                    "label" => $choice["text"],
                    "colorName" => $choice["colorName"]
                );
            }
        }
        return $return;
    }

    //--------------------------------------------------------------------------
    
    /**
     * Ger Result Data
     * This method displays the results of a numeric question.
     * It is used by showRoundResultsForNumeric().
     * @param $question, $answers, $answer_count, $round_id
     */
    public function getResultDataForNumeric($question, $answers, $answer_count, $round_id) {
        // Get the questions' choice from the database
        $answers	= $this->object->getAnswers($round_id);
        $choices	= $this->object->getChoices($question['question_id']);

        $numeric_values		= (explode(';',$choices[0]['text']));
        $correct_number		= $numeric_values[3];
        $tolerance_range	= $numeric_values[4];

        // empty array for the datas
        $data = Array();
        // summarizing and sorting of the different answers => output: $data
        if(!count($choices) == 0) {
            foreach ($answers as $answer){
                if ($answer['choice_id'] == $choices[0]['choice_id']){
                    // collects all answers and counts them
                    $data[((string)$answer['value'])]++;
                }
            }
            // if nobody chose the right answer, we must add it.
            if(!array_key_exists((String)$correct_number, $data)) {
                $data["||+||".(String)$correct_number] = 0;
            }
            ksort($data);
        }

        $return = array();
        foreach ($data as $key => $value) {
            $row = Array();
            $row['text'] = str_replace("||+||", "", $key);

            // calculating percentage
            if ( $answer_count != 0 ) $count1 = $value / $answer_count;
            $count2 = $count1 * 100;
            $percent = number_format($count2, 0);

            // display correct choices green, not correct red and neutral blue
            // if the last symbol is a semicolon, then the numeric question is neutral
            // this means there aren't correct or not correct answers.
            // empty() cannot be used, because zero can be a possible value.
            if (substr($choices[0]['text'], -2) == ';;'){
                // neutral
                $row['color'] = "0"; // blue
            } else if ($key == $correct_number ||
                (isset($tolerance_range) &&
                    (($correct_number-$tolerance_range) <= $key) &&
                    (($correct_number+$tolerance_range) >= $key))) {
                // correct
                // considering the correct number and the olerance range
                $row['color'] = "2"; //green
            } else {
                // not correct
                $row['color'] = "1"; //red
            }
            $row['percentdesign'] = str_replace(",",".",0.9*$percent);
            $row['percent'] = $percent;
            $row['count'] = $value." ".$this->txt("results_out_of");

            if($key != $correct_number) {
                $result[] = $row;
            } else {
                // correct answer short
                array_unshift($result, $row);
            }

            $return[] = array(
                "data" => array("y" => $value, "color" => $row["color"]),
                "label" => addslashes($row["text"])
            );
        }

        return $return;
    }

    //--------------------------------------------------------------------------
    
    /**
     * Show round results for multiple choice
     * This method displays the results of a multiple choice question.
     * It is used by showRoundResults().
     * 
     * @param unknown_type $question
     */
    public function showRoundResultsForMultipleChoice($question, $answers, $answer_count, $round_id) {
        
        $chart_tpl = new ilTemplate("tpl.result_row.html", '', '',
            "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");

        // Collect Data
        $datas = $this->getResultDataForMultipleChoice($question, $answers, $answer_count, $round_id);
        
        // Structure the data for Chart displaying
        $chart_data_string;
        $chart_label_string;
        $chart_color_string;
        $chart_color_border_string;
        foreach( $datas as $data ) {
                $chart_data_string .= " ".$data['data']['y'].",";
                $chart_label_string .= ' "'.ilObjMobileQuizHelper::polishText($data['label']).'",';
                
                switch ($data['colorName']){
                    case 'blue':
                        $chart_color_string .= " 'rgba(54, 162, 235, 0.4)',";
                        $chart_color_border_string .= " 'rgba(54, 162, 235, 1)',";
                        break;
                    case 'green':
                        $chart_color_string .= " 'rgba(75, 192, 192, 0.4)',";
                        $chart_color_border_string .= " 'rgba(75, 192, 192, 1)',";
                        break;
                    case 'red':
                        $chart_color_string .= " 'rgba(255, 99, 132, 0.4)',";
                        $chart_color_border_string .= " 'rgba(255, 99, 132, 1)',";
                        break;
                }
        }

        $chart_tpl->setVariable("title", ilObjMobileQuizHelper::polishText($question['text']));
        $chart_tpl->setVariable("question_id", $question['question_id']);
        $chart_tpl->setVariable("data", $chart_data_string);
        $chart_tpl->setVariable("labels", $chart_label_string);
        $chart_tpl->setVariable("colors", $chart_color_string);
        $chart_tpl->setVariable("colors_border", $chart_color_border_string);
        
        
        // Get number of correct answers
        $correct_answers = $this->getCorrectAnswersCount($question['question_id'], $round_id);

        // calculating percentage
        $count1 = empty($answer_count)? 0 : ($correct_answers / $answer_count);
        $count2 = $count1 * 100;
        $percent = number_format($count2, 0);
           
        // depending on whether the answer can be classified as correct or incorrect, the number of all right answers is shown. Otherwise this information is not provided.
        if (($choice['correct_value'] != 2)){
            $correct_answer_text = $this->txt("results_round_correct").": ".$correct_answers." ".$this->txt("results_round_out_of")." ".$answer_count." (".$percent."%)";
        } else {
            $correct_answer_text = "";
        }
        $chart_tpl->setVariable("correct_answer_text", $correct_answer_text);
        
        $html = $chart_tpl->get();

        return $html;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Show round results for single choice
     * This method displays the results of a single choice question.
     * It is used by showRoundResults().
     * It does the same as Multiple Choice.
     * 
     * @param unknown_type $question
     */
    public function showRoundResultsForSingleChoice($question, $answers, $answer_count, $round_id) {
        return $this->showRoundResultsForMultipleChoice($question, $answers, $answer_count, $round_id);
    }

    //--------------------------------------------------------------------------
    
    /**
     * Show round results for numeric
     * This method displays the results of a numeric question.
     * It is used by showRoundResults().
     * @param $question, $answers, $answer_count, $round_id
     */
    public function showRoundResultsForNumeric($question, $answers, $answer_count, $round_id) {

        $tbl_mc = new ilTable2GUI($this);
        $tbl_mc->setId('question'.$question['question_id']);
        $tbl_mc->setRowTemplate('tpl.result_numeric.html', 'Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz');
        $datas = $this->getResultDataForNumeric($question, $answers, $answer_count, $round_id);

        // Very poor workaround for the needed API; but ILIAS doesn't have a good architecture for this thing
        // TODO: Find a better way for the API
        if ( $_GET['api'] == "result" && $question['question_id'] == $_GET['question_id'] ) {
            include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/frontend/api.chartData.php");
        }
        // Get the questions' choice from the database
        $answers	= $this->object->getAnswers($round_id);
        $choices	= $this->object->getChoices($question['question_id']);

        $numeric_values		= (explode(';',$choices[0]['text']));
        $correct_number		= $numeric_values[3];
        $tolerance_range	= $numeric_values[4];


        // Define Javacript for Highcharts Plugin
        $highChartJS = array();
        foreach( $datas as $data ) {
            if ( $data["label"] != "" ) {
                $highChartJS["data"] .= "{y: ".$data["data"]["y"].", color: colors[".$data["data"]["color"]."]},";
                $highChartJS["label"] .= "'".nl2br(trim(addslashes($data["label"])))."',";
            }
        }

        // Chart erstellen
        $tbl_mc->setData(
            array(0 =>
            array(
                "round_id" => $round_id,
                "id" => $question['question_id'],
                "data" => substr($highChartJS["data"], 0, -1),
                "label" => nl2br(trim(substr($highChartJS["label"], 0, -1))),
                "title" => ilObjMobileQuizHelper::cutText(preg_replace('/^\s+|\n|\r|\s+$/m', '',strip_tags($question['text']))),
                "count" => $answer_count,
                "lang_votes" => $this->txt("votes"),
                "lang_summary" => $this->txt("results_summary")
            )
            )
        );

        // depending on whether the answer can be classified as correct or incorrect, the number of all right answers is shown. Otherwise this information is not provided.
        if (($choices[0]['correct_value'] != 2)){
            // correct / not correct question
            //$correct_answers = $this->getCorrectNumericAnswersCount($question['question_id'], $round_id);
            $correct_answers = "a";

            // calculating percentage
            $count1 = empty($answer_count)? 0 : ($count / $answer_count);
            $count2 = $count1 * 100;
            $percent = number_format($count2, 0);
            //return $tbl_mc->getHTML().$this->txt("results_round_correct").": ".$correct_answers." ".$this->txt("results_round_out_of")." ".$answer_count." (".$percent."%)<br></br><br></br>";
        } else {
            // neutral question
            return $tbl_mc->getHTML()."<br></br><br></br>";
        }
    }

    //--------------------------------------------------------------------------
    
    /**
     * Get correct answers count
     *
     * @return	int		correct_answers_count
     * @param	int		question_id
     * @param	int		round_id
     */
    public function getCorrectAnswersCount($question_id, $round_id){
        global $ilAccess, $ilUser, $tpl, $ilTabs;

        $correct_answers_count = 0;
        // get all answers that belong to a certain round (only their user_sting)
        $answers = $this->object->getDistinctAnswers($round_id);

        foreach ($answers as $answer){
          	$fault_choices = 0;

            // these are the choices together with their answers correlating to a certain question and a certain answer
            $choices_of_question_answer = $this->object->getChoicesOfQuestionAnswer($question_id, $answer['user_string']);
            $part_answer_correct_count=0;
            $part_answer_correct_selected_count=0;
            foreach ($choices_of_question_answer as $element){
            	if ($element['correct_value'] == 1 ){
            		$part_answer_correct_count++;
            	}
            	
		        if ($element['correct_value'] == 1 && $element['value'] == 1 ){
		        	$part_answer_correct_selected_count++;
		        }

		        if ($element['correct_value'] == 0 && $element['value'] == 1 ){
		        	$fault_choices++;
		        }
            }

            if ($part_answer_correct_count==$part_answer_correct_selected_count && $fault_choices==0){
            	$correct_answers_count++;
            }
//             if ($choice['correct_value'] == 1 && $choice['value'] == 1){
//                 $correct_answers_count++;
//             }
        }
        

        return $correct_answers_count;
    }


    /**
     * Get correct numeric answers count
     *
     * @return	int		correct_answers_count
     * @param	int		question_id
     * @param	int		round_id
     */
    public function getCorrectNumericAnswersCount($question_id, $round_id){
        global $ilAccess, $ilUser, $tpl, $ilTabs;

        $correct_answers_count = 0;
        // get all answers that belong to a certain round (only their user_sting)
        $answers = $this->object->getAnswers($round_id);

        // get the choice of the numeric answer and read out the correct number and the tolerance range
        // both are necessary to count the correct answers
        // there is only one choice for a numeric question
        $choices	= $this->object->getChoices($question_id);
        // extract the variables from the text-field
        $numeric_values		= (explode(';',$choices[0]['text']));
        $correct_number		= $numeric_values[3];
        $tolerance_range	= $numeric_values[4];

        foreach ($answers as $answer){
            // filter the answers
            if($answer['choice_id'] == $choices[0]['choice_id']) {
                // check the answers
                if($answer['value'] == $correct_number ||
                    (isset($tolerance_range) &&
                        (($correct_number-$tolerance_range) <= $answer['value']) &&
                        ($answer['value'] <= ($correct_number+$tolerance_range)))) {
                    // exact aligmnent or in the tolerance range
                    $correct_answers_count++;
                }
            }

        }

        return $correct_answers_count;
    }


    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////// end Rounds    ///////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Edit Quiz
     * Enter description here ...
     */
    public function editQuiz() {
        global $tpl, $ilTabs;
        $ilTabs->activateTab("editQuiz");
        iljQueryUtil::initjQuery();

        $tpl->setContent($this->initQuestionAndAnswersTable());
    }

    /**
     * Add Question ansd Answers. This creates the question form by calling the initAddQuestionAndAnswersForm() method.
     */
    public function addQuestionAndAnswers () {
        global $tpl, $ilTabs;
        $ilTabs->activateTab("editQuiz");
        $this->initAddQuestionAndAnswersForm();
    }

    /**
     * Init Add Question and Answers Form. This was separated to be used from different
     * commands. Though right now it is only called by one command.
     */
    public function initAddQuestionAndAnswersForm () {
        global $tpl, $ilCtrl;

        $my_tpl = new ilTemplate("tpl.question_and_answers.html", true, true,
            "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");
        $rtokenFactory = new ilCtrl();
        $my_tpl->setVariable("ACTION_URL",$this->ctrl->getFormAction($this));
        $my_tpl->setVariable("SUBMIT_BUTTON", $this->txt("save"));
        $my_tpl->setVariable("NEW_QUESTION", $this->txt("question_add_head"));
        $my_tpl->setVariable("QUESTION", $this->txt("question_add_text"));
        $my_tpl->setVariable("QUESTION_TYPE", $this->txt("question_add_type"));
        $my_tpl->setVariable("CHOICES", $this->txt("choice_add_texts"));
        $my_tpl->setVariable("VAR_1", "value1");
        $my_tpl->setVariable("COMMAND", "cmd[createQuestionAndAnswers]");
        $my_tpl->setVariable("MINIMUM", $this->txt("choice_add_numeric_minimum"));
        $my_tpl->setVariable("MAXIMUM", $this->txt("choice_add_numeric_maximum"));
        $my_tpl->setVariable("STEP", $this->txt("choice_add_numeric_steprange"));
        $my_tpl->setVariable("CORRECT_VALUE", $this->txt("choice_add_numeric_correctvalue"));
        $my_tpl->setVariable("TOLERANCE_RANGE", $this->txt("choice_add_numeric_tolerenace_range"));
        $my_tpl->setVariable("SELECTED_SINGLE", 'selected="selected"');

        $my_tpl->setVariable("HIDE_NUMERIC_BLOCK", 'style="display:none;"');
        $my_tpl->setVariable("HIDE_SINGLE_CHOICE_BLOCK", 'style="display:none;"');

        $html = $my_tpl->get();
        $tpl->setContent($html);
    }


    public function initAddQuestionAndAnswersFormAfterError () {
        global $tpl, $ilCtrl;

        $my_tpl = new ilTemplate("tpl.question_and_answers.html", true, true,
            "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");
        $rtokenFactory = new ilCtrl();
        $my_tpl->setVariable("ACTION_URL",$this->ctrl->getFormAction($this));
        $my_tpl->setVariable("SUBMIT_BUTTON", $this->txt("save"));
        $my_tpl->setVariable("NEW_QUESTION", $this->txt("question_add_head"));
        $my_tpl->setVariable("QUESTION", $this->txt("question_add_text"));
        $my_tpl->setVariable("QUESTION_TYPE", $this->txt("question_add_type"));
        $my_tpl->setVariable("CHOICES", $this->txt("choice_add_texts"));
        $my_tpl->setVariable("VAR_1", "value1");
        $my_tpl->setVariable("MINIMUM", $this->txt("choice_add_numeric_minimum"));
        $my_tpl->setVariable("MAXIMUM", $this->txt("choice_add_numeric_maximum"));
        $my_tpl->setVariable("STEP", $this->txt("choice_add_numeric_steprange"));
        $my_tpl->setVariable("CORRECT_VALUE", $this->txt("choice_add_numeric_correctvalue"));
        $my_tpl->setVariable("TOLERANCE_RANGE", $this->txt("choice_add_numeric_tolerenace_range"));

        // refill fields
        include_once('class.ilObjMobileQuizWizard.php');
        $wiz = new ilObjMobileQuizWizard();
        $wiz -> fillQuestionAndAnswersAfterError($my_tpl);

        $html = $my_tpl->get();
        $tpl->setContent($html);
    }

    /**
     * Create Question and Answers. This is called after the firm was filled out. From here
     * the function in the Model is called to insert the question to the database
     */
    public function createQuestionAndAnswers () {
        global $tpl, $lng, $ilCtrl, $ilTabs;

        // create wizard object
        include_once('class.ilObjMobileQuizWizard.php');
        $wiz = new ilObjMobileQuizWizard();


        $ilTabs->activateTab("editQuiz");


        if ($wiz->checkInput()){
            $wiz->createQuestionAndAnswers($this->object);
            ilUtil::sendSuccess($this->txt("question_obj_create"), true);
            $ilCtrl->redirect($this, "editQuiz");
        }
        $this->initAddQuestionAndAnswersFormAfterError();
        //$tpl->setContent("Test");
    }

    public function showQuestionAndAnswers () {

    }

    //--------------------------------------------------------------------------
    
    /**
     * Display the questions in a table
     */
    public function initQuestionAndAnswersTable () {
        global $ilAccess, $ilUser, $ilDB;
        include_once('./Services/Table/classes/class.ilTable2GUI.php');
        $tbl = new ilTable2GUI($this);

        $tbl->setId('QuestionTable');
        $this->ctrl->setParameter($this, 'cmd', 'addQuestionAndAnswers');

        $formaction = $this->ctrl->getLinkTarget($this,'addQuestionAndAnswers');
        $formaction = $this->ctrl->appendRequestTokenParameterString($formaction);
        $tbl->setFormAction($formaction);
        //$tbl->addMultiCommand('insertQuestions', $this->lng->txt('insert'));
        $tbl->addCommandButton('addQuestionAndAnswers', $this->lng->txt('add'));

        $tbl->setRowTemplate('tpl.question_row.html', 'Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz');
        $tbl->setTitle($this->txt("question_table_head"));
        $tbl->setLimit($_GET[$tbl->getId()."_trows"], 20);

        $tbl->setOrderColumn('type');

        $tbl->addColumn($this->txt("question_table_text"), 'text', '60%');
        $tbl->addColumn($this->txt("question_table_type"), 'type', '10%');
        $tbl->addColumn($this->txt("question_table_choices"), 'choices', '8%');
        $tbl->addColumn($this->txt("question_table_edit"), 'edit', '8%');
        $tbl->addColumn($this->txt("question_table_delete"), 'delete', '8%');
        $tbl->addColumn($this->txt("question_table_up"),"upArrow", '3%');
        $tbl->addColumn($this->txt("question_table_down"),"downArrow", '3%');
        
        // write two columns with arrows

        // Get the questions from the database
        $questions = $this->object->getQuestions();
        $result = array();

        if(!count($questions) == 0) {
            foreach($questions as $question){

                // Show choices table
                $this->ctrl->setParameter($this,'question_id',$question['question_id']);
                $action_show_choices = $this->ctrl->getLinkTarget($this,'editQuestionAndAnswers');
                $action_show_choices = $this->ctrl->appendRequestTokenParameterString($action_show_choices);
                $question['show_choices_href'] = "<a href=".$action_show_choices.">";

                $question["choices"] = $this->object->getChoicesCount($question['question_id']);

                // Edit Question
                $this->ctrl->setParameter($this,'question_id',$question['question_id']);
                $this->ctrl->setParameter($this,'text',urlencode($question['text']));
                $action_edit = $this->ctrl->getLinkTarget($this,'editQuestionAndAnswers');
                $action_edit = $this->ctrl->appendRequestTokenParameterString($action_edit);
                $question['edit_question_href'] = "<a href=".$action_edit.">";
                $question["edit_question_txt"] = $this->txt("question_edit");
                $this->ctrl->clearParameters($this);

                // Delete Question
                $this->ctrl->setParameter($this,'question_id',$question['question_id']);
                $action_delete = $this->ctrl->getLinkTarget($this,'deleteQuestionAndAnswers');
                $action_delete = $this->ctrl->appendRequestTokenParameterString($action_delete);
                $question['delete_question_href'] = "<a href=".$action_delete.">";
                $question["delete_question_txt"] = $this->txt("question_delete");
                
                //switchupfunktion for arrow
                $this->ctrl->setParameter($this,'question_id',$question['question_id']);
                $action_Up = $this->ctrl->getLinkTarget($this,'switchUp');
                
                $action_Up = $this->ctrl->appendRequestTokenParameterString($action_Up);
                $question['arrow_up_href'] = "<a href=".$action_Up.">";
                $question["arrow_up_txt"] = "&#9650;";
                
                //switchDownfunktion for arrow
                $this->ctrl->setParameter($this,'question_id',$question['question_id']);
                $action_Down = $this->ctrl->getLinkTarget($this,'switchDown');
                
                $action_Down = $this->ctrl->appendRequestTokenParameterString($action_Down);
                $question['arrow_down_href'] = "<a href=".$action_Down.">";
                $question["arrow_down_txt"] = "&#9660;";
                
            

                // question type
                switch($question['type']) {
                    case "1":
                        $question['type'] = "Multiple Choice";
                        break;
                    case "2":
                        $question['type'] = "Single Choice";
                        break;
                    case "3":
                        $question['type'] = "Numeric";
                        break;
                }
                $this->ctrl->setParameter($this,'type',$question['type']);

                $this->ctrl->clearParameters($this);

                $result[] = $question;
            }
        }

        $tbl->setData($result);
        return $tbl->getHTML();
    }

    /**
     * Deleting the question and its choices from the database
     */
    public function deleteQuestionAndAnswers () {
        global $ilUser, $tpl, $ilTabs, $ilCtrl;
        $ilTabs->activateTab("editQuiz");

        if(isset($_GET['question_id']) && is_numeric($_GET['question_id'])){

            $question_id = $_GET['question_id'];

            $this->object->deleteQuestion($question_id);
            $ilTabs->activateTab("editQuiz");

            ilUtil::sendSuccess($this->txt("successful_delete"), true);
            // Forwarding
            $ilCtrl->redirect($this, "editQuiz");
        }
    }
    
    public function switchUp(){
    	global $ilCtrl;
    	$question_id = $_GET['question_id'];
    	$this->object->switchUp($question_id);
    	$ilCtrl->redirect($this, "editQuiz");
    	// get id
    	// rufe switchfunktion auf
    	//redirect auf sich selber
    	//im switch case oben noch switch up und down
    }
    
    public function switchDown(){
    	global $ilCtrl;
    	//http://localhost/ilias/ilias.php?ref_id=48&question_id=1&cmd=switchDown&cmdClass=ilobjmobilequizgui&cmdNode=2y:lp&baseClass=ilObjPluginDispatchGUI&rtoken=0f57d896685c36d2c12204bd51a5cfad
    	$question_id = $_GET['question_id'];
    	$this->object->switchDown($question_id);
    	$ilCtrl->redirect($this, "editQuiz");
    	//$ilCtrl->($this, "")
    }

    public function editQuestionAndAnswers () {
        global $tpl, $ilTabs;
        $ilTabs->activateTab("editQuiz");
        $this->initQuestionAndAnswersEditForm();
    }

    /**
     * Creates the form for editing question and choices
     */
    public function initQuestionAndAnswersEditForm () {
        global $tpl, $ilCtrl;

        $my_tpl = new ilTemplate("tpl.question_and_answers.html", true, true,
            "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");
        $rtokenFactory = new ilCtrl();
        $my_tpl->setVariable("ACTION_URL",$this->ctrl->getFormAction($this));
        $my_tpl->setVariable("SUBMIT_BUTTON", $this->txt("save"));
        $my_tpl->setVariable("NEW_QUESTION", $this->txt("question_add_head"));
        $my_tpl->setVariable("QUESTION", $this->txt("question_add_text"));
        $my_tpl->setVariable("QUESTION_TYPE", $this->txt("question_add_type"));
        $my_tpl->setVariable("CHOICES", $this->txt("choice_add_texts"));
        $my_tpl->setVariable("MINIMUM", $this->txt("choice_add_numeric_minimum"));
        $my_tpl->setVariable("MAXIMUM", $this->txt("choice_add_numeric_maximum"));
        $my_tpl->setVariable("STEP", $this->txt("choice_add_numeric_steprange"));
        $my_tpl->setVariable("CORRECT_VALUE", $this->txt("choice_add_numeric_correctvalue"));
        $my_tpl->setVariable("TOLERANCE_RANGE", $this->txt("choice_add_numeric_tolerenace_range"));
        $my_tpl->setVariable("VAR_1", "value1");
        $my_tpl->setVariable("COMMAND", "cmd[changeQuestionAndAnswers]");
        $my_tpl->setVariable("HIDE_QUESTION_TYPE", 'style="display:none;"');

        // refill fields
        include_once('class.ilObjMobileQuizWizard.php');
        $wiz = new ilObjMobileQuizWizard();
        $wiz -> loadAnswerAndQuestions($_GET['question_id'],$my_tpl, $this->object);

        $html = $my_tpl->get();
        $tpl->setContent($html);

        $this->ctrl->clearParameters($this);
    }

    /**
     * Write the changes in the database
     */
    public function changeQuestionAndAnswers () {
        global $ilUser, $tpl, $ilTabs, $ilCtrl;
        $ilTabs->activateTab("editQuiz");

        // update database
        // create wizard object
        include_once('class.ilObjMobileQuizWizard.php');
        $wiz = new ilObjMobileQuizWizard();
        $wiz->changeQuestionAndAnswers($this->object);

        $_GET['question_id'] = $_POST['question_id'];
        $this->initQuestionAndAnswersEditForm();

        // load changed data and display them
    }

}
?>