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
        !Configuration::updateValue('OPACITY_IMAGE', 0) ||
        !Configuration::updateValue('REPEAT_IMAGE', 0) ||
        !Configuration::updateValue('IMAGE_SIZE', 100) ||
        !Configuration::updateValue('CATEGORIE_IMAGES', 0) ||
        !Configuration::updateValue('PRODUCTS_IMAGES', 0) 
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
        !Configuration::deleteByName('OPACITY_IMAGE') ||
        !Configuration::deleteByName('REPEAT_IMAGE') ||
        !Configuration::deleteByName('IMAGE_SIZE') ||
        !Configuration::deleteByName('CATEGORIE_IMAGES') ||
        !Configuration::deleteByName('PRODUCTS_IMAGES')
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
            && Validate::isFileName(Tools::getValue('UPLOAD_IMAGE')) && Validate::isInt(Tools::getValue('IMAGE_SIZE'))
            && Validate::isInt(Tools::getValue('OPACITY_IMAGE')) || Validate::isFloat(Tools::getValue('OPACITY_IMAGE'))
            )
            {
                //Switch
                Configuration::updateValue('ACTIVE_OPTION',Tools::getValue('ACTIVE_OPTION'));
                Configuration::updateValue('REPEAT_IMAGE',Tools::getValue('REPEAT_IMAGE'));
                Configuration::updateValue('CATEGORIE_IMAGES',Tools::getValue('CATEGORIE_IMAGES'));
                Configuration::updateValue('PRODUCTS_IMAGES',Tools::getValue('PRODUCTS_IMAGES'));

                //text
                Configuration::updateValue('OPACITY_IMAGE',Tools::getValue('OPACITY_IMAGE'));
                Configuration::updateValue('IMAGE_SIZE',Tools::getValue('IMAGE_SIZE'));

                //Image file
                
                Configuration::updateValue('UPLOAD_IMAGE',Tools::getValue('UPLOAD_IMAGE'));
                
                if($_FILES["UPLOAD_IMAGE"]["error"] == UPLOAD_ERR_OK)
                {
                    $targetDir = _PS_MODULE_DIR_.$this->name.'/views/img/';
        
                    if(!file_exists($targetDir))
                    {
                        mkdir($targetDir,0755, true);
                    }
        
                    $targetFile = $targetDir.basename($_FILES["UPLOAD_IMAGE"]["name"]);
        
                    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
                    if($imageFileType == 'png' || $imageFileType == 'jpg')
                    {
                        move_uploaded_file($_FILES["UPLOAD_IMAGE"]["tmp_name"], $targetFile);
                    }
                    else
                    {
                        return $this->displayError($this->l('Only PNG and JPG files are allowed'));
                    }
        
                }
                else
                {
                    return $this->displayError($this->l('Error during the file upload'));
                }
                
                return $this->displayConfirmation('Well recorded!');
            }
            
        }
    }

    public function renderForm()
    {
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
                ],
                [
                    'type' => 'text',
                        'label' => $this->l('Choose opacity'),
                        'name' => 'OPACITY_IMAGE',
                        'descr' => $this->l('The value must be between 0 and 1')
                ],
                [
                    'type' => 'text',
                        'label' => $this->l('Image size'),
                        'name' => 'IMAGE_SIZE',
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
        $helper->fields_value['OPACITY_IMAGE'] = Configuration::get('OPACITY_IMAGE');
        $helper->fields_value['REPEAT_IMAGE'] = Configuration::get('REPEAT_IMAGE');
        $helper->fields_value['IMAGE_SIZE'] = Configuration::get('IMAGE_SIZE');
        $helper->fields_value['CATEGORIE_IMAGES'] = Configuration::get('CATEGORIE_IMAGES');
        $helper->fields_value['PRODUCTS_IMAGES'] = Configuration::get('PRODUCTS_IMAGES');

        return $helper->generateForm($field_form);
    }
}