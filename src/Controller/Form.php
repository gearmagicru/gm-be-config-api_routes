<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Config\ApiRoutes\Controller;

use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Helper\ExtCombo;
use Gm\Panel\Widget\EditWindow;
use Gm\Panel\Controller\FormController;

/**
 * Контроллер формы API маршрута.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Config\ApiRoutes\Controller
 * @since 1.0
 */
class Form extends FormController
{
    /**
     * {@inheritdoc}
     * 
     * @var BaseModule|\Gm\Backend\Config\ApiRoutes\Extension
     */
    public BaseModule $module;

    /**
     * {@inheritdoc}
     */
    public function createWidget(): EditWindow
    {
        /** @var EditWindow $window */
        $window = parent::createWidget();

        // панель формы (Gm.view.form.Panel GmJS)
        $window->form->router->route = $this->module->route('/form');
        $window->form->autoScroll = true;
        $window->form->controller = 'gm-config-api_routes-form';
        $window->form->bodyPadding = 10;
        $window->form->defaults = [
            'labelAlign' => 'right',
            'labelWidth' => 120
        ];
        $window->form->loadJSONFile('/form', 'items', [
            '@moduleCombobox'    => ExtCombo::modules('#Module', 'moduleId', 'rowId', ['id' => 'g-apirouting__module']),
            '@extensionCombobox' => ExtCombo::extensions('#Extension', 'extensionId', 'rowId', ['id' => 'g-apirouting__extension', 'hidden' => true])
        ]);

        // окно компонента (Ext.window.Window Sencha ExtJS)
        $window->width = 520;
        $window->autoHeight = true;
        $window->resizable = false;
        $window
            ->setNamespaceJS('Gm.be.config.api_routes')
            ->addRequire('Gm.be.config.api_routes.FormController');
        return $window;
    }
}
