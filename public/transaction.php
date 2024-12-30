<?php
	require_once __DIR__ . '/../vendor/autoload.php';

	\Sentry\init([
  		'dsn' => 'https://9f6e3a7ae4e9bf1b0ca0f68f4bfc9e8f@o4508475231961088.ingest.us.sentry.io/4508475233992704',

		// Set tracesSampleRate to 1.0 to capture 100%
		// of transactions for performance monitoring.
		'traces_sample_rate' => 1.0,
	]);
?>