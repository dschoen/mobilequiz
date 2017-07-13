<?php
/*
+-----------------------------------------------------------------------------+
| MobileQuiz ILIAS plug-in for audience feedback with mobile devices          |
+-----------------------------------------------------------------------------+
| Copyright 2016 Daniel Schoen                                                |
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
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/classes/class.ilObjMobileQuizHelper.php");
include_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/classes/class.ilMobileQuizConfigDAO.php');
include_once("./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/configuration.php");


/**

 *
 * @author Stephan Schulz
 * @author Daniel Schoen <schoen@uni-mannheim.de>
 *
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
class ilObjMobileQuizGUI extends ilObjectPluginGUI {

	var $config;
	
    /**
     * Initialisation
     */
    protected function afterConstructor(){
        $this->config = new ilMobileQuizConfigDAO();
    }


    final function getType()
    {
        return "xuiz";
    }

    /**
     * Handles all commmands of this class, centralizes permission checks
     */
    function performCommand($cmd)
    {
        switch ($cmd){
            // write Permissions
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

            // read Permission
            case "showCurrentRound":    
                $this->checkPermission("read");
                $this->$cmd();
                break;
        }
    }

    /**
     * After object has been created -> jump to this command
     */
    function getAfterCreationCmd(){
        return "editQuiz";
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

    //--------------------------------------------------------------------------
    
    /**
     * Get Properties Values
     */
    function getPropertiesValues(){
        $values["title"] = $this->object->getTitle();
        $values["description"] = $this->object->getDescription();
        $this->form->setValuesByArray($values);
    }

    //--------------------------------------------------------------------------
    
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

    //--------------------------------------------------------------------------
    
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
        $description->setRequired(false);
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
    //                            Rounds
    //--------------------------------------------------------------------------

    /**
     * End current round
     */
    public function endCurrentRound(){
        global $tpl, $lng, $ilCtrl, $ilTabs;

        $ilTabs->activateTab("showCurrentRound");

        $this->object->endCurrentRound();
        $ilCtrl->redirect($this, "showCurrentRound");
    }

    //--------------------------------------------------------------------------
    
    /**
     * Begin current round
     */
    public function beginCurrentRound(){
        global $tpl, $lng, $ilCtrl, $ilTabs;

        $ilTabs->activateTab("showCurrentRound");

        $this->object->beginCurrentRound();
        $ilCtrl->redirect($this, "showCurrentRound");
    }

    //--------------------------------------------------------------------------
    
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

            $shorted_url = $currentRound['tiny_url'];

            $my_tpl = new ilTemplate("tpl.startpage_stop.html", true, true,
                "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");

            // link to stop a new round
            if ($ilAccess->checkAccess("write", "", $this->object->getRefId())){
            	$my_tpl->setVariable("STOP_BUTTON_LINK", $action_edit);
            	$my_tpl->setVariable("STOP_BUTTON_TEXT", $this->txt("round_stop"));
            }

            $my_tpl->setVariable("LNG_FULLSCREEN", $this->txt("startpage_fullscreen"));
            $my_tpl->setVariable("LNG_USERS", $this->txt("startpage_users"));
            $my_tpl->setVariable("DATA_USERS", count($this->object->getDistinctAnswers($round_id)));
            
            $my_tpl->setVariable("IMAGE_URL",$server_url.ilUtil::getWebspaceDir()."/MobileQuiz_data/".$round_id."/qrcode.png");
            $my_tpl->setVariable("QUIZ_URL",$shorted_url);

            // Ajax Update Information
            $my_tpl->setVariable("AJAX_INTERFACE_URL", ilObjMobileQuizHelper::getPluginUrl()."interfaces/liveChartUpdate.php");
            $my_tpl->setVariable("AJAX_SECRET", AJAX_INTERFACE_SECRET);
            $my_tpl->setVariable("AJAX_UPDATE_TIME", AJAX_CHART_UPDATE_TIME);
            $my_tpl->setVariable("ROUND_ID", $round_id);
            
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
                $my_tpl->setVariable("START_BUTTON_LINK", $action_edit);
                $my_tpl->setVariable("START_BUTTON_TEXT", $this->txt("round_start"));                
            }
            // info text
            $my_tpl->setVariable("INFO_TEXT",$this->txt("round_start_info"));

            $html = $my_tpl->get();

            $this->ctrl->clearParameters($this);
        }

        $tpl->setContent($html);
    }

    //--------------------------------------------------------------------------
    
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

    //--------------------------------------------------------------------------
    
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


    //--------------------------------------------------------------------------
    //                         RESULTS - Export
    //--------------------------------------------------------------------------

    /**
     * Is called when the "Export Result" Button is pressed.
     * The function calles the Export Function.
     */
    public function exportResultData() {
        
        // get rounds
        $rounds = $this->object->getRounds();
        
        // Check if rounds exist, write a message if not
        if (count($rounds) > 0) {            
            $this->exportResults();
        } else {
            ilUtil::sendFailure($this->txt("results_export_failure"), true);
        }
        
        $this->ctrl->redirect($this, 'showResults');
    }

    //--------------------------------------------------------------------------
    
    /*
     * Exports the data
     * Function is called by public function exportResultData
     */
    function exportResults() {
        $format_bold        = "";
        $format_percent     = "";
        $format_datetime    = "";
        $format_title       = "";
        $surveyname = "mobilequiz_data_export";

        include_once "./Services/Excel/classes/class.ilExcelWriterAdapter.php";
        $excelfile  = ilUtil::ilTempnam();
        $adapter    = new ilExcelWriterAdapter($excelfile, FALSE);
        $workbook   = $adapter->getWorkbook();
        $workbook->setVersion(8); // Use Excel97/2000 Format
        //
        // Create a worksheet
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

        // Create a worksheet
        include_once ("./Services/Excel/classes/class.ilExcelUtils.php");

        // Get rounds from the Database
        $rounds = $this->object->getRounds();

        if(!count($rounds) == 0) {
            foreach($rounds as $round){

                // Add a seperate worksheet for every Round
                $mainworksheet =& $workbook->addWorksheet("Runde ".$round['round_id']);
                $column = 0;
                $row    = 0;

                // Write first line with titles
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Frage", "excel", $format_bold));
                $column++;
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Fragentyp", "excel", $format_bold));
                $column++;
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Antwort", "excel", $format_bold));
                $column++;
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Antworttyp", "excel", $format_bold));
                $column++;
                $mainworksheet->writeString(0, $column, ilExcelUtils::_convert_text("Anzahl", "excel", $format_bold));

                $round_id   = $round['round_id'];                
                $questions  = $this->object->getQuestionsOfQuiz($this->object->getId());

                if(!count($questions) == 0) {
                    foreach ($questions as $question){

                        $choices = $this->object->getChoicesOfQuestion($question['question_id']);
                        $answers = $this->object->getAnswers($round_id);

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
                            case QUESTION_TYPE_NUM:
                                if(!count($choices) == 0) {
                                    foreach($choices as $choice){ // Theres always only one Choice with numeric questions
                                        
                                        // get Answers to this choice
                                        $answers = $this->object->getAnswersToChoice($round_id, $choice['choice_id']);                                        
                                        
                                        // Summarize the answers
                                        $values = array();
                                        foreach ($answers as $answer){
                                            $value = $answer['value'];
                                            if (key_exists($value, $values)){
                                                $values[$value] += 1;
                                            } else {
                                                $values[$value] = 1;
                                            }                                            
                                        }
                                           
                                        // Sort values from low to high
                                        ksort($values);
                                        
                                        // Write values in Sheet
                                        foreach ($values as $value => $count){
                                            // write into sheet
                                            $column = 0;
                                            $row++;
                                            $mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($question['text'], "excel", $format_bold));
                                            $column++;
                                            $mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($question['type'], "excel", $format_bold));
                                            $column++;
                                            $mainworksheet->writeNumber($row, $column, ilExcelUtils::_convert_text($value, "excel", 0));
                                            $column++;
                                            $mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text('', "excel", 0));
                                            $column++;
                                            $mainworksheet->writeNumber($row, $column, ilExcelUtils::_convert_text($count, "excel", 0));
                                        }
                                    }
                                }
                                break;
                        	case QUESTION_TYPE_TEXT:
                        		if(count($choices) == 0) {
                        			// do nothing
                        			break;
                        		}
                        		$choice = $choices[0];
                        		$answers = $this->object->getAnswersToChoice($round_id, $choice['choice_id']);
                        		
                        		error_log("---------------");
                        		
                        		
                        		// write into sheet
                        		foreach ($answers as $answer) {
                        			$column = 0;
                        			$row++;
                        			$mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($question['text'], "excel", $format_bold));
                        			$column++;
                        			$mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($question['type'], "excel", $format_bold));
                        			$column++;
                        			$mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text($answer['value'], "excel", 0));
                        			$column++;
                        			$mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text("-", "excel", 0));
                        			$column++;
                        			$mainworksheet->writeString($row, $column, ilExcelUtils::_convert_text("-", "excel", 0));
                        		}
                        		
                        		break;
                        }
                        // write empty line after question
                        $row++;
                    }
                }
            }
        }
        // Send file to client
        $workbook->close();
        ilUtil::deliverFile($excelfile, "$surveyname.xls", "application/vnd.ms-excel");
        exit();
    }

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
                $round["quiz_url"] = $round['tiny_url'];
                $round["image_url"] = ilUtil::getWebspaceDir()."/MobileQuiz_data/".$round['round_id']."/qrcode.png";
                $round["show_qr"] = $this->txt("results_show_qr");
                $round["open_quiz"] = $this->txt("rounds_open_quiz");                
                
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
        $answers        = $this->object->getAnswers($round_id);
        $answer_count   = count($this->object->getDistinctAnswers($round_id));
        $questions      = $this->object->getQuestionsOfQuiz($this->object->getId());

        
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
                    case QUESTION_TYPE_NUM:		// Numeric
                        $html = $html.$this->showRoundResultsForNumeric($question, $answers, $answer_count, $round_id);
                        break;
                    case QUESTION_TYPE_TEXT:	// Text
                       	$html = $html.$this->showRoundResultsForText($question, $answers, $answer_count, $round_id);
                       	break;
                }
            }
        }

        $tpl->setContent($html);
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
                
                // prepare text and escape Curvy Brackets for MathJax LaTeX transformation
                // and escape backslash "\" with "\\" for it is used in JavaScript
                $label_string = ilObjMobileQuizHelper::polishText($data['label']);                
                $label_string = ilObjMobileQuizHelper::escapeCurvyBrackets($label_string);
                $label_string = ilObjMobileQuizHelper::escapeBackslashesForJavaScript($label_string);
                
                $chart_label_string .= ' "'.$label_string.'",';
                
                switch ($data['colorName']){
                    case 'blue':
                        $chart_color_string .= " 'rgba(54, 162, 235, 0.4)',";
                        $chart_color_border_string .= " 'rgba(54, 162, 235, 1)',";
                        break;
                    case 'green':
                        $chart_color_string .= " 'rgba(75, 192, 75, 0.4)',";
                        $chart_color_border_string .= " 'rgba(75, 192, 75, 1)',";
                        break;
                    case 'red':
                        $chart_color_string .= " 'rgba(255, 99, 132, 0.4)',";
                        $chart_color_border_string .= " 'rgba(255, 99, 132, 1)',";
                        break;
                }
        }
        
        // prepare and escape title
        $chart_title = ilObjMobileQuizHelper::polishText($question['text']);
        $chart_title = ilObjMobileQuizHelper::escapeCurvyBrackets($chart_title);
           
        $chart_tpl->setVariable("title", $chart_title);
        $chart_tpl->setVariable("question_id", $question['question_id']);
        $chart_tpl->setVariable("round_id", $round_id);
        $chart_tpl->setVariable("data", $chart_data_string);
        $chart_tpl->setVariable("labels", $chart_label_string);
        $chart_tpl->setVariable("colors", $chart_color_string);
        $chart_tpl->setVariable("colors_border", $chart_color_border_string);
        $chart_tpl->setVariable("ajax_interface_url", ilObjMobileQuizHelper::getPluginUrl()."interfaces/liveChartUpdate.php");
        $chart_tpl->setVariable("secret", AJAX_INTERFACE_SECRET);
        $chart_tpl->setVariable("ajax_update_time", AJAX_CHART_UPDATE_TIME);
        $chart_tpl->setVariable("latex", $this->config->getConfigItem("LATEX_ACTIVE"));
        
        
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
    
    public function showRoundResultsForNumeric($question, $answers, $answer_count, $round_id) {

        $chart_tpl = new ilTemplate("tpl.result_numeric.html", '', '',
            "Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");
        
        // Get Data for Chart
        $datas = $this->getResultDataForNumeric($question, $answers, $answer_count, $round_id);

        // Get the questions parameters from the database
        $parameters	= $this->object->getChoicesOfQuestion($question['question_id']);
        $numeric_values     = (explode(';',$parameters[0]['text']));
        $numeric_min        = $numeric_values[0];
        $numeric_max        = $numeric_values[1];
        $numeric_step       = $numeric_values[2];
        $numeric_correct    = $numeric_values[3];
        $numeric_tolerance  = $numeric_values[4];

        // Structure the data for Chart displaying
        $chart_data_string = "";
        $chart_label_string = "";
        $chart_color_string;
        $chart_color_border_string;
        foreach( $datas as $data ) {
                
            // Skip loop if data is empty
            if (empty($data)) {
                continue;
            }
            
            $data_value = $data['data'];

            $chart_data_string .= $data_value.", ";

            // No need for polishing as labes are calculated integers
            $chart_label_string .= '"'.$data['label'].'", ';

            switch ($data['colorName']){
                case 'blue':
                    $chart_color_string .= " 'rgba(54, 162, 235, 0.4)',";
                    $chart_color_border_string .= " 'rgba(54, 162, 235, 1)',";
                    break;
                case 'green':
                    $chart_color_string .= " 'rgba(75, 192, 75, 0.4)',";
                    $chart_color_border_string .= " 'rgba(75, 192, 75, 1)',";
                    break;
                case 'red':
                    $chart_color_string .= " 'rgba(255, 99, 132, 0.4)',";
                    $chart_color_border_string .= " 'rgba(255, 99, 132, 1)',";
                    break;
            }
        }

        $chart_tpl->setVariable("title", ilObjMobileQuizHelper::polishText($question['text']));
        $chart_tpl->setVariable("question_id", $question['question_id']);
        $chart_tpl->setVariable("round_id", $round_id);
        $chart_tpl->setVariable("data", $chart_data_string);
        $chart_tpl->setVariable("labels", $chart_label_string);
        $chart_tpl->setVariable("colors", $chart_color_string);
        $chart_tpl->setVariable("colors_border", $chart_color_border_string);
        $chart_tpl->setVariable("ajax_interface_url", ilObjMobileQuizHelper::getPluginUrl()."interfaces/liveChartUpdate.php");
        $chart_tpl->setVariable("secret", AJAX_INTERFACE_SECRET);
        $chart_tpl->setVariable("ajax_update_time", AJAX_CHART_UPDATE_TIME);
        $chart_tpl->setVariable("latex", $this->config->getConfigItem("LATEX_ACTIVE"));
        
        // Correct answer Text
        $correct_answer_text = "-";
        if (!empty($numeric_correct)) {
        
            // Get number of correct answers
            $correct_answers = $this->getCorrectNumericAnswersCount($question['question_id'], $round_id);

            // calculating percentage
            $count1 = empty($answer_count)? 0 : ($correct_answers / $answer_count);
            $count2 = $count1 * 100;
            $percent = number_format($count2, 0);

            $correct_answer_text = $this->txt("results_round_correct").": ".$correct_answers." ".$this->txt("results_round_out_of")." ".$answer_count." (".$percent."%)";

        }
        $chart_tpl->setVariable("correct_answer_text", $correct_answer_text);
        
        $html = $chart_tpl->get();
        return $html;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Show round results for multiple choice
     * This method displays the results of a multiple choice question.
     * It is used by showRoundResults().
     *
     * @param unknown_type $question
     */
    public function showRoundResultsForText($question, $answers, $answer_count, $round_id) {
    	 
    	$chart_tpl = new ilTemplate("tpl.result_text.html", '', '',
    			"Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");
    
    	// Collect Data
    	$datas = $this->getResultDataForText($question, $answers, $answer_count, $round_id);
       
    	// Create Data String
    	$data_string = "";
    	$data_ids = "";    	
    	foreach( $datas as $data ) {
    	
    		// Skip loop if data is empty
    		if (empty($data)) {
    			continue;
    		}    		
    		$data_string = $data_string.'"'.ilObjMobileQuizHelper::polishText($data['value']).'", ';
    		$data_ids = $data_ids.'"'.$data['answer_id'].'", ';
    	}
    	
    	// Count Data for Tag Cloud weight  	
    	$data_bucket = array();
    	foreach( $datas as $data ) {
    		 
    		// Skip loop if data is empty
    		if (empty($data)) {
    			continue;
    		}
    		$word = trim($data['value']);
    		
    		if (array_key_exists($word, $data_bucket)) {
    			$data_bucket[$word] += 1;
    		} else {
    			$data_bucket[$word] = 1;
    		}    		
    	}
    	
    	// write tag cloud string
    	$data_weight_string = "";
    	foreach( $data_bucket as $data => $weight ) {
    		 

    		$data_weight_string = $data_weight_string
    			.'{text:"'.ilObjMobileQuizHelper::polishTextTagCloud($data).'",'
    			. 'weight:'.$weight.'},';
    	}
    	
    	// prepare and escape title
    	$chart_title = ilObjMobileQuizHelper::polishText($question['text']);
    	$chart_title = ilObjMobileQuizHelper::escapeCurvyBrackets($chart_title);
    	 
    	$chart_tpl->setVariable("TITLE", $chart_title);
    	$chart_tpl->setVariable("QUESTION_ID", $question['question_id']);
    	$chart_tpl->setVariable("ANSWERS", $data_string);
    	$chart_tpl->setVariable("ANSWER_IDS", $data_ids);
    	$chart_tpl->setVariable("ANSWERS_WEIGHTED", $data_weight_string);    	
    	$chart_tpl->setVariable("ROUND_ID", $round_id);    	
    	
    	$chart_tpl->setVariable("AJAX_INTERFACE_URL", ilObjMobileQuizHelper::getPluginUrl()."interfaces/liveChartUpdate.php");
    	$chart_tpl->setVariable("AJAX_SECRET", AJAX_INTERFACE_SECRET);
    	$chart_tpl->setVariable("AJAX_UPDATE_TIME", AJAX_CHART_UPDATE_TIME);
    
    	$chart_tpl->setVariable("latex", $this->config->getConfigItem("LATEX_ACTIVE"));
    	
    	$html = $chart_tpl->get();    
    	return $html;
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
        $choices = $this->object->getChoicesOfQuestion($question['question_id']);

        if(!count($choices) == 0) {
            $return = array();
            foreach($choices as $choice){
                
            	// get the numbers for this answer
                $count = $this->object->countAnswers($round_id, $choice['choice_id']);

                if ($choice['correct_value'] == 2){ // neutral
                    $choice['colorName'] = "blue";
                } else if ($choice['correct_value'] == 0){
                    $choice['colorName'] = "red";
                } else {
                    $choice['colorName'] = "green";
                }
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
     * Get Result Data
     * This method displays the results of a numeric question.
     * It is used by showRoundResultsForNumeric().
     * @param $question, $answers, $answer_count, $round_id
     */
    public function getResultDataForNumeric($question, $answers, $answer_count, $round_id) {
        // Get the questions' choice from the database
        $answers	= $this->object->getAnswers($round_id);
        $parameters	= $this->object->getChoicesOfQuestion($question['question_id']);

        $numeric_values     = (explode(';',$parameters[0]['text']));
        $numeric_min        = $numeric_values[0];
        $numeric_max        = $numeric_values[1];
        $numeric_step       = $numeric_values[2];
        $numeric_correct    = $numeric_values[3];
        $numeric_tolerance  = $numeric_values[4];

        // empty array for the datas
        $data = Array();
            
        // create the answer buckets
        for ($i = (float)$numeric_min; $i <= (float)$numeric_max; $i = $i+(float)$numeric_step) {    
            $data[(String)$i] = 0; 
        }

        // summarizing and sorting of the different answers => output: $data
        if(!count($parameters) == 0) {
            foreach ($answers as $answer){
                if ($answer['choice_id'] == $parameters[0]['choice_id']){
                    // collects all answers and counts them
                    $data[((string)$answer['value'])]++;
                }
            }            
        }
        
        // sort all answers        
        ksort($data);
        
        // create retun array
        foreach ($data as $key => $value) {
 
            $label = $key;

            // display correct choices green, not correct red and neutral blue
            if (!is_numeric($numeric_correct)){
                // neutral
                $color = "blue"; // blue
            } else if ($key == $numeric_correct ||
                (isset($numeric_tolerance) &&
                    (($numeric_correct-$numeric_tolerance) <= $key) &&
                    (($numeric_correct+$numeric_tolerance) >= $key))) {
                $color = "green"; //green
            } else {
                $color = "red"; //red
            }

            $return[] = array(
                "data"      => $value,
                "label"     => $label,
                "colorName" => $color,                
            );
        }
        return $return;
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Get result data for multiple choice
     * This method us for a multiple choice question.
     * It is used by showRoundResultsForMultipleChoice().
     * @param unknown_type $question
     */
    public function getResultDataForText($question, $answers, $answer_count, $round_id) {
    	// Get the questions' choices from the database
    	$choices = $this->object->getChoicesOfQuestion($question['question_id']);
    	
    	// there should only be one choice
    	$choice = $choices[0];
    	
    	if(count($choices) == 0) {
    		// failure should not happen
    		return;
    	}
    	
    	$answers = $this->object->getAnswersToChoice($round_id, $choice['choice_id']);
    	
    	return $answers;
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

    // -------------------------------------------------------------------------

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
        $choices	= $this->object->getChoicesOfQuestion($question_id);
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

    //--------------------------------------------------------------------------
    //                         Questions - LIST
    //--------------------------------------------------------------------------

    /**
     * Open Edit Page
     * 
     */
    public function editQuiz() 
    {
        global $tpl, $ilTabs;
        $ilTabs->activateTab("editQuiz");
        iljQueryUtil::initjQuery();

        $tpl->setContent($this->initQuestionTable());
    }

    // -------------------------------------------------------------------------
    
    /**
     * Add Question and Answers. 
     * This creates the question form by calling the initAddQuestionAndAnswersForm() method.
     */
    public function addQuestionAndAnswers() 
    {
        global $tpl, $ilTabs;
        $ilTabs->activateTab("editQuiz");
        $this->openQuestionAndAnswersForm();
    }

    //--------------------------------------------------------------------------
    
    /**
     * Renders the view of all questions in the Table List
     */
    public function initQuestionTable () {
        global $ilAccess, $ilUser, $ilDB;
        include_once('./Services/Table/classes/class.ilTable2GUI.php');
        $tbl = new ilTable2GUI($this);

        $tbl->setId('QuestionTable');
        $this->ctrl->setParameter($this, 'cmd', 'addQuestionAndAnswers');

        $formaction = $this->ctrl->getLinkTarget($this,'addQuestionAndAnswers');
        $formaction = $this->ctrl->appendRequestTokenParameterString($formaction);
        $tbl->setFormAction($formaction);
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
        $questions = $this->object->getQuestionsOfQuiz($this->object->getId());
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
                
                // No idea where this is originally set, but here can the text
                // be polised before handing to the template
                $question['text'] = ilObjMobileQuizHelper::polishText($question['text']);
                
                // To prevent the problem with curvy brackets.
                $question['text'] = ilObjMobileQuizHelper::escapeCurvyBrackets($question['text']);

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
                    case "4":
                       	$question['type'] = "Text";
                       	break;
                }
                
                // This is quite ugly, but this way the information can be given
                // to the template for using LaTeX transformation.
                $question["latex"] = $this->config->getConfigItem("LATEX_ACTIVE");
                
                $this->ctrl->setParameter($this,'type',$question['type']);
                $this->ctrl->clearParameters($this);

                $result[] = $question;
            }
        }

        $tbl->setData($result);
        return $tbl->getHTML();
    }

    //--------------------------------------------------------------------------
    
    /**
     * Switch one question UP
     */
    public function switchUp(){
    	global $ilCtrl;
    	$question_id = $_GET['question_id'];
    	$this->object->switchUp($question_id);
    	$ilCtrl->redirect($this, "editQuiz");
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Switch one questino DOWN
     */
    public function switchDown(){
    	global $ilCtrl;
    	$question_id = $_GET['question_id'];
    	$this->object->switchDown($question_id);
    	$ilCtrl->redirect($this, "editQuiz");
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Open this question in Choices FORM
     */
    public function editQuestionAndAnswers () {
    	global $tpl, $ilTabs;
    	$ilTabs->activateTab("editQuiz");
    	$this->openQuestionAndAnswersForm();
    }
    
    //--------------------------------------------------------------------------
    //                   Choices aka. Questions and Answers FORM
    //--------------------------------------------------------------------------    
    
    /**
     * Creates the form for editing question and choices
     */
    public function openQuestionAndAnswersForm () {
    	global $tpl, $ilCtrl;
    
    	$my_tpl = new ilTemplate("tpl.question_and_answers.html", true, true,
    			"Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz");
    	$rtokenFactory = new ilCtrl();
    
    	$my_tpl->setVariable("ACTION_URL",			$this->ctrl->getFormAction($this));
    	
    	// labels    	
    	$my_tpl->setVariable("CHOICES_HEADLINE", 	$this->txt("choice_form_headline"));
    	$my_tpl->setVariable("SUBMIT_BUTTON", 		$this->txt("save"));
    	$my_tpl->setVariable("BUTTON_CHOICE_ADD",	$this->txt("choice_form_button_add"));
    	$my_tpl->setVariable("BUTTON_CHOICE_DELETE",$this->txt("choice_form_button_delete"));
    	$my_tpl->setVariable("ASTERISK_TEXT",$this->txt("choice_form_asterisk"));    	
    	$my_tpl->setVariable("QUESTION", 			$this->txt("question_add_text"));
    	$my_tpl->setVariable("QUESTION_TYPE", 		$this->txt("question_add_type"));
    	$my_tpl->setVariable("CHOICES", 			$this->txt("choice_add_texts"));
    	$my_tpl->setVariable("MINIMUM", 			$this->txt("choice_add_numeric_minimum"));
    	$my_tpl->setVariable("MAXIMUM", 			$this->txt("choice_add_numeric_maximum"));
    	$my_tpl->setVariable("STEP", 				$this->txt("choice_add_numeric_steprange"));
    	$my_tpl->setVariable("CORRECT_VALUE", 		$this->txt("choice_add_numeric_correctvalue"));
    	$my_tpl->setVariable("TOLERANCE_RANGE", 	$this->txt("choice_add_numeric_tolerenace_range"));    
    	$my_tpl->setVariable("DELETE",          	$this->txt("choice_delete"));
    	$my_tpl->setVariable("DELETE_INFO",     	$this->txt("choice_delete_info"));
    	$my_tpl->setVariable("MOVE_UP",         	$this->txt("choice_up"));
    	$my_tpl->setVariable("MOVE_UP_INFO",    	$this->txt("choice_up_info"));
    	$my_tpl->setVariable("MOVE_DOWN",       	$this->txt("choice_down"));
    	$my_tpl->setVariable("MOVE_DOWN_INFO",  	$this->txt("choice_down_info"));    
    	$my_tpl->setVariable("SOLUTION", 			$this->txt("choice_form_solution"));
    	$my_tpl->setVariable("FURTHERMORE", 		$this->txt("choice_form_furthermore"));
    	$my_tpl->setVariable("TEXT_CORRECT_LABEL",  $this->txt("choice_form_text_correct_label"));
           	
    	// Set the Parameters for the choice Field, so that it is not visible, but the parameters are valid
    	$my_tpl->setVariable ( "MUL_SHOW", "none" );
    	$my_tpl->setVariable ( "MUL_TEXT", "" );
    	$my_tpl->setVariable ( "MUL_ID", "000" );	// set dummy id to 000
    	$my_tpl->setVariable ( "MUL_COU", "1" );
    	$my_tpl->setVariable ( "MUL_DEL", true );
    	$my_tpl->setVariable ( "MUL_TYPE_C", "" );
    	$my_tpl->setVariable ( "MUL_TYPE_N", "checked");
    	$my_tpl->setVariable ( "MUL_TYPE_I", "" );
    	$my_tpl->setVariable ( "ROW_ID", "1" );
    	
    	// check if it is a new question or editing an existing one
    	if (!isset($_GET['question_id'])) {
    		// new question
    		$my_tpl->setVariable("COMMAND", "cmd[createQuestionAndAnswers]");
    		$my_tpl->setVariable("HIDE_NUMERIC_BLOCK", 'style="display:none;"');
    		$my_tpl->setVariable("HIDE_TEXT_BLOCK", 'style="display:none;"');
    		// multiple choice is not hidden as it is the default value
    		
    	} else {
    		// prepare for edit existing question and choices
    		$question_id = $_GET['question_id'];
    		$my_tpl->setVariable("COMMAND", "cmd[changeQuestionAndAnswers]");
    		$my_tpl->setVariable("HIDE_QUESTION_TYPE", 'style="display:none;"');
    	}
    	
    	
    	// Open Wizard and fill fields with data
    	include_once('class.ilObjMobileQuizWizard.php');
    	$wiz = new ilObjMobileQuizWizard();
    	$wiz -> loadAnswerAndQuestions($_GET['question_id'],$my_tpl, $this->object);
    
    	$html = $my_tpl->get();
    	$tpl->setContent($html);
    
    	$this->ctrl->clearParameters($this);
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Create Question and Answers. This is called after the form was filled out. From here
     * the function in the Model is called to insert the question to the database
     */
    public function createQuestionAndAnswers () {
    	global $tpl, $ilCtrl, $ilTabs, $lng;
    
    	$ilTabs->activateTab("editQuiz");
    	
    	// create wizard object
    	include_once('class.ilObjMobileQuizWizard.php');
    	$wiz = new ilObjMobileQuizWizard();    
    	  
    	$wiz->changeQuestionAndAnswers($this->object);
    		
    	ilUtil::sendSuccess($this->txt("question_message_created"), true);
    	$ilCtrl->redirect($this, "editQuiz");
    	
    	$this->openQuestionAndAnswersForm();
    }
    
    //--------------------------------------------------------------------------
    
    /**
     * Write the changes in the database
     */
    public function changeQuestionAndAnswers () {
    	global $tpl, $ilCtrl, $ilTabs, $ilUser;
    	
    	$ilTabs->activateTab("editQuiz");
    
    	// create wizard object
    	include_once('class.ilObjMobileQuizWizard.php');
    	$wiz = new ilObjMobileQuizWizard();
    	
    	$wiz->changeQuestionAndAnswers($this->object);
    
    	ilUtil::sendSuccess($this->txt("question_message_edited"), true);
    	
    	$_GET['question_id'] = $_POST['question_id'];
    	$this->openQuestionAndAnswersForm();
    }
    
    //--------------------------------------------------------------------------
    
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
}
?>
