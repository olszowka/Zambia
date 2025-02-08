// Bulk Email Sending functions
//  instance of the class must be a javascript variable named emailBulkSend
// Usage concept:
//  1. create the bulk email instance
//  2. Call the getList function passing in the script and data to pass to retrieve the email parameters
//  3. Get approval to send the list (in regular or test mode) to the list of people
//  4. Loop over the list calling the function for a particular batch size, until all emails are sent.
class EmailBulkSend {

    // Bulk Email DOM related privates
    #emailStatusDivId = null;
    #emailStatusDiv = null;

    // Bulk Email in progress privates
    #emailText = null;
    #emailFrom = null;
    #emailTo = null;
    #emailCC = null;
    #emailSubject = null;
    #emailStatusHTML = '';
    #emailBatch = null;

    // Operational privates
    #debug = 0;
    #sendURL = null;
    #batchStartTime = null;

    // Email sending parameters
    #batchSize = 100;

    constructor(statusDivName, sendURL, debug = 0) {
        this.#debug = debug;
        this.#sendURL = sendURL;
        this.#batchSize = email.batchsize;
        this.#emailStatusDivId = statusDivName;
        this.#emailStatusDiv = document.getElementById(statusDivName);
        this.#emailStatusHTML = "Email Send Started for " + recipient_count + " emails\n<PRE>\n";
        this.#emailStatusDiv.innerHTML = this.#emailStatusHTML + "</pre>\n";
        this.#emailTo = recipientinfo;
        if (recipient_count > 0)
            this.sendNextBatch();
        else
            this.#emailStatusDiv.innerHTML = "Nothing to send?";
    }

    sendNextBatch() {
        this.#emailBatch = this.#emailTo.slice(0, this.#batchSize);
        this.#batchStartTime = Date.now();
        var data = {
            email: email,
            recipientinfo: this.#emailBatch,
        };
        var dataJSON = btoa(encodeURI(JSON.stringify(data)));
        var _this = this;
        $.ajax({
            url: this.#sendURL,
            data: { data: dataJSON },
            method: 'POST',
            success: function (data, textstatus, jqxhr) {
                _this.finishBatch(data);
            },
            error: showAjaxError
        })
    }

    finishBatch(data) {
        if (this.#debug & 1)
            console.log(data);
        if (data['error'] !== undefined) {
            show_message(data['error'], 'error', this.#emailStatusDivId );
            return;
        }
        if (data['success'] !== undefined) {
            show_message(data['success'], 'success', this.#emailStatusDivId);
        }
        if (data['warn'] !== undefined) {
            show_message(data['warn'], 'warn', this.#emailStatusDivId);
        }

        var elapsed = (Date.now() - this.#batchStartTime) / 1000;
        this.#emailStatusHTML += "Batch of " + this.#emailBatch.length + " sent in " + elapsed + " seconds\n";
        this.#emailStatusDiv.innerHTML = this.#emailStatusHTML + "</pre>\n";
        this.#emailTo = this.#emailTo.slice(this.#batchSize);
        if (this.#emailTo.length > 0) {
            this.sendNextBatch();
        } else {
            this.#emailStatusHTML += "\n\n</pre>\nEmail Send Complete";
            this.#emailStatusDiv.innerHTML = this.#emailStatusHTML;
        }
    }
}

emailBulkSend = null;
window.onload = (function() {
    emailBulkSend = new EmailBulkSend('bulkSendStatusDiv', 'staffBulkSendBatch.php', 1);
});

function showAjaxError(data, textStatus, jqXHR) {
    var $resultBoxDIV = $("#resultBoxDIV");
    if (data && data.responseText) {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">${data.responseText}</div></div></div>`;
    } else {
        content = `<div class="row mt-3"><div class="col-12"><div class="alert alert-danger" role="alert">An error occurred on the server.</div></div></div>`;
    }
    $resultBoxDIV.html(content).show();
    window.scrollTo(0, 0);
}