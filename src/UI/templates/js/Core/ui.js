/**
 * Replace a component or parts of a component using ajax call
 *
 * @param id component id
 * @param url replacement url
 * @param marker replacement marker ("component", "content", "header", ...)
 */
var replaceContent = function($) {
    return function (id, url, marker) {
        // get new stuff via ajax
        $.ajax({
            url: url,
            dataType: 'html'
        }).done(function(html) {
            var $new_content = $("<div>" + html + "</div>");
            var $marked_new_content = $new_content.find("[data-replace-marker='" + marker + "']").first();

            if ($marked_new_content.length == 0) {

                // if marker does not come with the new content, we put the new content into the existing marker
                // (this includes all script tags already)
                $("#" + id + " [data-replace-marker='" + marker + "']").html(html);

            } else {

                // if marker is in new content, we replace the complete old node with the marker
                // with the new marked node
                $("#" + id + " [data-replace-marker='" + marker + "']").first()
                    .replaceWith($marked_new_content);

                // append included script (which will not be part of the marked node
                $("#" + id + " [data-replace-marker='" + marker + "']").first()
                    .after($new_content.find("[data-replace-marker='script']"));
            }
        });
    }
};

class Tooltip {
    constructor(element) {
        this.element = element;
        this.document = element.ownerDocument;

        var tooltip_id = this.element.getAttribute("aria-describedby");
        if (tooltip_id == null) {
            throw new Error("Could not find expected attribute aria-describedby for element with tooltip.");
        }

        this.tooltip = this.document.getElementById(tooltip_id);
        if (this.tooltip == null) {
            throw new Error("Tooltip " + foo + " not found.", {cause: this.element});
        }

        this.showTooltip = this.showTooltip.bind(this);
        this.hideTooltip = this.hideTooltip.bind(this);
        this.onKeyDown = this.onKeyDown.bind(this);
        this.onPointerDown = this.onPointerDown.bind(this);

        this.bindElementEvents();
    }

    bindElementEvents() {
        this.element.addEventListener("mouseenter", this.showTooltip);
        this.element.addEventListener("touchstart", this.showTooltip);
        this.element.addEventListener("focus", this.showTooltip);
        this.element.addEventListener("mouseleave", this.hideTooltip);
        this.element.addEventListener("blur", this.hideTooltip);
    }

    showTooltip() {
        this.tooltip.classList.add("tooltip-visible");
        this.bindDocumentEvents(); 
    }

    hideTooltip() {
        this.tooltip.classList.remove("tooltip-visible");
        this.unbindDocumentEvents(); 
    }

    bindDocumentEvents() {
        this.document.addEventListener("keydown", this.onKeyDown);
        this.document.addEventListener("pointerdown", this.onPointerDown);
    }

    unbindDocumentEvents() {
        this.document.removeEventListener("keydown", this.onKeyDown);
        this.document.removeEventListener("pointerdown", this.onPointerDown);
    }

    onKeyDown(event) {
        if (event.key === "Esc" || event.key === "Escape") {
            this.hideTooltip();
        }
    }

    onPointerDown(event) {
        if(event.target === this.element || event.target === this.tooltip) {
            event.preventDefault();
        }
        else {
            this.hideTooltip();
            this.element.blur();
        }
    }
}

il = il || {};
il.UI = il.UI || {};
il.UI.core = il.UI.core || {};

il.UI.core.replaceContent = replaceContent($);
il.UI.core.Tooltip = Tooltip;
