
var initSlider = function() {
    $('.cm-range-slider').each(function () {
        var that = this;
        if ($(that).find('.slider-track').length > 0) {
            $(that).find('input.integer-slider').slider('destroy');
        }
        $(that).find('input.integer-slider').slider({});
        $(that).find('input.integer-slider').off('slideStop').on('slideStop', function (ev) {
            $(that).find('input.min-value').val(ev.value[0]);
            $(that).find('input.max-value').val(ev.value[1]);
        })
    });
}


$Behavior.dd_slider_init = initSlider;
initSlider();