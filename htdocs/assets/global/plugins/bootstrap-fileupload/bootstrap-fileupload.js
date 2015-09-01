/* ===========================================================
 * bootstrap-fileupload.js j1a
 * http://jasny.github.com/bootstrap/javascript.html#fileupload
 * ===========================================================
 * Copyright 2012 Jasny BV, Netherlands.
 *
 * Licensed under the Apache License, Version 2.0 (the "License")
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */

! function($) {

    "use strict"; // jshint ;_

    /* INPUTMASK PUBLIC CLASS DEFINITION
     * ================================= */

    var Fileupload = function(element, options) {
        this.$element = $(element)
        this.type = this.$element.data('uploadtype') || (this.$element.find('.thumbnail').length > 0 ? "image" : "file")

        this.$input = this.$element.find(':file')
        if (this.$input.length === 0) return

        this.name = this.$input.attr('name') || options.name

        this.$hidden = this.$element.find(':hidden[name="' + this.name + '"]')
        if (this.$hidden.length === 0) {
            this.$hidden = $('<input type="hidden" />')
            this.$element.prepend(this.$hidden)
        }

        this.$preview = this.$element.find('.fileupload-preview')
        var height = this.$preview.css('height')
        if (this.$preview.css('display') != 'inline' && height != '0px' && height != 'none') this.$preview.css('line-height', height)

        this.$remove = this.$element.find('[data-dismiss="fileupload"]')

        this.listen()
    }

    Fileupload.prototype = {

        listen: function() {
            this.$input.on('change.fileupload', $.proxy(this.change, this))
            if (this.$remove) this.$remove.on('click.fileupload', $.proxy(this.clear, this))
        },

        change: function(e, invoked) {
            var file = e.target.files !== undefined ? e.target.files[0] : {
                name: e.target.value.replace(/^.+\\/, '')
            }
            var typeArray = [
                'image/jpg', 'image/jpeg', 'image/png', 'image/gif'
            ];

            //判断图片类型
            if ($.inArray(file.type, typeArray)<0) {
                alert('图片格式必须为jpg、jpeg、png、gif');
                return;
            }
            if (!file || invoked === 'clear') return
            if (file.size > 2097152) {
                alert('图片大小必须小于2M');
                return
            }

            this.$hidden.val('')
            this.$hidden.attr('name', '')
            this.$input.attr('name', this.name)

            if (this.type === "image" && this.$preview.length > 0 && (typeof file.type !== "undefined" ? file.type.match('image.*') : file.name.match('\\.(gif|png|jpe?g)$')) && typeof FileReader !== "undefined") {
                var reader = new FileReader()
                var preview = this.$preview
                var element = this.$element

                reader.onload = function(e) {
                    preview.html('<img src="' + e.target.result + '" ' + (preview.css('max-height') != 'none' ? 'style="max-height: ' + preview.css('max-height') + ';"' : '') + ' />')
                    element.addClass('fileupload-exists').removeClass('fileupload-new')
                }

                reader.readAsDataURL(file)
            } else {
                this.$preview.text(file.name)
                this.$element.addClass('fileupload-exists').removeClass('fileupload-new')
            }
        },

        clear: function(e) {
            this.$hidden.val('')
            this.$hidden.attr('name', this.name)
            this.$input.attr('name', '')

            this.$preview.html('')
            this.$element.addClass('fileupload-new').removeClass('fileupload-exists')

            this.$input.trigger('change', ['clear'])

            e.preventDefault()
            return false
        }
    }


    /* INPUTMASK PLUGIN DEFINITION
     * =========================== */

    $.fn.fileupload = function(options) {
        return this.each(function() {
            var $this = $(this),
                data = $this.data('fileupload')
            if (!data) $this.data('fileupload', (data = new Fileupload(this, options)))
        })
    }

    $.fn.fileupload.Constructor = Fileupload


    /* INPUTMASK DATA-API
     * ================== */

    $(function() {
        $('body').on('click.fileupload.data-api', '[data-provides="fileupload"]', function(e) {
            var $this = $(this)
            if ($this.data('fileupload')) return
            $this.fileupload($this.data())

            if ($(e.target).data('dismiss') == 'fileupload') $(e.target).trigger('click.fileupload')
        })
    })


    $.fn.serializeObject = function() {
        var o = {};
        var a = this.serializeArray();
        $.each(a, function() {
            if (o[this.name] !== undefined) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
}(window.jQuery)
