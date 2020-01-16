var timer;

$(document).ready(function () {
    $(".result").on("click", function () {
        var id = $(this).attr("data-linkId");
        var url = $(this).attr("href");

        if (!id) {
            alert("data-linkId attribute not found");
        }

        increaseLinkClicks(id, url);

        return false;
    });

    var grid = $(".image-results");

    grid.on("layoutComplete", function () {
        $(".grid-item img").css("visibility", "visible");
    });
    grid.masonry({
        itemSelector: ".grid-item",
        columnWidth: 200,
        gutter: 5,
        isInitLayout: false
    });

    $("[data-fancybox]").fancybox({
        caption: function (instance, item) {
            var caption = $(this).data('caption') || '';
            var siteUrl = $(this).data('siteurl') || '';

            if ( item.type === 'image' ) {
                caption = (caption.length ? caption + '<br />' : '') + '<a href="' + item.src + '">View image</a><br><a href="' + siteUrl + '">Visit image</a>' ;
            }
            return caption;
        },

        afterShow: function (instance, item) {
            increaseImageClicks(item.src);
        }
    });
});

function increaseLinkClicks(linkId, url) {
    $.post("ajax/update-link-count.php", {linkId: linkId})
        .done(function (result) {
            if (result != '') {
                alert(result);
                return;
            }

            window.location.href = url;
        });
}

function increaseImageClicks(imageUrl) {
    $.post("ajax/update-image-count.php", {imageUrl: imageUrl})
        .done(function (result) {
            if (result != '') {
                alert(result);
                return;
            }
        });
}

function loadImage(src, className) {
    var image = $("<img>");

    image.on("load", function () {
        $("." + className + " a").append(image);
        clearTimeout(timer);

        timer = setTimeout(function () {
            $(".image-results").masonry();
        }, 500);

    });

    image.on("error", function () {
        $("." + className).remove();
        $.post("ajax/set-broken.php", {src: src});
    });

    image.attr("src", src);
}