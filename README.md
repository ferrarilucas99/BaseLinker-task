# _Baselinker Task_
# Shipment Manager Project

## Overview

The Shipment Manager project is a PHP-based application that allows users to create shipments and download shipping labels using the Spring Courier API. It features two main functionalities: creating a new shipment and retrieving a PDF label for a given shipment. The application is built with a simple frontend interface using Bootstrap and a backend system written in PHP.

## Table of Contents
1. [Installation](#installation)
2. [Features](#features)
3. [Usage](#usage)
4. [API Integration](#api-integration)
5. [Code Structure](#code-structure)

## Installation
### Prerequisites
- PHP 7.4 or higher
- cURL extension enabled in PHP

### Setup
1. Clone this repository to your local environment.
   ```
   git clone https://github.com/ferrarilucas99/baselinker-task.git
   cd baselinker-task
   ```
2. Run.
    ```
    php -S localhost:8000
    ```
3. Optionally, configure the spring.php file to point to your production API by replacing the test URL in the SpringCourier class.

## Features
- Create Shipment: Allows users to create a shipment by providing sender and recipient details.
- Download Label: Retrieves a shipping label in PDF format for the specified tracking number.
- Input Validation: Validates inputs like phone numbers and zip codes.
- Session-based Flash Messages: Displays success or error messages after operations.
- Auto-generated Shipper Reference: A unique reference is automatically generated for each shipment (but you can declare manually).

## Usage
### Create Shipment
- Fill in the form with the required sender and recipient information.
- Click "Create Shipment" to submit the form.
- A tracking number will be generated and displayed upon success.

### Getting a Label
- Enter the tracking number and API key in the second form.
- Click "Download Label" to retrieve the shipping label in PDF format.
- The label will be displayed in a modal, and the download will automatically start.

## API Integration
The system integrates with the Spring Courier API via cURL requests to create shipments and retrieve labels. The SpringCourier class in spring.php handles the API requests and responses. Key functions include:

- newPackage(array $order, array $params): Creates a new shipment with sender and recipient details.
- packagePDF(string $trackingNumber, string $apikey): Retrieves the label PDF for a given shipment tracking number (I added the optional $apikey parameter so it would be possible to test with other keys without messing with the code).

## Code Structure
It was developed using the best development practices for both PHP and JavaScript.
Following PSR-12 recommendations, KISS principle and early return and single responsibility patterns.

### index.php
This file handles:
- Form Submission: Two forms are used to create shipments and get labels.
- Session Management: Flash messages for error/success handling.
- Modal and Auto-download: Displays the shipping label in a modal and automatically triggers the label download.
- Input Validation: Uses JavaScript to validate phone and zip code inputs.

### spring.php
This file contains the SpringCourier class, which handles communication with the Spring Courier API. Key components:
- API Request Construction: Data is structured as required by the Spring Courier API.
- cURL Requests: Handles API calls and responses.
- Error Handling: Manages error conditions from the API and returns structured feedback.























