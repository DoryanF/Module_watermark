<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class WaterMark extends Module
{
    public function __construct()
    {
        $this->name = 'watermark';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Doryan Fourrichon';
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];
        
        //récupération du fonctionnement du constructeur de la méthode __construct de Module
        parent::__construct();
        $this->bootstrap = true;

        $this->displayName = $this->l('Watermark');
        $this->description = $this->l('The module that adds a watermark to the images');

        $this->confirmUninstall = $this->l('Do you want to delete this module');
    }

    public function install()
    {
        if (!parent::install() ||
        !Configuration::updateValue('ACTIVE_OPTION', 0) ||
        !Configuration::updateValue('UPLOAD_IMAGE', '') ||
        !Configuration::updateValue('CURRENT_IMAGE', '') ||
        !Configuration::updateValue('OPACITY_IMAGE', 0) ||
        !Configuration::updateValue('REPEAT_IMAGE', 0) ||
        !Configuration::updateValue('CATEGORIE_IMAGES', 0) ||
        !Configuration::updateValue('PRODUCTS_IMAGES', 0) ||
        !Configuration::updateValue('HOME_IMAGES', 0) ||
        !$this->registerHook('DisplayHeader')
        ) {
            return false;
        }
            return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
        !Configuration::deleteByName('ACTIVE_OPTION') ||
        !Configuration::deleteByName('UPLOAD_IMAGE') ||
        !Configuration::deleteByName('CURRENT_IMAGE') ||
        !Configuration::deleteByName('OPACITY_IMAGE') ||
        !Configuration::deleteByName('REPEAT_IMAGE') ||
        !Configuration::deleteByName('CATEGORIE_IMAGES') ||
        !Configuration::deleteByName('PRODUCTS_IMAGES') ||
        !Configuration::deleteByName('HOME_IMAGES') ||
        !$this->unregisterHook('DisplayHeader')
        ) {
            return false;
        }
        return true;
    }
    
    public function getContent()
    {

        return $this->postProcess().$this->renderForm();
    }

    public function postProcess()
    {
        if(Tools::isSubmit('saving'))
        {
            if(Validate::isBool(Tools::getValue('ACTIVE_OPTION')) && Validate::isBool(Tools::getValue('REPEAT_IMAGE')) 
            && Validate::isBool(Tools::getValue('CATEGORIE_IMAGES')) && Validate::isBool(Tools::getValue('PRODUCTS_IMAGES')) 
            && Validate::isBool(Tools::getValue('HOME_IMAGES')) &&Validate::isFileName(Tools::getValue('UPLOAD_IMAGE')) 
            && Validate::isInt(Tools::getValue('OPACITY_IMAGE')) || Validate::isFloat(Tools::getValue('OPACITY_IMAGE'))
            )
            {
                //Switch
                Configuration::updateValue('ACTIVE_OPTION',Tools::getValue('ACTIVE_OPTION'));
                Configuration::updateValue('REPEAT_IMAGE',Tools::getValue('REPEAT_IMAGE'));
                Configuration::updateValue('CATEGORIE_IMAGES',Tools::getValue('CATEGORIE_IMAGES'));
                Configuration::updateValue('PRODUCTS_IMAGES',Tools::getValue('PRODUCTS_IMAGES'));
                Configuration::updateValue('HOME_IMAGES',Tools::getValue('HOME_IMAGES'));

                //text
                Configuration::updateValue('OPACITY_IMAGE',Tools::getValue('OPACITY_IMAGE'));
                

                //Image file
                Configuration::updateValue('CURRENT_IMAGE',Configuration::get('UPLOAD_IMAGE'));
                
                $uploadImage = $_FILES['UPLOAD_IMAGE'];
                
                if($uploadImage["error"] == UPLOAD_ERR_OK)
                {
                    $targetDir = _PS_MODULE_DIR_.$this->name.'/views/img/';
                    
                    if(!file_exists($targetDir))
                    {
                        mkdir($targetDir,0755, true);
                    }
                    
                    $targetFile = $targetDir.basename($uploadImage["name"]);
                    
                    // cas n° 1 
                    // si la variable de Files est vide alors la variable Files est égale à La variable de configuration.
                    if(empty($uploadImage["name"]))
                    {
                        $uploadImage["name"] == Configuration::get('UPLOAD_IMAGE');
                    }
                    // cas numero 2
                    // si l'image uplodé est la même que celle dans la bdd 
                    if ($uploadImage["name"] == Configuration::get('UPLOAD_IMAGE')) {
                        
                    }
                    else
                    {
                        $oldImagePath = $targetDir . Configuration::get('UPLOAD_IMAGE');
                        if (file_exists($oldImagePath)) {
                            unlink($oldImagePath);
                        }
                        else
                        {
                            move_uploaded_file($uploadImage["tmp_name"], $targetFile);
                        }
    
                        // Déplacer la nouvelle image vers le répertoire
                        move_uploaded_file($uploadImage["tmp_name"], $targetFile);
    
                        // Mettre à jour la configuration avec le nouveau nom d'image
                        Configuration::updateValue('UPLOAD_IMAGE', $uploadImage["name"]);
                    }
                    
                    return $this->displayConfirmation('Well recorded!');
                }
                else
                {
                    if(empty(Tools::getValue('CURRENT_IMAGE')))
                    {
                        return $this->displayError($this->l('Please choose an image file.'));
                    }

                    Configuration::updateValue('UPLOAD_IMAGE', Configuration::get('CURRENT_IMAGE'));
                    return $this->displayConfirmation('Well recorded!');
                }
        
                
            }
            
        }
    }

    public function renderForm()
    {
        $options = array(
            array('id'=>0.1, 'name' => '10%'),
            array('id'=>'0.2', 'name' => '20%'),
            array('id'=>'0.3', 'name' => '30%'),
            array('id'=>'0.4', 'name' => '40%'),
            array('id'=>'0.5', 'name' => '50%'),
            array('id'=>'0.6', 'name' => '60%'),
            array('id'=>'0.7', 'name' => '70%'),
            array('id'=>'0.8', 'name' => '80%'),
            array('id'=>'0.9', 'name' => '90%'),
            array('id'=>'1', 'name' => '100%'),
        );


        $field_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'switch',
                        'label' => $this->l('Active module ?'),
                        'name' => 'ACTIVE_OPTION',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                ],
                [
                    'type' => 'file',
                        'label' => $this->l('Url image'),
                        'name' => 'UPLOAD_IMAGE',
                        'display_image' => true,
                ],
                [
                    'type' => 'hidden',
                    'name' => 'CURRENT_IMAGE',
                    'value' => Configuration::get('UPLOAD_IMAGE')
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Choose opacity'),
                    'name' => 'OPACITY_IMAGE',
                    'options' => array(
                        'query' => $options,
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ],
                [
                    'type' => 'switch',
                        'label' => $this->l('Repeat watermark ?'),
                        'name' => 'REPEAT_IMAGE',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                ],
                [
                    'type' => 'switch',
                        'label' => $this->l('Display on product page ?'),
                        'name' => 'PRODUCTS_IMAGES',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                ],
                [
                    'type' => 'switch',
                        'label' => $this->l('Display on categorie product ?'),
                        'name' => 'CATEGORIE_IMAGES',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                ],
                [
                    'type' => 'switch',
                        'label' => $this->l('Display on home page products ?'),
                        'name' => 'HOME_IMAGES',
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'label2_on',
                                'value' => 1,
                                'label' => $this->l('Yes')
                            ),
                            array(
                                'id' => 'label2_off',
                                'value' => 0,
                                'label' => $this->l('No')
                            )
                        )
                ],
                
            ],
            'submit' => [
                'title' => $this->l('save'),
                'class' => 'btn btn-primary',
                'name' => 'saving'
            ]
        ];

        $helper = new HelperForm();
        $helper->module  = $this;
        $helper->name_controller = $this->name;
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value['ACTIVE_OPTION'] = Configuration::get('ACTIVE_OPTION');
        $helper->fields_value['UPLOAD_IMAGE'] = Configuration::get('UPLOAD_IMAGE');
        $helper->fields_value['CURRENT_IMAGE'] = Configuration::get('CURRENT_IMAGE');
        $helper->fields_value['OPACITY_IMAGE'] = Configuration::get('OPACITY_IMAGE');
        $helper->fields_value['REPEAT_IMAGE'] = Configuration::get('REPEAT_IMAGE');
        $helper->fields_value['CATEGORIE_IMAGES'] = Configuration::get('CATEGORIE_IMAGES');
        $helper->fields_value['PRODUCTS_IMAGES'] = Configuration::get('PRODUCTS_IMAGES');
        $helper->fields_value['HOME_IMAGES'] = Configuration::get('HOME_IMAGES');

        return $helper->generateForm($field_form);
    }

    public function hookDisplayHeader()
    {
        $link = new Link();
        $imagePath = $link->getBaseLink().'/modules/'.$this->name.'/views/img/'.Configuration::get('UPLOAD_IMAGE');
        $controller = $this->context->controller->php_self;

        if(Configuration::get('ACTIVE_OPTION') == 1)
        {
            if (
                (Configuration::get('CATEGORIE_IMAGES') == 1 && $controller == 'category') ||
                (Configuration::get('PRODUCTS_IMAGES') == 1 && $controller == 'product') ||
                (Configuration::get('HOME_IMAGES') == 1 && $controller == 'index')
            ) {
                $this->smarty->assign(array(
                    'img' => $imagePath,
                    'img_size' => Configuration::get('IMAGE_SIZE').'%',
                    'img_opacity' => Configuration::get('OPACITY_IMAGE'),
                    'img_repeat' => Configuration::get('REPEAT_IMAGE')
                ));

                return $this->display(__FILE__, '/views/templates/hook/displayHeader.tpl');
            }

        }
    }
}