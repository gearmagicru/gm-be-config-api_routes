/*!
 * Контроллер представления виджета формы.
 * Расширение "API Маршрутизация".
 * Модуль "Конфигурация".
 * Copyright 2015 Вeб-студия GearMagic. Anton Tivonenko <anton.tivonenko@gmail.com>
 * https://gearmagic.ru/license/
 */

Ext.define('Gm.be.config.api_routes.FormController', {
    extend: 'Gm.view.form.PanelController',
    alias: 'controller.gm-config-api_routes-form',

    /**
     * Выбор модуля.
     * @param {Ext.form.field.Checkbox} me
     * @param {Boolean} value Значение.
     */
    onCheckTypeModule: function (me, value) {
        Ext.getCmp('g-apirouting__module').hide();
        Ext.getCmp('g-apirouting__extension').show();
    },

    /**
     * Выбор расширения модуля.
     * @param {Ext.form.field.Checkbox} me
     * @param {Boolean} value Значение.
     */
    onCheckTypeExtension: function (me, value) {
        Ext.getCmp('g-apirouting__extension').hide();
        Ext.getCmp('g-apirouting__module').show();
    }
});
