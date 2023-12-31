<!DOCTYPE html>
<html lang="it" class="h-100">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title><?php echo (wp_title('', false) != NULL && wp_title('', false) != '' ? wp_title('', false) : get_bloginfo( 'name' )); ?> | RN24</title>
  <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/css/selectize.default.min.css">
  <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/style.css">
  <link href="<?php echo get_bloginfo('template_directory'); ?>/img/rn24-agesci_simbolo.png" rel="shortcut icon" type="image/x-icon" />
  <!-- Open Graph -->
  <meta property="og:url" content="https://rn24.agesci.it/" />
  <meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo (wp_title('', false) != NULL && wp_title('', false) != '' ? wp_title('', false) : get_bloginfo( 'name' )); ?> | RN24" />
	<meta property="og:description" content="Narrare e condividere il cambiamento realizzato e ancora necessario. Identità e appartenenza. Riconoscerci e farci riconoscere. Comunità capi centrali e protagoniste. Dentro l'Associazione e nei territori. È un Tempo per riscoprire le ragioni della Scelta di essere educatori oggi." />
	<meta property="og:image" content="<?php echo get_bloginfo('template_directory'); ?>/img/rn24-agesci_simbolo.png" />

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary" />
	<meta name="twitter:title" content="<?php echo (wp_title('', false) != NULL && wp_title('', false) != '' ? wp_title('', false) : get_bloginfo( 'name' )); ?> | RN24" />
	<meta name="twitter:description" content="Narrare e condividere il cambiamento realizzato e ancora necessario. Identità e appartenenza. Riconoscerci e farci riconoscere. Comunità capi centrali e protagoniste. Dentro l'Associazione e nei territori. È un Tempo per riscoprire le ragioni della Scelta di essere educatori oggi." />
	<meta property="twitter:image" content="<?php echo get_bloginfo('template_directory'); ?>/img/rn24-agesci_simbolo.png" />
  
  <link rel="stylesheet" href="<?php echo get_bloginfo('template_directory'); ?>/css/leaflet.css" />
  <?php wp_head(); ?>
</head>