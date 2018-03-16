<html>

<?php



$clientToken = Braintree_ClientToken::generate();

?>

<script>clientToken = '<?php echo($clientToken) ?>';</script>

<head>
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-57930548-13"></script>
		<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		gtag('config', 'UA-57930548-13');
		</script>

		<!-- COMMON TAGS -->
		<meta charset="utf-8">
		<title>Protected.design - Blockchain Based Proof of Design Ownership & Existence</title>
		<!-- Search Engine -->
		<meta name="description" content="Using the Ethereum blockchain, Protected.design allows you to create a legal proof that you are the owner of the design and that the design existed prior to a specific date.">
		<meta name="image" content="https://protected.design/img/social_img.jpg">
		<!-- Schema.org for Google -->
		<meta itemprop="name" content="Protected.design - Blockchain Based Proof of Design Ownership & Existence">
		<meta itemprop="description" content="Using the Ethereum blockchain, Protected.design allows you to create a legal proof that you are the owner of the design and that the design existed prior to a specific date.">
		<meta itemprop="image" content="https://protected.design/img/social_img.jpg">
		<!-- Twitter -->
		<meta name="twitter:card" content="summary">
		<meta name="twitter:title" content="Protected.design - Blockchain Based Proof of Design Ownership & Existence">
		<meta name="twitter:description" content="Using the Ethereum blockchain, Protected.design allows you to create a legal proof that you are the owner of the design and that the design existed prior to a specific date.">
		<meta name="twitter:site" content="@protecteddesign">
		<meta name="twitter:image:src" content="https://protected.design/img/social_img.jpg">
		<!-- Open Graph general (Facebook, Pinterest & Google+) -->
		<meta property="og:title" content="Protected.design - Blockchain Based Proof of Design Ownership & Existence">
		<meta property="og:description" content="Using the Ethereum blockchain, Protected.design allows you to create a legal proof that you are the owner of the design and that the design existed prior to a specific date.">
		<meta property="og:image" content="https://protected.design/img/social_img.jpg">
		<meta property="og:url" content="https://protected.design">
		<meta property="og:site_name" content="Protected.design">
		<meta property="og:type" content="website">

		<link rel="icon" type="image/png" href="<?php echo get_stylesheet_directory_uri(); ?>/img/favicon.png">

		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	
		<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/dropin.min.js"></script>
		<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/_auth.js"></script>
		<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/vue.js"></script>
		<script src="<?php echo get_stylesheet_directory_uri(); ?>/includes/vue-resource.min.js"></script>

		<link href="<?php echo get_stylesheet_directory_uri(); ?>/app-styles.css?v=019" rel="stylesheet">

		<link href="https://fonts.googleapis.com/css?family=PT+Mono|Source+Sans+Pro" rel="stylesheet">

</head>

<body <?php body_class(); ?>>