$( document ).ready(() => {

    let formData = null;
    getFileNames();

    $('#reportFile').change(function() {
        formData = new FormData();
        if($(this).prop('files').length > 0)
        {
            file =$(this).prop('files')[0];
            formData.append("reportFile", file);
        }
    });

    $('button#reportFileSubmit').click( function(event) {
        event.preventDefault();

        if (!formData)
            return;

        $.ajax({
            url: 'http://localhost:8000/api/reports',
            type: 'post',
            processData: false,
            contentType: false,
            data: formData,
            success: function(result) {
                $('.report-upload').append(
                    '<div class="alert alert-success" id="successUploadedReport" role="alert">File ' + result.filename + ' uploaded successfully!</div>'
                );
                $('#successUploadedReport').fadeOut(6000);
                getFileNames();
            },
            error: function() {
                $('.report-upload').append(
                    '<div class="alert alert-danger" id="successUploadedReport" role="alert">Some error occurred!</div>'
                );
                $('#successUploadedReport').fadeOut(6000);
            }
        });
    });

    $('button#reportFileNameSubmit').click( function(event) {
        event.preventDefault();

        const reportId = $('#reportFileName').val();

        $.ajax({
            url: 'http://localhost:8000/api/reports/' + reportId,
            type: 'get',
            processData: false,
            contentType: false,
            success: function(result) {
                const reportList = $(".report-list");
                reportList.empty();
                if (result.id) {
                    reportList
                        .append($("<h4>" + result.filename + "</h4>"));
                    (result);
                    createReportTable(result);
                } else {
                    $.each(result, (key, report) => {
                        reportList
                            .append($("<h4>" + report.filename + "</h4>"));
                        createReportTable(report);
                    });
                }
            },
            error: () => {
                $('.report-list').append(
                    '<div class="alert alert-danger" id="successUploadedReport" role="alert">Some error occurred!</div>'
                );
                $('#successUploadedReport').fadeOut(6000);
            }
        });
    });

    function getFileNames() {
        $( ".report-list" ).empty();
        $.ajax({
            url: 'http://localhost:8000/api/reports/file-names',
            type: 'get',
            processData: false,
            contentType: false,
            success: function(result) {

                if (result.length === 0) {
                    $( ".report-list" ).append(
                        '<div class="alert alert-info" role="alert">There are no uploaded reports!</div>'
                    );
                } else {
                    const reportSelect = $('#reportFileName');
                    reportSelect.empty();

                    reportSelect
                        .append($("<option></option>")
                            .attr("value", '')
                            .text('All Reports'));

                    $.each(result, (key, value) => {
                        reportSelect
                            .append($("<option></option>")
                                .attr("value", value.id)
                                .text(value.fileName));
                    });
                }
            }
        });
    }


    function createReportTable(report) {
        console.log(report);
        $(".report-list")
            .append($('<table class="table table-bordered report-list-table-' + report.id + '">\n' +
                '<thead>' +
                '<th>ID</th>' +
                '<th>Amount</th>' +
                '<th>Price</th>' +
                '<th>Date</th>' +
                '<th>Errors</th>' +
                '</thead>' +
                '<tbody></tbody>' +
                '</table>'));

        $.each(report.data, (key, invoice) => {
            let content = '<tr>' +
                '<td>' + invoice.id + '</td>' +
                '<td>' + invoice.amount + '</td>' +
                '<td>' + invoice.price + '</td>' +
                '<td>' + invoice.date + '</td>' +
                '<td>';

            if (invoice.errors) {
                $.each(invoice.errors, (key, error) => {
                    content += '<p>' + error + '</p>'
                });
            }
            content += '</td>';

            $(".report-list-table-" + report.id + " > tbody")
                .append(content);
        });
    }

});