jQuery('#export-html-form-csv').on('click', (e) => {
  e.preventDefault();
  jQuery
    .ajax({
      url: wpApiSettings.root + 'export-html-forms/v1/download?form_id=21',
      method: 'GET',
      beforeSend: function (xhr) {
        xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
      },
    })
    .done(function (response) {
      let headers = [
        ...Object.keys(response[1].data),
        ...Object.keys(response[1]).filter((key) => key !== 'data'),
      ];
      let values = Object.values(response).map((value) => [
        ...Object.values(value.data),
        ...Object.keys(value)
          .filter((key) => key !== 'data')
          .map((key) => value[key]),
      ]);
      let csv = arrayToCsv([headers, ...values]);
      downloadBlob(csv, 'export.csv', 'text/csv;charset=utf-8;');
    });
});

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
