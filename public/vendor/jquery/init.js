/*
 * Форматирование элемента автокомплита
 */
function liFormat(row, i, num) {
    var result = row[0];
    return result;
}
/*
 * Действие при выборе элемента автокомплита
 */
function selectItem(li) {
    if (li != null) {
        location.href = "/" + li.extra[0];
    }
}

$(document).ready(
    function () {
        /*
         * Инициализация автокомплита (поиска по таблеткам)
         */
        $("#autocomplete").autocomplete(routeSearchAutocomplete, {
            delay: 10,
            minChars: 3,
            matchSubset: 3,
            matchCase: 0,
            autoFill: false,
            matchContains: 3,
            cacheLength: 100,
            cellSeparator: "||",
            selectFirst: false,
            maxItemsToShow: 30,
            // scroll: true,
            // context : this,
            onItemSelect: selectItem,
            focus: function (event, ui) {
                this.value = ui.item.label;
                $("#autocomplete").val(ui.item.label);
                event.preventDefault(); // without this: keyboard movements reset the input to ''
            },
            formatItem: liFormat
        });

        if (!!document.getElementById("shopping_cart")) {
            $.ajax({
                url: routeCartContent,
                type: 'GET',
                cache: false,
                dataType: 'html',
                data: {},
                success: function (data) {
                    data = JSON.parse(data);
                    $('#shopping_cart').html(data.html);
                }
            });
        }

    }


);
