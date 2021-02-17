import ajax from "@codexteam/ajax";

export class editorJsHelper {
    constructor() {}

    onSelectFile(Tool = null) {
        //if (!typeof this instanceof Image) throw "Bad context";
        Tool = Tool ? Tool : this;
        var inlineImageField = document.querySelector('div[id*="inline_image"] a');
        inlineImageField.click();

        var id = document.querySelector("input[id*=inline_image]").getAttribute("id");
        jQuery("#" + id).one("change", function (event) {
            console.log("call onChange select file");
            var id = jQuery(this).val();

            var upload = ajax
                .post({
                    url: "/admin/media/block",
                    data: Object.assign({
                        id: id,
                    }),
                    type: ajax.contentType.JSON,
                })
                .then((response) => {
                    //Tool.ui.showPreloader(response.body.file.url);
                    //Tool.ui.fillImage(response.body.file.url);
                    if (Tool.onFileLoading) Tool.onFileLoading();
                    Tool.onUpload(response.body);
                })
                .catch((error) => {
                    console.log(Tool);
                    Tool.uploadingFailed(error);
                });
        });
    }

    onUploadFile(Tool = null) {
        //if (!typeof this instanceof Image) throw "Bad context";
        Tool = Tool ? Tool : this;
        var inlineImageField = document.querySelector('div[id*="inline_image"] a:nth-child(2)');
        inlineImageField.click();

        var id = document.querySelector("input[id*=inline_image]").getAttribute("id");
        jQuery("#" + id).one("change", function (event) {
            var id = jQuery(this).val();

            var upload = ajax
                .post({
                    url: "/admin/media/block",
                    data: Object.assign({
                        id: id,
                    }),
                    type: ajax.contentType.JSON,
                })
                .then((response) => {
                    if (Tool.onFileLoading) Tool.onFileLoading();
                    Tool.onUpload(response.body);
                })
                .catch((error) => {
                    Tool.uploadingFailed(error);
                });
        });
    }

    toggleEditorJs(editorId) {
        var editorJsInput = document.querySelector("input[data-editorjs]");
        var textareaInput = document.querySelector("textarea[data-editorjs]");
        var elementToReplace = editorJsInput ? editorJsInput : textareaInput;

        console.log(document.getElementById(editorId));
        document.getElementById(editorId).style.display = editorJsInput ? "none" : "block";

        var replaceElement = document.createElement(editorJsInput ? "textarea" : "input");

        for (var i = 0, l = elementToReplace.attributes.length; i < l; ++i) {
            var nodeName = elementToReplace.attributes.item(i).nodeName;
            var nodeValue = elementToReplace.attributes.item(i).nodeValue;

            replaceElement.setAttribute(nodeName, nodeValue);
        }

        if (editorJsInput) {
            replaceElement.innerHTML = editorJsInput.value;
            replaceElement.classList.add("form-control");
            replaceElement.style.border = 0;
        }
        //else replaceElement.setAttribute("value", replaceElement.innerHTML); // useless because editor.js doesn't listen value content

        elementToReplace.parentNode.replaceChild(replaceElement, elementToReplace);
    }
}
