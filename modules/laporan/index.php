<?php
// modules/laporan/index.php - REDIRECT TO NEW LOCATION
// This file redirects to the new merged kinerja page
header('Location: ../kinerja/index.php' . ($_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : ''));
exit;
