import $ from 'jquery';
window.jQuery = $;
window.$ = $;

// спливне вікно
export class CustomModal {
    constructor(overlaySelector) {
        this.overlay = $(overlaySelector);
        this.modalContainer = $('<div class="modal-container"></div>');
        this.modalInnerContainer = $('<div class="modal-inner-container"></div>');
        this.modalHeader = $('<div class="modal-header"></div>');
        this.modalContent = $('<div class="modal-content"></div>');
        this.modalFooter = $('<div class="modal-footer"></div>');

        this.closeButton = $('<span class="modal-close"></span>');
        this.modalHeader.append(this.closeButton);
        this.closeButton.on('click', () => {
            this.hideModal();
        });

        this.modalContainer.append(this.modalInnerContainer);
        this.modalInnerContainer.append(this.modalHeader, this.modalContent, this.modalFooter);
        this.overlay.append(this.modalContainer);
    }
  
    showModal(title, message, type, callback, value = null) {
        this.modalHeader.contents().not(this.closeButton).remove();
        this.modalHeader.prepend(title || '');
        this.modalContent.text(message || '');
        this.modalFooter.empty();
  
        // Alert
        if (type === 'alert') {
            const okButton = $('<button class="base-btn">Добре</button>').on('click', () => {
                this.hideModal();
                if (callback) callback();
            });
            this.modalFooter.append(okButton);
        }
        // Confirm
        else if (type === 'confirm') {
            const yesButton = $('<button class="base-btn">Так</button>').on('click', () => {
                this.hideModal();
                if (callback) callback(true);
            });
            const noButton = $('<button>Скасувати</button>').on('click', () => {
                this.hideModal();
                if (callback) callback(false);
            });
            this.modalFooter.append(noButton,yesButton);
        }
        // Input
        else if (type === 'input') {
            const inputField = $('<input type="text" class="modal-input" value="'+value+'">');
            const submitButton = $('<button class="base-btn">Підтвердити</button>').on('click', () => {
                const inputValue = inputField.val();
                this.hideModal();
                if (callback) callback(inputValue);
            });
            const noButton = $('<button>Скасувати</button>').on('click', () => {
                this.hideModal();
                if (callback) callback(false);
            });
            this.modalContent.append(inputField);
            this.modalFooter.append(noButton,submitButton);
        }
        this.overlay.fadeIn();
    }
  
    hideModal() {
        this.overlay.fadeOut();
    }
  
    alert(title, message, callback = null) {
        this.showModal(title, message, 'alert', callback);
    }
  
    confirm(title, message, callback) {
        this.showModal(title, message, 'confirm', callback);
    }
  
    input(title, message, callback, value = null) {
        this.showModal(title, message, 'input', callback, value);
    }

    modal(title, value) {
        this.modalHeader.contents().not(this.closeButton).remove();
        this.modalHeader.prepend(title || '');
        this.modalContent.empty();
        this.modalFooter.empty();
        this.modalContent.html(value);
        this.overlay.fadeIn();
    }

}

// Спливне сповіщення
export function showToast(message, iconClass) {
    var $toast = $('#toast');
    var $message = $toast.find('.message');
    var $icon = $toast.find('.icon-box');

    $message.text(message);
    $icon.removeClass().addClass('icon-box ' + iconClass);

    $toast.css({
        opacity: 0,
        bottom: '0',
        visibility: 'visible'
    });

    $toast.animate({
        opacity: 1,
        bottom: '30px'
    }, 400);

    var timeoutId = setTimeout(function() {
        $toast.animate({
            opacity: 0,
            bottom: '0'
        }, 400, function() {
            $toast.css('visibility', 'hidden');
        });
    }, 2500);

    $toast.hover(
        function() {
            clearTimeout(timeoutId);
        },
        function() {
            timeoutId = setTimeout(function() {
                $toast.animate({
                    opacity: 0,
                    bottom: '0'
                }, 400, function() {
                    $toast.css('visibility', 'hidden');
                });
            }, 2500);
        }
    );
}
export const IconTypes = {
    SUCCESS: 'success',
    ERROR: 'error',
    INFO: 'info',
    WARNING: 'warning'
  };