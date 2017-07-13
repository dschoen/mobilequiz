<?php
require_once('./Services/Component/classes/class.ilPluginConfigGUI.php');
require_once('./Customizing/global/plugins/Services/Repository/RepositoryObject/MobileQuiz/classes/class.ilMobileQuizConfigDAO.php');

/**
 * Class ilMobileQuizConfigGUI
 *
 * @author  Daniel Schön <daniel.schoen@uni-mannheim.de>
 */
class ilMobileQuizConfigGUI extends ilPluginConfigGUI {

	const CMD_CONFIGURE 	= 'configure';
	const CMD_SAVE 			= 'save';
	
	var $configDAO;
	
	public function __construct()
	{
// 		parent::__construct();
		$this->configDAO = new ilMobileQuizConfigDAO();
	}
		
	
	public function performCommand($cmd) 
	{	
		switch ($cmd)
		{
			case self::CMD_CONFIGURE:
			case self::CMD_SAVE:
				$this->{$cmd}();
				break;
			default:
				error_log("MobileQuiz Settings: unknown command: ".$cmd);
		}
	}
	
	protected function configure()
	{
		global $tpl;
		$form = $this->initConfigurationForm($plugin);		
		$formValues = array();
		$formValues["SHORTENER_ACTIVE"] 	= $this->configDAO->getConfigItem("SHORTENER_ACTIVE");		
		$formValues["SHORTENER_USERNAME"] 	= $this->configDAO->getConfigItem("SHORTENER_USERNAME");
		$formValues["SHORTENER_PASSWORD"] 	= $this->configDAO->getConfigItem("SHORTENER_PASSWORD");
		$formValues["SHORTENER_URL"] 		= $this->configDAO->getConfigItem("SHORTENER_URL");
		$formValues["SHORTENER_FORMAT"] 	= $this->configDAO->getConfigItem("SHORTENER_FORMAT");
		$formValues["LATEX_ACTIVE"] 		= $this->configDAO->getConfigItem("LATEX_ACTIVE");		
		$form->setValuesByArray($formValues);
		$tpl->setContent($form->getHTML());
	}
	

	protected function save()
	{
		global $tpl, $lng, $ilCtrl;
		$form = $this->initConfigurationForm();
	
		if ($form->checkInput())
		{
			$this->configDAO->setConfigItem("SHORTENER_ACTIVE", ($_POST["SHORTENER_ACTIVE"] ? 1 : 0 ));
			$this->configDAO->setConfigItem("SHORTENER_USERNAME", $_POST["SHORTENER_USERNAME"] );
			$this->configDAO->setConfigItem("SHORTENER_PASSWORD", $_POST["SHORTENER_PASSWORD"] );
			$this->configDAO->setConfigItem("SHORTENER_URL", $_POST["SHORTENER_URL"] );
			$this->configDAO->setConfigItem("SHORTENER_FORMAT", $_POST["SHORTENER_FORMAT"] );
			$this->configDAO->setConfigItem("LATEX_ACTIVE", ($_POST["LATEX_ACTIVE"] ? 1 : 0 ));	
	
			ilUtil::sendSuccess($lng->txt("saved_successfully"), true);
			$ilCtrl->redirect($this, "configure");
		}
		else
		{
			$form->setValuesByPost();
			$tpl->setContent($form->getHtml());
		}
	}
	
	
	private function initConfigurationForm()
	{
		global $lng, $ilCtrl;
		$plugin = $this->getPluginObject();
	
		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
		$form->setTableWidth("90%");
		$form->setTitle($plugin->txt("config_label"));
		$form->setFormAction($ilCtrl->getFormAction($this));
	
		$input = new ilCheckboxInputGUI($plugin->txt("config_shortener_active"), "SHORTENER_ACTIVE");
		$input->setRequired(false);
		$input->setInfo($plugin->txt("config_shortener_active_info"));
		$form->addItem($input);
		
		$input = new ilTextInputGUI($plugin->txt("config_shortener_url"), "SHORTENER_URL");
		$input->setRequired(false);
		$input->setInfo($plugin->txt("config_shortener_url_info"));
		$form->addItem($input);
		
		$input = new ilTextInputGUI($plugin->txt("config_shortener_username"), "SHORTENER_USERNAME");
		$input->setRequired(false);
		$form->addItem($input);
		
		$input = new ilTextInputGUI($plugin->txt("config_shortener_password"), "SHORTENER_PASSWORD");
		$input->setRequired(false);
		$form->addItem($input);
				
		$input = new ilTextInputGUI($plugin->txt("config_shortener_format"), "SHORTENER_FORMAT");
		$input->setRequired(false);
		$input->setInfo($plugin->txt("config_shortener_format_info"));
		$form->addItem($input);
		
		$input = new ilCheckboxInputGUI($plugin->txt("config_latex_active"), "LATEX_ACTIVE");
		$input->setRequired(false);
		$input->setInfo($plugin->txt("config_latex_active_info"));
		$form->addItem($input);
		
		$form->addCommandButton("save", $lng->txt("save"));
	
		return $form;
	}
}