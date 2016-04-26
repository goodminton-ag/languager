/*
 *  Goodminton
 *
 *  Copyright 2016 Goodminton AG
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *    http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *  @package     Goodminton_Languager
 *  @author      Pierre Bernard <pierre.bernard@foodspring.com>
 *  @copyright   Copyright (c) 2016 Goodminton AG (http://goodminton.ag)
 *  @license     https://opensource.org/licenses/Apache-2.0  Apache License, Version 2.0
 */

document.addEventListener( 'DOMContentLoaded', function () {
    (function($) {
        var LanguagerAttributesManagement = {

            enabledBlock: null,
            disabledBlock: null,
            enabledList: null,
            disabledList: null,

            init: function() {

                this.enabledBlock = $('#enabled');
                this.disabledBlock = $('#disabled');
                this.enabledList = this.enabledBlock.find('ul');
                this.disabledList = this.disabledBlock.find('ul');

                var self = this;

                this.enabledBlock.droppable({
                    tolerance: 'pointer',
                    accept: '.disabled',
                    activate: function() {
                        self.activate($(this));
                    },
                    deactivate: function() {
                        self.deactivate($(this));
                    },
                    drop: function(event, ui) {
                        var element = ui.draggable;
                        element.removeClass('disabled');
                        element.addClass('enabled');
                        element.css({top: '0', left: '0'});
                        element.appendTo(self.enabledList);
                        self.checkAttribute(element);
                        self.deactivate(self.enabledBlock);
                    }
                }
                );

                this.disabledBlock.droppable({
                    tolerance: 'pointer',
                    accept: '.enabled',
                    activate: function() {
                        self.activate($(this));
                    },
                    deactivate: function() {
                        self.deactivate($(this));
                    },
                    drop: function(event, ui) {
                        var element = ui.draggable;
                        element.removeClass('enabled');
                        element.addClass('disabled');
                        element.css({top: '0', left: '0'});
                        element.appendTo(self.disabledList);
                        self.uncheckAttribute(element);
                        self.deactivate(self.disabledBlock);
                    }
                });

                $('.languager_attributes:checked').each(function () {
                    var label = $('label[for="' + $(this).attr('id') + '"]').html();
                    var element = '<li class="enabled" id="li_' + $(this).attr('id') + '">' + label + '</li>';
                    $(element).appendTo(self.enabledList);
                });

                $('.languager_attributes:not(:checked)').each(function () {
                    var label = $('label[for="' + $(this).attr('id') + '"]').html();
                    var element = '<li class="disabled" id="li_' + $(this).attr('id') + '">' + label + '</li>';
                    $(element).appendTo(self.disabledList);
                });

                this.enabledBlock.find('ul').find('li').draggable({
                    revert: true
                });
                this.disabledBlock.find('ul').find('li').draggable({
                    revert: true
                });
            },

            activate: function (block) {
                block.addClass('ui-widget-overlay');
            },

            deactivate: function (block) {
                block.removeClass('ui-widget-overlay');
            },

            checkAttribute: function (element) {
                var id = element.attr('id').replace('li_', '');
                $('#' + id).prop('checked', true);
            },

            uncheckAttribute: function (element) {
                var id = element.attr('id').replace('li_', '');
                $('#' + id).prop('checked', false);
            }
        };

        LanguagerAttributesManagement.init();

    }) (jQuery);
}, false);