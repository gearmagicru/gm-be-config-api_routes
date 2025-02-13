<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Config\ApiRoutes\Model;

use Gm\Panel\Data\Model\GridModel;

/**
 * Модель данных списка API маршрутов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Config\ApiRoutes\Model
 * @since 1.0
 */
class Grid extends GridModel
{
    /**
     * Имена модулей.
     * 
     * @see Grid::beforeFetchRows()
     * 
     * @var array
     */
    protected array $moduleNames = [];

    /**
     * Имена расширений модулей.
     * 
     * @see Grid::beforeFetchRows()
     * 
     * @var array
     */
    protected array $extensionNames = [];

    /**
     * Типы.
     * 
     * @see Grid::beforeFetchRows()
     * 
     * @var array
     */
    protected array $typeNames = [];

    /**
     * {@inheritdoc}
     */
    public function getDataManagerConfig(): array
    {
        return [
            'tableName' => '{{api}}',
            'primaryKey' => 'id',
            'useAudit'   => false,
            'fields'     => [
                ['ownerName', 'direct' => 'owner_id'], // название владельца
                ['owner_id', 'alias' => 'ownerId'], // идентификатор владельца
                ['owner_type', 'alias' => 'ownerType'], // вид владельца
                ['note'], // примечание
                ['route'], // маршрут
                ['enabled'] // доступность
            ],
            'order' => [
                'route' => 'ASC'
            ],
            'resetIncrements' => ['{{api}}']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_DELETE, function ($someRecords, $result, $message) {
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            })
            ->on(self::EVENT_AFTER_SET_FILTER, function ($filter) {
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function beforeFetchRows(): void
    {
        /** @var \Gm\Session\Container $storage */
        $storage = $this->module->getStorage();
        $this->moduleNames = $storage->moduleNames;
        $this->extensionNames = $storage->extensionNames;

        // типы
        $this->typeNames = $this->t('{types}');
    }

    /**
     * {@inheritdoc}
     */
    public function prepareRow(array &$row): void
    {
        // заголовок контекстного меню записи
        $row['popupMenuTitle'] = $row['route'];

        // название
        if ($row['ownerType'] === 'module') {
            $row['ownerName'] = $this->moduleNames[$row['ownerId'] ?? ''] ?? SYMBOL_NONAME;
        } else 
        if ($row['ownerType'] === 'extension') {
            $row['ownerName'] = $this->extensionNames[$row['ownerId'] ?? ''] ?? SYMBOL_NONAME;
        } else
            $row['ownerName'] = SYMBOL_NONAME;
            
        // тип
        $row['ownerType'] = $this->typeNames[$row['ownerType']] ?? $row['ownerType'];
    }
}
