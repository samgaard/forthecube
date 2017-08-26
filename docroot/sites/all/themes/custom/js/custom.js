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

            //console.log('drafteruid = ' + drafteruid);
            updateDraftViewer(draftnid, drafteruid);


            if ($('#no-picks').length && !$('body').hasClass('rochester')) {
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

})(jQuery);

function checkForPicks(draftnid, drafteruid) {
    setTimeout(function () {
        if (jQuery('#no-picks').length && !$('body').hasClass('rochester')) {
            checkforpack(draftnid, drafteruid);
        }
    }, 3000);
}

function toggleSlidshowFilters(color) {
    if (jQuery('.mana-symbol-' + color).hasClass('on')) {
        jQuery('.mana-symbol-' + color).removeClass('on');
    } else {
        jQuery('.mana-symbol-' + color).addClass('on');
    }

    jQuery('#edit-field-color-tid option').each(function () {
        if (jQuery(this).html() == color) {
            if (jQuery(this).is(':selected')) {
                jQuery(this).removeAttr('selected');
            } else {
                jQuery(this).attr('selected', 'selected');
            }
        }
    });

    var colors = new Array();
    jQuery('#edit-field-color-tid option').each(function () {
        if (jQuery(this).is(':selected')) {
            colors.push(jQuery(this).html());
        }
    });

    if (colors.length) {
        jQuery('#edit-field-color-tid-1 option').each(function () {
            if (colors.indexOf(jQuery(this).html()) != -1) {
                jQuery(this).removeAttr('selected');
            } else {
                jQuery(this).attr('selected', 'selected');
            }
        });

        jQuery('#edit-submit-art').click();
        jQuery('.color-filters').fadeOut('slow').fadeIn('slow');
    } else {
        jQuery('#edit-reset').click();
    }
}

function layoutdraftseats() {
    var elems = jQuery('div.drafter-wrapper');
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
    jQuery('body').tooltip({
        selector: '[data-toggle=tooltip]'
    });
}

function draftpick(cardnid, packnid, draftnid, drafteruid) {
    jQuery('.view-draft-pack.view-display-id-default').html('Submitting Pick.');
    var i = 0;
    var text = "Submitting Pick";
    var refreshIntervalId = setInterval(function () {
        jQuery('.view-draft-pack.view-display-id-default').html('<h2>' + text + Array((++i % 4) + 1).join(".") + '</h2>');
    }, 100);

    jQuery.ajax({
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
                //console.log(data.content);
                clearInterval(refreshIntervalId);
                jQuery('#block-system-main').html(data.content);
                //if (jQuery('.node.node-draft #no-picks').length && !$('body').hasClass('rochester')) {
                //console.log('calling updateDraftViewer & checkForPicks with data.draftnid = ' + data.draftnid + ' and data.drafteruid = ' + data.drafteruid);
                updateDraftViewer(data.draftnid, data.drafteruid);
                checkForPicks(data.draftnid, data.drafteruid);
                //}
            }
        },
        error: function (xmlhttp) {
        }
    });
}

function checkforpack(draftnid, drafteruid) {
    jQuery.ajax({
        type: "POST",
        url: '/js/get-next-pack',
        dataType: "json",
        data: {
            'draftnid': draftnid,
            'drafteruid': drafteruid
        },
        success: function (data) {
            if (data.content == 'draft complete') {
                jQuery('.pack-wrapper').html(data.content);
            } else {
                if (data.content == 'no content') {
                    checkForPicks(data.draftnid, data.drafteruid);
                } else {
                    jQuery('.pack-wrapper').html(data.content);
                    jQuery('.pack-wrapper').attr('id', 'node-' + data.packnid);
                    jQuery('#no-picks').remove();
                }
            }
        },
        error: function (xmlhttp) {
        }
    });
}

function updateDraftViewer(draftnid, drafteruid) {
    //console.log('in updateDraftViewer with draftnid = ' + draftnid + ' and drafteruid = ' + drafteruid);

    jQuery.ajax({
        type: "POST",
        url: '/js/get-seat-viewer',
        dataType: "json",
        data: {
            'draftnid': draftnid,
            'drafteruid': drafteruid
        },
        success: function (data) {
            //console.log('in success of updateDraftViewer with data = ' + draftnid);
            if (jQuery('#pick-list-div').length && !jQuery('.view-draft-pack .item-list ul li').length) {
                if (jQuery('#draft-seat-default').length) {
                    var seatnumber = jQuery('#draft-seat-default').html();
                    jQuery('#seat_number').val(seatnumber);
                } else {
                    var seatnumber = 1;
                }
                picksViewer(draftnid, seatnumber, 1);
            }
            if (data.drafteruid == drafteruid && !jQuery('.pickable').length) {
                checkforpack(draftnid, drafteruid);
                //console.log('calling checkforpack with data.draftnid = ' + draftnid + ' and data.drafteruid = ' + data.drafteruid);
            } else {
                //console.log('setting timeout to check seats again');
                setTimeout(function () {
                    updateDraftViewer(draftnid, drafteruid);
                }, 5000);
            }

            jQuery('#draft-seats-wrapper').html(data.content);
            layoutdraftseats();
        },
        error: function (xmlhttp) {
        }
    });
}

function picksViewer(draftid, seatid, picknumber) {
    if (jQuery('.pick-count-wrapper').html() != '0') {
        jQuery('#pick-list-div').html('<h2>Getting Pick.</h2>');
        var i = 0;
        var text = "Getting Pick";
        var refreshIntervalId = setInterval(function () {
            jQuery('#pick-list-div').html('<h2>' + text + Array((++i % 4) + 1).join(".") + '</h2>');
        }, 100);
    }

    jQuery.ajax({
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
                jQuery('#pick-list-div').html(data.content);
                jQuery('.nid-' + data.picknid + ' img').addClass('picked-card');
                if (jQuery('.view-draft-pack .item-list ul li')) {
                    jQuery('html, body').animate({
                        //scrollTop: jQuery("#picks-wrapper").offset().top
                    }, 500);
                }
            }
        },
        error: function (xmlhttp) {
        }
    });
}

function quoteComment(cid) {
    var author = jQuery('#post-' + cid + ' .username').text();
    var post = '[QUOTE user="' + author + '"]';
    post += jQuery('#post-' + cid + ' .field-name-comment-body .field-item').text();
    post += '[/QUOTE]';
    jQuery('#edit-comment-body textarea').val(jQuery('#edit-comment-body textarea').val() + post);
    jQuery('html, body').animate({
        scrollTop: jQuery("#comment-form").offset().top
    }, 100);
}