(function ($) {

    $(document).ready(function () {
        /*
         $('.forum-post').each(function() {
         if($(this).height() > 600) {
         //$(this).readmore({sectionCSS: 'display: inline-block; width: 100%; maxHeight: 500'});
         }
         });

         if($('#comment-form').length) {
         $('#comment-form button#edit-submit').on('click', function(event) {
         var button = $(this);
         button.attr('disabled', 'disabled');
         var i = 0;
         setInterval(function() {
         button.html("Saving"+Array((++i % 4)+1).join("."));
         }, 200);
         });
         }
         */

        $('.card-link').hover(function () {
            $(this).parent().find('.hidden-card').addClass('show');
        }, function () {
            $(this).parent().find('.hidden-card').removeClass('show');
        });
        $('#edit-field-color-tid option').each(function () {
            var color = $(this).html();
            if ($(this).is(':selected')) {
                $('.mana-symbol-' + color).addClass('on');
            }
        });

        if ($('.node.node-draft').length) {
            var draftnid = $('.node.node-draft').attr('id').replace('node-', '');
            var drafteruid = $('body').attr('class').split('uid-')[1].split(' ')[0];

            updateDraftViewer(draftnid);

            if ($('#no-picks').length) {
                checkForPicks(draftnid, drafteruid);
            }
        }

        if ($('#toggle-cube').length) {
            if ($('#edit-cube-card').val() != '') {
                $('#toggle-cube').attr('checked', 'checked');
            }
            $('#toggle-cube').on('click', function () {
                if ($(this).is(':checked')) {
                    $('#edit-cube-card').val(15888);
                } else {
                    $('#edit-cube-card').val('');
                }
            });
        }

        /*
         if($('.view-art').length) {
         $('.view-art ul.jcarousel').jcarousel({
         auto: .01,
         wrap: 'circular',
         animation: 7000
         });
         }
         if($('#block-views-art-block').length) {
         $('.view-art .jcarousel-item').hover(function() {
         $(this).find('.views-field-title').animate({'top': '-25px'});
         }, function() {
         $(this).find('.views-field-title').animate({'top': '0'});
         });
         }
         */
        if ($('#block-views-art-block').length) {
            $('#edit-field-color-tid option, #edit-field-color-tid-1 option').each(function () {
                if ($(this).is(':selected')) {
                    $(this).removeAttr('selected');
                }
            });
            $('.mana-symbol').each(function () {
                $(this).removeClass('on');
            });
        }
    });


    function checkForPicks(draftnid, drafteruid) {
        setTimeout(function () {
            if ($('#no-picks').length) {
                checkforpack(draftnid, drafteruid);
            }
        }, 3000);
    }

    function toggleSlidshowFilters(color) {
        if ($('.mana-symbol-' + color).hasClass('on')) {
            $('.mana-symbol-' + color).removeClass('on');
        } else {
            $('.mana-symbol-' + color).addClass('on');
        }

        $('#edit-field-color-tid option').each(function () {
            if ($(this).html() == color) {
                if ($(this).is(':selected')) {
                    $(this).removeAttr('selected');
                } else {
                    $(this).attr('selected', 'selected');
                }
            }
        });

        var colors = new Array();
        $('#edit-field-color-tid option').each(function () {
            if ($(this).is(':selected')) {
                colors.push($(this).html());
            }
        });

        if (colors.length) {
            $('#edit-field-color-tid-1 option').each(function () {
                if (colors.indexOf($(this).html()) != -1) {
                    $(this).removeAttr('selected');
                } else {
                    $(this).attr('selected', 'selected');
                }
            });

            $('#edit-submit-art').click();
            $('.color-filters').fadeOut('slow').fadeIn('slow');
        } else {
            $('#edit-reset').click();
        }
    }

    function layoutdraftseats() {
        var elems = $('div.drafter-wrapper');
        var increase = Math.PI * 2 / elems.length;
        var x = 0, y = 0, angle = 0, elem;

        for (var i = 0; i < elems.length; i++) {
            elem = elems[i];
            x = 50 * Math.cos(angle) + 70;
            y = 50 * Math.sin(angle) + 100;
            elem.style.position = 'absolute';
            elem.style.left = x + 'px';
            elem.style.top = y + 'px';
            angle += increase;
        }
        $('body').tooltip({
            selector: '[data-toggle=tooltip]'
        });
    }

    function draftpick(cardnid, packnid, draftnid, drafteruid) {
        $('.view-draft-pack.view-display-id-default').html('Submitting Pick.');
        var i = 0;
        var text = "Submitting Pick";
        var refreshIntervalId = setInterval(function () {
            $('.view-draft-pack.view-display-id-default').html('<h2>' + text + Array((++i % 4) + 1).join(".") + '</h2>');
        }, 100);

        $.ajax({
            type: "POST",
            url: '/js/draft-pick',
            dataType: "json",
            data: {
                'cardnid': cardnid,
                'packnid': packnid,
                'draftnid': draftnid,
                'drafteruid': drafteruid
            },
            success: function (data) {
                if (data.content == 'error') {
                    location.reload(true);
                } else {
                    clearInterval(refreshIntervalId);
                    $('#block-system-main').html(data.content);
                    if ($('.node.node-draft #no-picks').length) {
                        checkForPicks(data.draftnid, data.drafteruid);
                    }
                }
            },
            error: function (xmlhttp) {
            }
        });
    }

    function checkforpack(draftnid, drafteruid) {
        $.ajax({
            type: "POST",
            url: '/js/get-next-pack',
            dataType: "json",
            data: {
                'draftnid': draftnid,
                'drafteruid': drafteruid
            },
            success: function (data) {
                if (data.content == 'draft complete') {
                    $('.pack-wrapper').html(data.content);
                } else {
                    if (data.content == 'no content' || data.content == null) {
                        checkForPicks(data.draftnid, data.drafteruid);
                    } else {
                        $('.pack-wrapper').html(data.content);
                        $('.pack-wrapper').attr('id', 'node-' + data.packnid);
                        $('#no-picks').remove();
                    }
                }
            },
            error: function (xmlhttp) {
            }
        });
    }

    function updateDraftViewer(draftnid) {
        $.ajax({
            type: "POST",
            url: '/js/get-seat-viewer',
            dataType: "json",
            data: {
                'draftnid': draftnid,
            },
            success: function (data) {
                $('#draft-seats-wrapper').html(data.content);
                layoutdraftseats();
            },
            error: function (xmlhttp) {
            }
        });
        setTimeout(function () {
            updateDraftViewer(draftnid);
        }, 5000);
    }

    function picksViewer(draftid, seatid, picknumber) {
        $('#picks-wrapper').html('<h2>Getting Pick.</h2>');
        var i = 0;
        var text = "Getting Pick";
        var refreshIntervalId = setInterval(function () {
            $('#picks-wrapper').html('<h2>' + text + Array((++i % 4) + 1).join(".") + '</h2>');
        }, 100);

        $.ajax({
            type: "POST",
            url: '/js/draft-viewer',
            dataType: "json",
            data: {
                'draftid': draftid,
                'seatid': seatid,
                'picknumber': picknumber
            },
            success: function (data) {
                if (data.content != '') {
                    clearInterval(refreshIntervalId);
                    $('#picks-wrapper').html(data.content);
                    $('.nid-' + data.picknid + ' img').addClass('picked-card');
                }
            },
            error: function (xmlhttp) {
            }
        });
    }

    function quoteComment(cid) {
        var author = $('#post-' + cid + ' .username').text();
        var post = '[QUOTE user="' + author + '"]';
        post += $('#post-' + cid + ' .field-name-comment-body .field-item').text();
        post += '[/QUOTE]';
        $('#edit-comment-body textarea').val($('#edit-comment-body textarea').val() + post);
        $('html, body').animate({
            scrollTop: $("#comment-form").offset().top
        }, 100);
    }

})(jQuery);







