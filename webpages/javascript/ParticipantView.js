//	Created by Peter Olszowka on 2022-12-26;
//	Copyright (c) 2022 Peter Olszowka. All rights reserved. See copyright document for more details.

const participantview = function() {
    this.initialize = () => {
        this.$confNotAttModal = $('#confNotAttModal');
        this.$confNotAttModal.modal({show:false});
        this.$pwform = document.getElementById('pwform');
        this.$pwform.addEventListener('submit', this.onSubmitPwform);
        this.$interestedSelect = document.getElementById('interested');
        this.scheduleCount = parseInt(this.$interestedSelect.dataset.scheduleCount, 10);
        document.getElementById('cancelNotAtt').addEventListener('click', this.cancelNotAttModal);
        document.getElementById('confNotAtt').addEventListener('click', this.onConfirmNotAtt);
    };

    this.onSubmitPwform = (event) => {
        if (this.$interestedSelect.value === '1'
            || (this.scheduleCount || 0) < 1) {
            return;
        }
        this.$confNotAttModal.modal('show');
        event.preventDefault();
    }

    this.onConfirmNotAtt = (event) => {
        this.$pwform.submit();
    }

    this.cancelNotAttModal = (event) => {
        this.$interestedSelect.value = '1';
    }
}

window.ParticipantView = new participantview();