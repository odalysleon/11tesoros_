!function(t){function r(o){if(n[o])return n[o].exports;var e=n[o]={exports:{},id:o,loaded:!1};return t[o].call(e.exports,e,e.exports,r),e.loaded=!0,e.exports}var n={};return r.m=t,r.c=n,r.p="",r(0)}([function(t,r,n){t.exports=n(1)},function(t,r,n){"use strict";n(2)},function(t,r){}]);
$(function () {

    $(document).on('hover', '#nav-sidebar li.has_submenu', function () {
        var submenu = $(this).find('.submenu').eq(0);

        var marginTop = $(this).position().top + parseInt($('header#header > nav').height());
        if ((marginTop + parseInt(submenu.height())) > parseInt($(window).height()))
            marginTop -= (marginTop + parseInt(submenu.height()) - parseInt($(window).height())) + 10;

        if ($('body').hasClass('page-sidebar-closed'))
            marginTop += 34;

        setTimeout(function () {
            submenu.attr('style', 'position: fixed !important; top: ' + marginTop + 'px !important');
        }, 50);
    });

});