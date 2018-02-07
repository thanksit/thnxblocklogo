<?php
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
class thnxblocklogo extends Module implements WidgetInterface
{
	public function __construct()
	{
		$this->name = 'thnxblocklogo';
		$this->tab = 'front_office_features';
		$this->version = '1.0.0';
		$this->author = 'thanksit.com';
		$this->need_instance = 0;
		$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Platinum Theme Logo block');
		$this->description = $this->l('Displays Logo at the top of the shop.');
		$this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
	}
	public function install()
	{
		if(!parent::install()
			|| !$this->registerHook('displayTopRightOne')
			|| !$this->registerHook('displayNav')
			|| !$this->registerHook('displayTopLeft')
			|| !$this->xpertsampledata()
		)
			return false;
		return true;
	}
	public function xpertsampledata($demo=NULL)
	{
		if(($demo==NULL) || (empty($demo)))
			$demo = "demo_1";
		$func = 'xpertsample_'.$demo;
		if(method_exists($this,$func)){
        	$this->{$func}();
        }
        return true;
	}
	public function xpertsample_demo_1(){
		$this->LogoInsert("logopng.png");
	}
	public function xpertsample_demo_2(){
		$this->LogoInsert("logo-white.png");
	}
	public function xpertsample_demo_3(){
		$this->LogoInsert("logopng.png");
	}
	public function xpertsample_demo_4(){
		$this->LogoInsert("logopng.png");
	}
	public function xpertsample_demo_5(){
		$this->LogoInsert("logo-white.png");
	}
	public function xpertsample_demo_6(){
		$this->LogoInsert("logopng.png");
	}
	public function LogoInsert($logo = "logo-w.png")
	{
		$languages = Language::getLanguages(false);
		$imgname = array();
		$DESC = array();
		foreach ($languages as $lang)
		{
			$imgname[$lang['id_lang']] = $logo;
			$DESC[$lang['id_lang']] = '';
		}
		Configuration::updateValue('thnxBLOCKLOGO_IMG',$imgname);
		Configuration::updateValue('thnxBLOCKLOGO_DESC',$DESC);
		return true;
	}
	public function uninstall()
	{
		if(!parent::uninstall()
			|| !Configuration::deleteByName('thnxBLOCKLOGO_IMG')
			|| !Configuration::deleteByName('thnxBLOCKLOGO_Height')
			|| !Configuration::deleteByName('thnxBLOCKLOGO_Width')
			|| !Configuration::deleteByName('thnxBLOCKLOGO_DESC')
			)
			return false;
		else
			return true;
	}
	public function renderWidget($hookName = null, array $configuration = [])
	{
	    $this->smarty->assign($this->getWidgetVariables($hookName,$configuration));
	    return $this->fetch('module:'.$this->name.'/views/templates/front/'.$this->name.'.tpl');	
	}
	public function getWidgetVariables($hookName = null, array $configuration = [])
	{
		$return_arr = array();
	    $imgname = Configuration::get('thnxBLOCKLOGO_IMG', $this->context->language->id);
	    if($imgname && file_exists(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$imgname)){
	    	$return_arr['thnxlogo_img'] =  $this->context->link->protocol_content.Tools::getMediaServer($imgname).$this->_path.'img/'.$imgname;
	    	$return_arr['thnxlogo_desc'] =  Configuration::get('thnxBLOCKLOGO_DESC', $this->context->language->id);
	    	$return_arr['thnxlogo_height'] =  Configuration::get('thnxBLOCKLOGO_Height');
	    	$return_arr['thnxlogo_width'] =  Configuration::get('thnxBLOCKLOGO_Width');
	    	$return_arr['hookName'] =  $hookName;
	    }
	    return $return_arr;
	}
	public function postProcess()
	{
		if (Tools::isSubmit('submit'.$this->name))
		{
			$languages = Language::getLanguages(false);
			$values = array();
			$update_images_values = false;
			foreach ($languages as $lang)
			{
				if (isset($_FILES['thnxBLOCKLOGO_IMG_'.$lang['id_lang']])
					&& isset($_FILES['thnxBLOCKLOGO_IMG_'.$lang['id_lang']]['tmp_name'])
					&& !empty($_FILES['thnxBLOCKLOGO_IMG_'.$lang['id_lang']]['tmp_name']))
				{
					if ($error = ImageManager::validateUpload($_FILES['thnxBLOCKLOGO_IMG_'.$lang['id_lang']], 4000000))
						return $error;
					else
					{
						$ext = substr($_FILES['thnxBLOCKLOGO_IMG_'.$lang['id_lang']]['name'], strrpos($_FILES['thnxBLOCKLOGO_IMG_'.$lang['id_lang']]['name'], '.') + 1);
						$file_name = Tools::link_rewrite($_FILES['thnxBLOCKLOGO_IMG_'.$lang['id_lang']]['name']).'.'.$ext;

						if (!move_uploaded_file($_FILES['thnxBLOCKLOGO_IMG_'.$lang['id_lang']]['tmp_name'], dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$file_name))
							return $this->displayError($this->l('An error occurred while attempting to upload the file.'));
						else
						{
							if (Configuration::hasContext('thnxBLOCKLOGO_IMG', $lang['id_lang'], Shop::getContext())
								&& Configuration::get('thnxBLOCKLOGO_IMG', $lang['id_lang']) != $file_name)
								@unlink(dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.Configuration::get('thnxBLOCKLOGO_IMG', $lang['id_lang']));
							$values['thnxBLOCKLOGO_IMG'][$lang['id_lang']] = $file_name;
						}
					}

					$update_images_values = true;
				}
				$values['thnxBLOCKLOGO_DESC'][$lang['id_lang']] = Tools::getValue('thnxBLOCKLOGO_DESC_'.$lang['id_lang']);
				$values['thnxBLOCKLOGO_Height'] = Tools::getValue('thnxBLOCKLOGO_Height');
				$values['thnxBLOCKLOGO_Width'] = Tools::getValue('thnxBLOCKLOGO_Width');
			}
			if ($update_images_values)
				Configuration::updateValue('thnxBLOCKLOGO_IMG', $values['thnxBLOCKLOGO_IMG']);
			Configuration::updateValue('thnxBLOCKLOGO_DESC', $values['thnxBLOCKLOGO_DESC']);
			Configuration::updateValue('thnxBLOCKLOGO_Height', $values['thnxBLOCKLOGO_Height']);
			Configuration::updateValue('thnxBLOCKLOGO_Width', $values['thnxBLOCKLOGO_Width']);
			return $this->displayConfirmation($this->l('The settings have been updated.'));
		}
		return '';
	}
	public function getContent()
	{
		return $this->postProcess().$this->renderForm();
	}
	public function renderForm()
	{
		$fields_form = array(
			'form' => array(
				'legend' => array(
					'title' => $this->l('Logo Settings'),
					'icon' => 'icon-cogs'
				),
				'input' => array(
					array(
						'type' => 'file_lang',
						'label' => $this->l('Logo image'),
						'name' => 'thnxBLOCKLOGO_IMG',
						'desc' => $this->l('Upload an Logo image for your shop. Dimention (173x69)'),
						'lang' => true,
					),
					array(
						'type' => 'text',
						'label' => $this->l('Logo Height'),
						'name' => 'thnxBLOCKLOGO_Height',
						'class' => 'fixed-width-md',
						'suffix' => 'pixels',
						'desc' => $this->l('Please enter a Logo Image Height value : 150.')
					),
					array(
						'type' => 'text',
						'label' => $this->l('Logo Width'),
						'name' => 'thnxBLOCKLOGO_Width',
						'class' => 'fixed-width-md',
						'suffix' => 'pixels',
						'desc' => $this->l('Please enter a Logo Image Width value : 150.')
					)
				),
				'submit' => array(
					'title' => $this->l('Save')
				)
			),
		);
		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->table =  $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->default_form_language = $lang->id;
		$helper->module = $this;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submit'.$this->name;
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'uri' => $this->getPathUri(),
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id
		);
		return $helper->generateForm(array($fields_form));
	}
	public function getConfigFieldsValues()
	{
		$languages = Language::getLanguages(false);
		$fields = array();
		foreach ($languages as $lang)
		{
			$fields['thnxBLOCKLOGO_IMG'][$lang['id_lang']] = Tools::getValue('thnxBLOCKLOGO_IMG_'.$lang['id_lang'], Configuration::get('thnxBLOCKLOGO_IMG', $lang['id_lang']));
			$fields['thnxBLOCKLOGO_DESC'][$lang['id_lang']] = Tools::getValue('thnxBLOCKLOGO_DESC_'.$lang['id_lang'], Configuration::get('thnxBLOCKLOGO_DESC', $lang['id_lang']));
		}
		$fields['thnxBLOCKLOGO_Height'] = Tools::getValue('thnxBLOCKLOGO_Height',Configuration::get('thnxBLOCKLOGO_Height'));
		$fields['thnxBLOCKLOGO_Width'] = Tools::getValue('thnxBLOCKLOGO_Width',Configuration::get('thnxBLOCKLOGO_Width'));
		return $fields;
	}
}