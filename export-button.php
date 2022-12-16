<button class="export-button button action" id="export-html-form-csv" download="export.csv" href="/wp-json/export-html-forms/v1/download?form_id=<?= $_GET['form_id'] ?>">
  Export CSV
</button>


<script>
  jQuery('#export-html-form-csv').on('click', (e) => {
    e.preventDefault();
    jQuery
      .ajax({
        url: wpApiSettings.root + 'export-html-forms/v1/download?form_id=21',
        method: 'GET',
        beforeSend: function(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', wpApiSettings.nonce);
        },
      })
      .done(function(response) {
        console.log(response);
      });
  });
</script>
<style>
  .export-button {
    position: absolute;
    right: 20px;
  }
</style>