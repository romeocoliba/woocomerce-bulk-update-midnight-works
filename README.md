# WooCommerce Bulk Update Plugin

## Description
The WooCommerce Bulk Update plugin allows you to bulk update WooCommerce products with a custom field. This plugin adds a "Promotional Tag" field to your WooCommerce products and provides an admin interface for bulk updating this field.

## Features
- Add a custom "Promotional Tag" field to WooCommerce products.
- Bulk update the custom field for multiple products at once.
- Filter products by category, maximum price, and stock status.
- User-friendly admin interface for easy management.

## Installation
1. Download the plugin zip file.
2. Go to your WordPress admin dashboard.
3. Navigate to **Plugins > Add New**.
4. Click on **Upload Plugin** and select the downloaded zip file.
5. Click on **Install Now**.
6. After the installation is complete, click on **Activate Plugin**.

## Usage Instructions
1. Once the plugin is activated, navigate to **WooCommerce > Bulk Update**.
2. Use the filter options to narrow down the products you want to update:
   - Select a category from the dropdown.
   - Enter a maximum price to filter products by price.
   - Choose a stock status (In Stock or Out of Stock).
3. Click on the **Filter** button to apply the filters.
4. Check the checkboxes next to the products you want to update, or use the checkbox in the header to select all displayed products.
5. Enter the new value for the "Promotional Tag" field in the input box below the table.
6. Click on the **Update Selected** button to apply the changes to the selected products.

## Overview of Features
### Custom Field
- Adds a "Promotional Tag" field to the product data in the WooCommerce product edit screen.

### Admin Interface
- Provides a new submenu under WooCommerce for bulk updating products.
- Displays a table of WooCommerce products with columns for product name, category, price, stock status, and current promotional tag.
- Includes filtering options to filter products by category, maximum price, and stock status.
- Allows bulk updating of the custom "Promotional Tag" field for selected products.

### Security and Performance
- Uses WordPress nonces for security in form submissions.
- Ensures data validation and sanitization for user inputs.
- Optimized for performance with efficient queries and use of WooCommerce APIs.

## Development and Contribution
- Follow WordPress coding standards and best practices.
- Use WordPress APIs for all interactions, avoiding direct database queries.

## Notes on Design Decisions
- The plugin strictly adheres to WordPress coding standards to ensure compatibility and maintainability.
- Security best practices are followed to prevent vulnerabilities.
- Efficient handling of large product sets to ensure performance.

## Challenges and Solutions
- Ensuring compatibility with various themes and WooCommerce extensions required extensive testing.
- Optimizing performance for large product sets involved careful query design and use of efficient data handling techniques.

## GitHub Repository
You can find the complete source code of the plugin, along with the installation and usage instructions, in the [GitHub repository](https://github.com/your-repository-link).

## License
This plugin is licensed under the GPL-2.0-or-later license. See the LICENSE file for more details.

---

## Author
- Romeo Coliba
- Contact: [romeocoliba@gmail.com](mailto:romeocoliba@gmail.com)
