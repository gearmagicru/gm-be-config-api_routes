<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Config\ApiRoutes\Controller;

use Gm;
use Gm\Mvc\Module\BaseModule;
use Gm\Panel\Helper\ExtGrid;
use Gm\Panel\Helper\HtmlGrid;
use Gm\Panel\Widget\TabGrid;
use Gm\Panel\Helper\HtmlNavigator as HtmlNav;
use Gm\Panel\Controller\GridController;

/**
 * Контроллер списка API маршрутов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Config\ApiRoutes\Controller
 * @since 1.0
 */
class Grid extends GridController
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
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_BEFORE_ACTION, function ($controller, $action) {
                if ($action === 'view') {
                    $this->prepareCache();
                }
            });
    }

    /**
     * Подготовить кэш. Если он не создан, создать и заполнить его.
     * 
     * @return void
     */
    public function prepareCache(): void
    {
        /** @var \Gm\Session\Container  $storage */
        $storage = $this->module->getStorage();
        // название модулей
        $storage->moduleNames = Gm::$app->modules->selectNames('name');
        // название рашсирение модулей
        $storage->extensionNames = Gm::$app->extensions->selectNames('name');
    }

    /**
     * {@inheritdoc}
     */
    public function createWidget(): TabGrid
    {
        /** @var TabGrid $tab Сетка данных (Gm.view.grid.Grid GmJS) */
        $tab = parent::createWidget();

        // столбцы (Gm.view.grid.Grid.columns GmJS)
        $tab->grid->columns = [
            ExtGrid::columnNumberer(),
            ExtGrid::columnAction(),
            [
                'text'      => ExtGrid::columnInfoIcon($this->t('Route')),
                'dataIndex' => 'route',
                'cellTip'   => HtmlGrid::tags([
                    HtmlGrid::header('{route}'),
                    HtmlGrid::fieldLabel($this->t('Component type'), '{ownerType}'),
                    HtmlGrid::fieldLabel($this->t('Module / Extension'), '{ownerName}'),
                    HtmlGrid::fieldLabel($this->t('Note'), '{note}'),
                    HtmlGrid::fieldLabel($this->t('Enabled'), HtmlGrid::tplChecked('enabled'))
                ]),
                'filter'    => ['type' => 'string'],
                'sortable'  => true,
                'width'     => 160
            ],
            [
                'text'      => '#Component type',
                'tooltip'   => '#Component type (module, extension)',
                'dataIndex' => 'ownerType',
                'sortable'  => true,
                'width'     => 200
            ],
            [
                'text'      => '#Module / Extension',
                'dataIndex' => 'ownerName',
                'cellTip'   => '{ownerName}',
                'sortable'  => true,
                'width'     => 200
            ],
            [
                'text'      => '#Note',
                'dataIndex' => 'note',
                'cellTip'   => '{note}',
                'filter'    => ['type' => 'string'],
                'sortable'  => true,
                'width'     => 220
            ],
            [
                'text'      => ExtGrid::columnIcon('g-icon-m_unlock', 'svg'),
                'tooltip'   => '#Enabled',
                'xtype'     => 'g-gridcolumn-switch',
                'collectData' =>['route'],
                'tdCls'     => 'g-mmanager-grid-td_offset',
                'dataIndex' => 'enabled',
                'filter'    => ['type' => 'boolean']
            ]
        ];

        // панель инструментов (Gm.view.grid.Grid.tbar GmJS)
        $tab->grid->tbar = [
            'padding' => 1,
            'items'   => ExtGrid::buttonGroups([
                'edit',
                'columns',
                'search',
            ], [
                'route' => $this->module->route()
            ])
        ];

        // контекстное меню записи (Gm.view.grid.Grid.popupMenu GmJS)
        $tab->grid->popupMenu = [
            'cls'        => 'g-gridcolumn-popupmenu',
            'titleAlign' => 'center',
            'width'      => 150,
            'items'      => [
                [
                    'text'        => '#Edit record',
                    'iconCls'     => 'g-icon-svg g-icon-m_edit g-icon-m_color_default',
                    'handlerArgs' => [
                        'route'   => Gm::alias('@route', '/form/view/{id}'),
                        'pattern' => 'grid.popupMenu.activeRecord'
                    ],
                    'handler' => 'loadWidget'
                ]
            ]
        ];

        // 2-й клик по строке сетки
        $tab->grid->rowDblClickConfig = [
            'allow' => true,
            'route' => $this->module->route('/form/view/{id}')
        ];
        // количество строк в сетке
        $tab->grid->store->pageSize = 50;
        // поле аудита записи
        $tab->grid->logField = 'route';
        // плагины сетки
        $tab->grid->plugins = 'gridfilters';
        // класс CSS применяемый к элементу body сетки
        $tab->grid->bodyCls = 'g-grid_background';

        // панель навигации (Gm.view.navigator.Info GmJS)
        $tab->navigator->info['tpl'] = HtmlNav::tags([
            HtmlNav::header('{route}'),
            HtmlNav::fieldLabel($this->t('Component type'), '{ownerType}'),
            HtmlNav::fieldLabel($this->t('Module / Extension'), '{ownerName}'),
            HtmlNav::tplIf(
                'note',
                HtmlNav::fieldLabel($this->t('Note'), '{note}'),
                ''
            ),
            HtmlNav::fieldLabel(
                ExtGrid::columnIcon('g-icon-m_unlock', 'svg') . ' ' . $this->t('Enabled'), 
                HtmlNav::tplChecked('enabled')
            ),
            '<br>',
            HtmlNav::widgetButton(
                $this->t('Edit record'),
                ['route' => Gm::alias('@route', '/form/view/{id}'), 'long' => true],
                ['title' => $this->t('Edit record')]
            )
        ]);

        $tab
            ->addCss('/grid.css')
            ->addRequire('Gm.view.grid.column.Switch');
        return $tab;
    }
}
