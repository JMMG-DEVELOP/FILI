<script>
  window.BASE_URL = "<?= rtrim(base_url(), '/') ?>/";
</script>

<script>
  let csrfName = '<?= csrf_token() ?>';
  let csrfHash = '<?= csrf_hash() ?>';
  // const baseUrl = '<?php // base_url() ?>';
</script>

<script src="<?= base_url(); ?>/assets/vendor/jquery/jquery-3.3.1.min.js"></script>
<script src="<?= base_url(); ?>/assets/vendor/bootstrap/js/bootstrap.bundle.js"></script>
<script src="<?= base_url(); ?>/assets/vendor/slimscroll/jquery.slimscroll.js"></script>
<script src="<?= base_url(); ?>/assets/libs/js/main-js.js"></script>
<script src="<?= base_url(); ?>/asyng/js/loader.js"></script>

<script src="<?= base_url(); ?>/asyng/js/ajax.js"></script>