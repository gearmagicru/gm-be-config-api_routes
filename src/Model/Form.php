<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Config\ApiRoutes\Model;

use Gm;
use Gm\Panel\Helper\ExtField;
use Gm\Panel\Data\Model\FormModel;

/**
 * Модель данных профиля API маршрута.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Config\ApiRoutes\Model
 * @since 1.0
 */
class Form extends FormModel
{
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
                ['id'],
                ['note'], // примечание
                ['route', 'label' => 'Route'], // маршрут
                ['enabled'], // доступность
                ['owner_id', 'alias' => 'ownerId'],
                ['owner_type', 'alias' => 'ownerType', 'label' => 'Type']
            ],
            // правила форматирования полей
            'formatterRules' => [
                [[
                    'route', 'note', 'ownerType'
                ], 'safe']
            ],
            // правила валидации полей
            'validationRules' => [
                [['route'], 'notEmpty'],
                // маршрут
                [
                    'route',
                    'between',
                    'max' => 255, 'type' => 'string'
                ],
                // примечание
                [
                    'note',
                    'between',
                    'max' => 255, 'type' => 'string'
                ]
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        $this
            ->on(self::EVENT_AFTER_SAVE, function ($isInsert, $columns, $result, $message) {
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                // обновить список
                $controller->cmdReloadGrid();
            })
            ->on(self::EVENT_AFTER_DELETE, function ($result, $message) {
                /** @var \Gm\Panel\Controller\GridController $controller */
                $controller = $this->controller();
                // всплывающие сообщение
                $this->response()
                    ->meta
                        ->cmdPopupMsg($message['message'], $message['title'], $message['type']);
                // обновить список
                $controller->cmdReloadGrid();
            });
    }

    /**
     * {@inheritdoc}
     */
    public function beforeLoad(array &$data): void
    {
        ExtField::checkboxValue($data, 'enabled'); // доступность
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate(bool $isValid): bool
    {
        if ($isValid) {
            // если выбран модуль
            if ($this->ownerType === 'module') {
                $this->ownerId = Gm::$app->request->getPost('moduleId', 0, 'int');
                if (empty($this->ownerId)) {
                    $this->addError($this->errorFormatMsg(Gm::t('app', 'Value is required and can\'t be empty'), 'Module'));
                    return false;
                }
            // если выбрано расширение модуля
            } else
            if ($this->ownerType === 'extension') {
                $this->ownerId = Gm::$app->request->getPost('extensionId', 0, 'int');
                if (empty($this->ownerId)) {
                    $this->addError($this->errorFormatMsg(Gm::t('app', 'Value is required and can\'t be empty'), 'Module extension'));
                    return false;
                }
            // если выбор отсутствует
            } else {
                $this->addError($this->errorFormatMsg(Gm::t('app', 'Value is required and can\'t be empty'), 'Type'));
                return false;
            }
        }
        return $isValid;
    }

    /**
     * {@inheritdoc}
     */
    public function processing(): void
    {
        parent::processing();

        // если выбран модуль
        if ($this->ownerType === 'module') {
            $this->moduleId = $this->ownerId;
        // если выбрано расширение модуля
        } else
        if ($this->ownerType === 'extension') {
            $this->extensionId = $this->ownerId;
        }
    }
}
