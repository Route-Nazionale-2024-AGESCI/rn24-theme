<footer class="footer mt-auto py-3 bg-dark">
    <div class="container">
      <div class="row justify-content-between">
        <div class="col">
          <img src="<?php echo get_bloginfo('template_directory'); ?>/img/rn24-agesci_neg_o.png" class="rn-logo-footer" alt="Logo RN24">
        </div>
        <div class="col text-right">
          <p class="follow-us-pre">Seguici anche su</p>
          <span class="follow-us-links">
            <a href="https://facebook.com/agesci.routenazionale2024" target="_blank">
              <img src="<?php echo get_bloginfo('template_directory'); ?>/img/facebook-f.svg" 
                class="social-link-footer" alt="Facebook RN24">
            </a>
            <a href="https://instagram.com/agesci.routenazionale2024" target="_blank">
              <img src="<?php echo get_bloginfo('template_directory'); ?>/img/instagram.svg" 
                  class="social-link-footer" alt="Instagram RN24">
            </a>
          </span>
        </div>
      </div><!-- .row -->
      <div class="footer-row"></div>
      <div class="row">
        <div class="col-md-6 col-12 mb-2 mb-md-0">
          <span class="footer-copy">&copy; AGESCI Associazione Guide e Scouts Cattolici Italiani</span>
        </div>
        <div class="col-md-6 col-12">
          <ul class="navbar-nav mr-auto footer-menu">
            <?php if (has_nav_menu('footer-menu')) {
              wp_nav_menu(array('theme_location' => 'footer-menu'));
            } ?>
        </ul>
        </div>
      </div><!-- .row -->
    </div><!-- .container -->
  </footer>

  
  <div class="modal" tabindex="-1" id="confirm-dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title"></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary cancel" data-bs-dismiss="modal">Chiudi</button>
            <button type="button" class="btn btn-primary save">Invia</button>
        </div>
        </div>
    </div>
</div>

  <input type="hidden" id="template-directory-url" value="<?php echo get_bloginfo('template_directory'); ?>">
  <script src="<?php echo get_bloginfo('template_directory'); ?>/js/bootstrap.min.js"></script>
  <script src="<?php echo get_bloginfo('template_directory'); ?>/js/selectize.min.js"></script>
  <script src="<?php echo get_bloginfo('template_directory'); ?>/js/leaflet.js"></script>
  <script src="<?php echo get_bloginfo('template_directory'); ?>/js/app.js"></script>
  <?php wp_footer(); ?>

</body>
</html>