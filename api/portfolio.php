<?php
/**
 * Portfolio API Entry Point
 *
 * Main entry point for portfolio API requests
 * Routes all requests to the PortfolioAPI class
 *
 * URL: /api/portfolio.php
 *
 * @package PYRAMEDIA\API
 * @version 1.0.0
 * @since 2025-10-31
 */

// Set error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', '0'); // Don't display errors to users
ini_set('log_errors', '1'); // Log errors to file

// Set timezone
date_default_timezone_set('Asia/Dubai');

// Include the Portfolio API class
require_once __DIR__ . '/../php/api/PortfolioAPI.php';

// That's it! The PortfolioAPI handles everything
