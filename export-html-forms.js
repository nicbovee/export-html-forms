jQuery('#export-html-form-csv').on('click', (e) => {
  e.preventDefault();
  jQuery
    .ajax({
      url:
        wpApiSettings.root +
        'export-html-forms/v1/download?form_id=' +
        wpApiSettings.htmlFormsId,
      method: 'GET',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
      },
    })
    .done(function (response) {
      const highestNumberOfKeys = Math.max(
        ...Object.values(response).map((x) => Object.keys(x.data).length)
      );

      const indexOfHighestNumberOfKeys = Object.values(response).findIndex(
        (x) => Object.keys(x.data).length === highestNumberOfKeys
      );

      let dataHeaders = Object.keys(
        response[Object.keys(response)[indexOfHighestNumberOfKeys]].data
      );
      let headers = [
        ...dataHeaders,
        ...Object.keys(response[Object.keys(response)[0]]).filter(
          (key) => key !== 'data'
        ),
      ];
      let values = Object.values(response)
        .map((value) => [
          ...dataHeaders.map((x) => {
            if (value.data.hasOwnProperty(x)) {
              if (typeof value.data[x] === 'object') {
                return Object.values(value.data[x]).join(', ');
              }
              return value.data[x];
            } else {
              return '';
            }
          }),
          ...Object.keys(value)
            .filter((key) => key !== 'data')
            .map((key) => value[key]),
        ])
        .reverse();

      let data = [headers, ...values];
      let csv = arrayToCsv(data);
      downloadBlob(csv, 'export.csv', 'text/csv;charset=utf-8;');
    });
});

// from https://stackoverflow.com/questions/14964035/how-to-export-javascript-array-info-to-csv-on-client-side#answer-68146412
function arrayToCsv(data) {
  return data
    .map(
      (row) =>
        row
          .map(String) // convert every value to String
          .map((v) => v.replaceAll('"', '""')) // escape double colons
          .map((v) => `"${v}"`) // quote it
          .join(',') // comma-separated
    )
    .join('\r\n'); // rows starting on new lines
}

// from https://stackoverflow.com/questions/14964035/how-to-export-javascript-array-info-to-csv-on-client-side#answer-68146412
function downloadBlob(content, filename, contentType) {
  // Create a blob
  var blob = new Blob([content], { type: contentType });
  var url = URL.createObjectURL(blob);

  // Create a link to download it
  var pom = document.createElement('a');
  pom.href = url;
  pom.setAttribute('download', filename);
  pom.click();
}
