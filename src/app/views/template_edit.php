<?php
    function get_primitive_field($type, $name, $data, $list, $extras) {
        $out = "";
        $field_name = "data[$name]" . ($list ? "[]" : "");
        switch ($type) {
            case 'color':
                if (!$data) {
                    $data = 'rgba(0, 0, 0, 0)';
                }
                $out .= "<input type='text' data-type='color' name='$field_name' value='" . htmlentities($data, ENT_QUOTES) . "' $extras/>";
                break;
            case 'longtext':
                $out .= "<br><textarea name='$field_name' $extras>" . htmlentities($data, ENT_QUOTES) . "</textarea>";
                break;
            case 'text':
            default:
                $out .= "<input type='text' name='$field_name' value='" . htmlentities($data, ENT_QUOTES) . "' $extras/>";
        }
        return $out;
    }

    function get_field($name, $meta, $data) {
        $opt = empty($meta['optional']) ? 'required' : '';
        $required_notice = empty($meta['optional']) ? '(*)' : '(optional)';

        $out = "<label for='$name'>$meta[name] $required_notice: ";

        switch ($meta["type"]) {
            case "list":
                $out .= "<div class='jugaad-list' data-type='$meta[listType]'>"
                    . "<div class='example-input' style='display:none'>"
                    . get_primitive_field($meta["listType"], $name, "", true, "disabled")
                    . " <a onclick='removeListItem(this)' href='javascript:void(0)'>Remove</a>"
                    . "</div><div class='input-list'>"
                    . "<input type='hidden' name='data[$name]' value='' />";
                if (!is_array($data)) {
                    $data = [];
                }
                foreach ($data as $list_elem) {
                    $out .= "<div>"
                        . get_primitive_field($meta["listType"], $name, $list_elem, true, "")
                        . " <a onclick='removeListItem(this)' href='javascript:void(0)'>Remove</a>"
                        . "</div>";
                }
                $out .= "</div><a onclick='addListItem(this)' href='javascript:void(0)'>Add item</a></div>";
                break;
            case 'external':
                $out .= "<a onclick='foldNext(this)' href='javascript:void(0)'>Hide</a>"
                    . "<pre>"
                    . htmlentities(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), ENT_QUOTES)
                    . "</pre>";
                break;
            default:
                $out .= get_primitive_field($meta["type"], $name, $data, false, $opt);
        }

        return $out . "</label>";
    }
?>
<?php
    if (is_array($template_meta)):
        foreach ($template_meta as $name => $meta):
            echo get_field($name, $meta, $data[$name]);
        endforeach;
    endif;
?>
<script>
    /* exported foldNext */
    function foldNext(elem) {
        var nextElem = elem.nextElementSibling;
        var display = nextElem.style.display;
        if (display == "none") {
            nextElem.style.display = "";
            elem.innerHTML = "Hide";
        } else {
            nextElem.style.display = "none";
            elem.innerHTML = "Show";
        }
    }

    /*exported addListItem */
    function addListItem(elem) {
        var jugaadList = elem.parentNode;
        var exampleListItem = jugaadList.getElementsByClassName("example-input")[0];
        var listItem = exampleListItem.cloneNode(true);
        listItem.className = "";
        listItem.style.display = "";
        var inputElem;
        switch (jugaadList.dataset.type) {
            case "text":
                inputElem = listItem.getElementsByTagName('input')[0];
                inputElem.disabled = false;
                break;
            case "color":
                inputElem = listItem.getElementsByTagName('input')[0];
                inputElem.disabled = false;
                setupColorInput($(inputElem));
                break;
            case "longtext":
                inputElem = listItem.getElementsByTagName('textarea')[0];
                inputElem.disabled = false;
                break;
            default:

        }
        jugaadList.getElementsByClassName("input-list")[0].appendChild(listItem);
    }

    /* exported removeListItem */
    function removeListItem(elem) {
        var listItem = elem.parentNode;
        listItem.parentNode.removeChild(listItem);
    }

    /* global $ */
    function setupColorInput($elem) {
        $elem.spectrum({
            showInput: true,
            showAlpha: true,
            showPalette: true,
            palette: [
                ["transparent"],
                ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
                ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
                ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
                ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
                ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
                ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
                ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
                ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
            ],
            preferredFormat: "rgb"
        });
    }

    setupColorInput($('input[data-type=color]:enabled'));
</script>
