/*
 * jQuery File Upload Plugin JS Example 8.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */

$(function () {
    'use strict';

    var $fileupload
        , $selectAll
        , $result
        ;

    $fileupload = $('#fileupload');
    $selectAll = $('#select-all');
    $result = $('#result');

    $fileupload.fileupload({
        singleFileUploads: false,
        done: function (e, data) {
            var $context
                , files
                , line
                , i;

            data.context.fadeOut(300, function () {
                $(this).remove();
            });

            for (i = 0; i < data.result.files.length; i++) {
                line = data.result.files[i].url;
                if ($result.text().length > 0) {
                    line = "\n" + line;
                }
                $result.append(line);
            }
        }
    });

    $selectAll.click(function (e) {
        e.preventDefault();
        $result.focus(function () {
            var $this = $(this);
            $this.select();

            // Work around Chrome's little problem
            $this.mouseup(function () {
                // Prevent further mouseup intervention
                $this.unbind("mouseup");
                return false;
            });
        });

        $result.focus();
    });
    $result.text('');
});
