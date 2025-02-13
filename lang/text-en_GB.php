<?php
/**
 * Этот файл является частью расширения модуля веб-приложения GearMagic.
 * 
 * Пакет английской (британской) локализации.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    '{name}'        => 'API Routing',
    '{description}' => 'Access to the functionality of modules and extensions using the API',
    '{permissions}' => [
        'any'    => ['Full access', 'View and modify routes'],
        'view'   => ['View', 'View routes'],
        'read'   => ['Read', 'Reading route entries'],
        'add'    => ['Add', 'Adding routes'],
        'edit'   => ['Edit', 'Edit route entries'],
        'delete' => ['Delete', 'Delete route entries'],
        'clear'  => ['Clear', 'Delete all route entries']
    ],

    // Grid: контекстное меню записи
    'Edit record' => 'Edit record',
    // Grid: столбцы
    'Route' => 'Route',
    'Module' => 'Module',
    'Module / Extension' => 'Module / Extension',
    'Component type' => 'Component type',
    'Component type (module, extension)' => 'Component type (module, extension)',
    'Note' => 'Note',
    '{types}' => ['extension' => 'Module extension', 'module' => 'Module'],
    // Grid: всплывающие сообщения / заголовок
    'Enabled' => 'Enabled',
    'Disabled' => 'Disabled',
    // Grid: всплывающие сообщения / текст
    'Route «{0}» enabled' => 'Route «{0}» enabled.',
    'Route «{0}» disabled' => 'Route «{0}» disabled.',

    // Form
    '{form.title}' => 'Add API route',
    '{form.titleTpl}' => 'Edit API route "{route}"',
    // Form: поля
    'Type' => 'Type',
    'Extension' => 'Extension',
    'Module extension' => 'Module extension'
];
