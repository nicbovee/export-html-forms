<button class="export-button button action" id="export-html-form-csv" download="export.csv" href="/wp-json/export-html-forms/v1/download?form_id=<?= $_GET['form_id'] ?>">
  Export CSV
</button>

<style>
  .export-button {
    position: absolute;
    right: 20px;
  }
</style>