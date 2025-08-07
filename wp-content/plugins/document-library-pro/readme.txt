=====================================================
WORDPRESS - PHP SCRIPTS - TEMPLATES -  FLUTTER - MORE
--------------- https://weadown.com -----------------
=====================================================

=== Document Library Pro ===
Contributors: barn2media
Tags: document, library, table, tables, shortcode, search, sort
Requires at least: 6.1
Tested up to: 6.8.1
Requires PHP: 7.4
Stable tag: 2.1.0
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Update URI: https://barn2.com/wordpress-plugins/document-library-pro/

Add documents and display them in a searchable document library with filters.

== Description ==

Add documents and display them in a searchable document library with filters.

== Installation ==

1. Go to Plugins -> Add New -> Upload and select the plugin ZIP file (see link in Purchase Confirmation Email).
2. Activate the plugin.
3. Enter your license key under Documents -> Settings.

== Frequently Asked Questions ==

See refer to the [documentation](https://barn2.com/kb-categories/document-library-pro-kb/). If you need further assistance please visit [our support center](https://barn2.com/support-center/).

== Changelog ==

= 2.1.0 =
Release date 18 June 2025

* New: Added design templates.
* Fix: Incorrect sort with accented characters.
* Fix: Filter `document_library_pro_column_heading_link` no longer working.
* Fix: Adjusted regex for ACF field values.
* Fix: Filter `document_library_pro_data_title` no longer working.
* Dev: Updated Barn2 libraries.

= 2.0.2 =
Release date 27 May 2025

* Fix: Custom taxonomy support in the new columns editor.
* Fix: Custom taxonomies and fields in the 'columns' setting were not migrated to the new columns editor setting.
* Fix: PHP warnings on sites with their custom taxonomies attached to the document post type.
* Dev: Added a filter for the maximum size of cacheable table data.

<!--more-->

= 2.0.1 =
Release date 14 May 2025

* Tweak: Added fallback for shortcodes using legacy link and preview style options.
* Tweak: Design alignment of the download icon on the grid layout.
* Tweak: Removed conditional display logic from the "Document limit" setting.
* Tweak: Added a new "accent-insensitive search" setting to the "Advanced" tab.
* Tweak: Added new attributes to preview button anchor tags.
* Fix: Edge cases where legacy link/preview style and link destination options were not migrated correctly.
* Fix: PHP warning when trying to access 'slug' on non-taxonomy object.
* Fix: Style issues with Select2 dropdowns.
* Fix: PHP 8.4 deprecation warnings.
* Fix: Issue with German translation.
* Dev: Added a filter to allow modification of the preview url.

= 2.0.0 =
Release date 06 May 2025

* New: Added a columns editor to streamline the interface for managing table columns.
* New: Redesigned settings pages for improved user experience.
* New: The Filename column is now clickable.
* New: Link destination option for document title and filename fields.
* New: Option to open other document links in a new tab.
* New: Add public taxonomies archives for file types and document authors.
* New: Added numeric sorting for the table layout.
* Dev: Updated Barn2 libraries.

= 1.17.1 =
Release date 05 March 2025

* New: Google Drive share URLs can now be imported by the DLP Importer if they reference a public file download.
* Tweak: Ensure the filename has the correct extension when imported from platforms like Google Docs or Google Sheets.
* Fix: Pagination not working correctly when using the page_length="false" shortcode attribute with a grid layout.
* Fix: Google Drive URLs imported by the DLP Importer will be uploaded with the correct file name.
* Dev: Added filters for translating File URLs being imported by the DLP Importer.
* Dev: Added filters for mime types allowed to be imported by the DLP Importer.
* Dev: Tested up to WordPress 6.7.2.
* Dev: Updated Barn2 libraries.

= 1.17.0 =
Release date 16 December 2024

 * New: Added a "Clear cache" button to the settings page.
 * Tweak: Added a filter to allow Password Protected Categories to be used with the form submission.
 * Tweak: Added a filter to disable the auto thumbnail feature.
 * Tweak: Adjusted how the search reset function works.
 * Tweak: Removed the default "Uncategorized" document category assignment.
 * Fix: Unable to import documents via CSV that have a Dropbox URL with a query string of `dl=0`.
 * Fix: Typo in the "Save Changes" button text.
 * Fix: The file info on the grid layout may overflow the container when the file name is long.
 * Dev: Updated Barn2 libraries.

= 1.16.2 =
Release date 03 December 2024

 * Fix: Translation compatibility issues with WordPress 6.7.
 * Tweak: Added WPML configuration file to the plugin.
 * Dev: Tested up to WordPress 6.7.1.
 * Dev: Updated Barn2 libraries.

= 1.16.1 =
Release date 05 November 2024

 * Tweak: Improved accessibility when document folders are enabled.
 * Tweak: Minor improvements to the code.
 * Fix: Fatal error when using the "content" attribute in the shortcode.

= 1.16.0 =
Release date 22 October 2024

 * New: Automatic featured images for documents with linked PDF or image files.
 * New: Added a default 'Uncategorized' document category.
 * Tweak: Set document expiry dates via the CSV import.
 * Tweak: Added a unique CSS class per document to the document grid card HTML.
 * Fix: Filters were displayed on the grid based on table columns and not grid content.
 * Fix: The download count was counting visits to the single document page.
 * Dev: Updated Barn2 internal libraries.

= 1.15.1 =
Release date 18 September 2024

 * Fix: Folder categories and table column categories were not hidden when protected with Password Protected Categories.
 * Fix: Fixed cf: not working in shortcode if value has @.
 * Fix: Fixed search_on_click bug not working for default true value.
 * Dev: Added filter `document_library_pro_folder_categories` to filter folder categories.
 * Dev: Added filter `document_library_pro_data_column_terms` to filter a column terms.
 * Dev: Tested up to WordPress 6.6.2

= 1.15.0 =
Release date 27 August 2024

 * New: Set a document expiry date, after which the document will become visible only to editors and administrators.
 * New: Taxonomy and page length dropdown filters for the grid layout.
 * Fix: Added fallbacks for the responsive srcset on the grid card image.
 * Fix: It was not possible to uncheck the custom folder icon setting.
 * Fix: Long link text would overflow outside the button on the table layout under certain configurations.
 * Dev: Tested up to WordPress 6.6.1.

= 1.14.0 =
Release date 27 May 2024

 * New: Word, Excel and Powerpoint files can now be previewed with the Office Web Viewer.
 * New: Supported external links can now be previewed.
 * New: Documents and their metadata are now retrievable via the REST API.
 * New: Added filename option for the table, grid and single document page layouts.
 * New: Added option to display the file size on the single document page.
 * New: Display the Document Author(s) on the grid page.
 * Tweak: Ensured all knowledge base links on the settings page open in a new tab.
 * Tweak: Adjusted the position and name of some settings.
 * Tweak: Changed the name of Document Author filter on the table layout to Author.
 * Tweak: Add more spacing below the folders output.
 * Tweak: Changed the default position of the Document Link metabox on the Add/Edit Document page in wp-admin.
 * Tweak: Removed the excerpt option from the single document page. Existing configurations with it enabled will remain in place until the setting is deactivated and there is a filter available to re-enable it.
 * Tweak: Allow full use of wp-admin functionality without a valid license.
 * Tweak: Changed the document author taxonomy to public.
 * Fix: PHP warning when viewing the single document page on a fresh install with the default settings.
 * Fix: Download count was not displayed on the grid layout when filename, file type, file size and categories were not set to display.
 * Fix: Download and preview buttons were not displaying correctly on new installs under certain conditions.
 * Fix: Preview button alignment in the table layout when using the multi-download checkboxes.
 * Fix: The version control confirmation dialog was incorrectly appearing under certain configurations.
 * Fix: Display the correct initial button text for the Add/Replace File button in the Document Link metabox.
 * Fix: Clear previously saved file size when switching the Document Link type from File to URL on the Add/Edit Document page in wp-admin.
 * Fix: cf: not working in shortcode if value has @.
 * Fix: Fixed bug with search_on_click not working for default true value.
 * Dev: Tested up to WordPress 6.5.3.

= 1.13.6 =
Release date 3 April 2024

 * Fix: Fixed PHP warning message when no custom sorting column is added.
 * Fix: Fixed responsive hidden row not closing after reset.
 * Fix: Fixed bug that empty folders are being shown when doc_category is added to the shortcode.
 * Fix: Fixed the grid search to only get results after a certain amount of keywords typed.
 * Tweak: Added document_library_pro_form_redirect filter hook.
 * Tweak: Added document_library_pro_download_checkboxes_should_display filter hook.
 * Tweak: Updated the search_box attribute to work with true or false values.
 * Tweak: Added show_page option to document library page settings.
 * Tweak: Removed parsing the shortcode attribute values.
 * Tweak: Auto-hide reset link.
 * Tweak: Moved the totals position to the right in the grid layout.
 * Tweak: Auto-hide the pagination on the grid layout.
 * Tweak: Add grid totals singular and plural wording.
 * Dev: Tested up to WordPress 6.5.

= 1.13.5 =
Release date 8 November 2023

 * New: Added French translation (credit: Nicolas Zein).
 * Fix: Fixed shortcodes adding extra space in excerpt.
 * Dev: Added document_library_pro_enable_media_filter filter hook.
 * Dev: Tested up to WordPress 6.4.

= 1.13.4 =
Release date 11 October 2023

 * Fix: Fixed ACF fields not showing when importing.
 * Tweak: Improved preview modal to support more themes.
 * Tweak: Saving license key when clicking next in setup wizard.
 * Dev: Reviewed parsing shortcode attribute values.

= 1.13.3 =
Release date 21 September 2023

 * Tweak: Added ACF and EPT custom fields to import options.
 * Tweak: Added folder category descriptions filter hook.
 * Fix: Fixed loading icon not showing on folders.
 * Dev: Normalized shortcode arguments.

= 1.13.2 =
Release date 14 September 2023

 * Fix: Turned post_object public to allow other plugins to use it.

= 1.13.1 =
Release date 13 September 2023

 * Fix: Fixed folders not including documents from doc_category attribute.
 * Fix: Fixed table sort for acf fields with empty values.
 * Fix: Fixed bug on total filtered items when using SearchWP.
 * Dev: Added document grid custom fields to wizard.
 * Dev: Added custom fields default values.

= 1.13.0 =
Release date 7 September 2023

 * New: PHP 8 support.
 * Dev: Tested up to WordPress 6.3.1.

= 1.12.1 =
Release date 18 August 2023

 * Fix: Fixed root folders displaying the documents that belongs to their sub folders.

= 1.12.0 =
Release date 15 August 2023

 * New: Option to display custom fields on single document pages.
 * New: Option to display custom fields on grid layout.
 * New: Option to set default WordPress custom fields on document edit pages.
 * Tweak: Added loader on submission form.
 * Tweak: Added reset button to filters in folder view.
 * Fix: Allowed numeric terms on folders view.
 * Fix: Fixed categories and tags not being saved on form submission.
 * Dev: Added document_library_pro_search_placeholder filter.
 * Dev: Added SECURITY.md file.
 * Dev: Tested up to WordPress 6.3.

= 1.11.1 =
Release date 19 July 2023

 * Fix: Fixed german language file error.
 * Fix: Fixed the possibility of renaming the filters.
 * Fix: Fixed close svg button aria label attribute.

= 1.11.0 =
Release date 11 July 2023

 * Tweak: Change Document Categories and Tags filter headings.
 * Fix: Fix parent folders not sorting when they are empty.
 * Fix: Added aria label accessibility to SVG links.
 * Dev: Updated get_file_url to contemplate more hostings.
 * Dev: Updated Barn2 libraries and dependencies.
 * Dev: Updated to webpack-config 2.0.0.

= 1.10.0 =
Release date 29 May 2023

 * New: Added German translation.
 * Fix: Fixed protected documents not being shown upon search.
 * Fix: Added filter hook searchwp\query\logic\phrase\strict compatibility.
 * Dev: Tested up to WordPress 6.2.2.

= 1.9.9 =
Release date 26 April 2023

 * Fix: Fixed custom post type naming on admin new toolbar.
 * Fix: Fixed search feature not working for non english characters.

= 1.9.8 =
Release date 17 April 2023

 * Tweak: Updated allowed file mime types.
 * Fix: Fixed FacetWP logic conditions not working
 * Fix: Fixed FacetWP pagination not working
 * Dev: Fixed path generation from urls with directories.

= 1.9.7 =
Release date 31 March 2023

 * Tweak: Added the possibility to import custom taxonomies by their name.
 * Dev: Fixed the hook name 'document_library_pro_data_version'.
 * Dev: Tested up to WordPress 6.2.

= 1.9.6 =
Release date 22 March 2023

 * Tweak: Added a new filter document_library_pro_import_capability to change the import user capability.
 * Dev: Tested up to WordPress 6.1.1.

= 1.9.5 =
Release date 15 March 2023

 * Tweak: Added 2 new filters that help determine visibility of the preview and download buttons.

= 1.9.4 =
Release date 16 February 2023

 * Fix: When using the 'document_library_pro_language_defaults' filter, 'totalsSingle' and 'totalsPlural' were not correctly applied.
 * Dev: Updated Barn2 libraries and dependencies.

= 1.9.3 =
Release date 6 February 2023

 * Fix: Content field could not be disabled on the frontend submission form.
 * Fix: Documents were not created via the frontend submission form if the document excerpt was disabled.
 * Fix: High resolution videos could exceed the container dimensions in the preview modal.
 * Dev: Add filter to allow adjustments on the frontend submission form fields configuration.
 * Dev: Updated Barn2 libraries and dependencies.

= 1.9.2 =
Release date 26 January 2023

 * Fix: HTML entities were not decoded in the taxonomy dropdowns on the frontend submission form.
 * Fix: Allow `post__in` as an orderby option in the table query for FacetWP sorting.
 * Dev: Updated Barn2 libraries and dependencies.

= 1.9.1 =
Release date 18 January 2023

* Tweak: Add support for the Sort facet in the FacetWP integration.
* Fix: Some special characters were not allowed when selecting documents by custom field, or when using the search box.
* Dev: Updated Barn2 libraries and dependencies.

= 1.9.0 =
Release date 12 January 2023

* New: Added frontend document uploader form.
* Tweak: Updated language files.
* Dev: Updated Barn2 libraries and dependencies.

= 1.8.4 =
Release date 12 December 2022

* Fix: Download count was not tracked when using the `icon_only`, `icon`, or `text` styles for the download button.
* Fix: The `no_docs_message` option was not functioning correctly in grid layout.
* Dev: Updated Barn2 libraries and dependencies.

= 1.8.3 =
Release date 6 December 2022

* Fix: The `search_on_click` option was not functioning correctly.
* Fix: In grid layout, the `simple_numbers` pagination style output a 'Last' button instead of a 'Next' button.
* Dev: Updated Barn2 libraries and dependencies.

= 1.8.2 =
Release date 22 November 2022

* Fix: FacetWP integration was returning all results when no documents were found.
* Dev: Updated Barn2 libraries.

= 1.8.1 =
Release date 2 November 2022

* Fix: Close icon on the preview lightbox could not be clicked.
* Fix: Result count in the grid layout was inaccurate when performing a search without results.
* Fix: The `no_docs_message` and `no_docs_filtered_message` shortcode options were not working in the grid layout.
* Fix: PHP warnings generated on the Filters step of the setup wizard.
* Fix: Version control settings were only visible after a page reload.
* Dev: Tested up to WordPress 6.1.
* Dev: Updated Barn2 libraries and dependencies.

= 1.8 =
Release date 17 October 2022

* New: Version control for your documents.
* New: Customize the folder colors and icons.
* New: Set folders as open, closed or a custom setup.
* Tweak: Various improvements to global search, including a search page setting and improved supported theme styling.
* Tweak: If no categories are available for the folder mode query then the default layout will be displayed instead.
* Tweak: The WordPress post author metabox and column are hidden by default. Re-enable via Screen Options.
* Tweak: Compatiblity with FacetWP 4.0.6 or greater.
* Fix: Having a document library in table layout and a Posts Table Pro table on the same page would break under certain configurations.
* Fix: The 'Documents' wp-admin menu would not be expanded when editing a term.
* Fix: Clicking the content in the preview modal would close the modal.
* Fix: Various undefined index PHP warnings.
* Fix: Flash of folder SVG on first page load.
* Dev: Tested up to WordPress 6.0.2.
* Dev: Updated Barn2 libraries and dependencies.

= 1.7.2 =
Release date 23 August 2022

* New: Added Swedish translation.
* Fix: Folder tree would not generate correctly with deep hierarchies and empty intermediary categories.
* Fix: Pagination did not work when performing a folder search with results in the grid layout.
* Fix: The `grid_columns` shortcode option did not work.
* Fix: Remove unnecessary extra double quote in the folder tree HTML.
* Dev: Added new WordPress filters.
* Dev: Updated Barn2 libraries.

= 1.7.1 =
Release date 1 August 2022

* Fix: Featured images could be hidden in grid mode.

= 1.7 =
Release date 28 July 2022

* New: Added support for custom taxonomies in the CSV importer.
* New: Automatic featured image for documents linked to image files.
* Tweak: Grid layout pagination is now consistent with the table layout pagination.
* Fix: There was no success notice when saving settings.
* Fix: Using pagination on grid layout with an active search term cleared the search term.
* Fix: Grid layout featured images overflowed the container on some themes.
* Dev: Added support for auto updates.
* Dev: Updated dependencies and Barn2 libraries.
* Dev: Tested up to WordPress 6.0.1.

= 1.6.4 =
Release date 11 July 2022

* Tweak: Label for folders order adjusted.
* Fix: Single document main content mobile layout.
* Dev: Updated dependencies and Barn2 libraries.

= 1.6.3 =
Release date 22 June 2022

* Fix: The single document sidebar would overflow under certain theme layouts.
* Fix: Audio and video embeds would not always display in grid mode.
* Fix: Folder mode would generate inaccurate category trees when using custom taxonomies in the `term` option.
* Dev: Updated dependencies and Barn2 libraries.

= 1.6.2 =
Release date 25 May 2022

* Tweak: Ensure that the Grid and Folder modes respect the minimum search term length.
* Fix: Custom filters option in the setup wizard would not display the custom filters input field.
* Dev: Added a custom args filter for modifying the display of the global search results.
* Dev: Tested up to WordPress 6.0.

= 1.6.1 =
Release date 25 April 2022

* Tweak: SearchWP integration now supports enabling the indexing of document file content via the SearchWP Engine options.
* Fix: File upload errors would not display on the Drag and drop file importer.
* Fix: Tags would not be assigned to documents when performing a CSV import.
* Fix: Folder search would not work if the lazy load option was present on the document library.
* Dev: Added a custom args filter for the FacetWP integration.
* Dev: Tested up to WordPress 5.9.3.

= 1.6 =
Release date 12 April 2022

* New: Download counts - track the number of downloads for each document with the option to display it on the table and grid layouts.
* New: Document author taxonomy which can be displayed on the table and single document.
* New: Set the number of columns to display in the grid layout.
* New: Options to sort folders.
* New: Change the permalink for the single document URL.
* New: Taxonomy dropdown filters added to the document list page in wp-admin.
* New: FacetWP integration.
* New: Added Dutch translations.
* Tweak: Improved responsive handling of images on the grid layout.
* Tweak: Consistent title naming when creating documents via Bulk and Media Library import tools.
* Tweak: Better handling of special characters in external URLs when using the CSV importer.
* Tweak: Support any type of Dropbox URL in the CSV importer.
* Tweak: Document category permalinks are now hierarchical.
* Fix: Filter dropdowns on the table had usability issues when the WP admin bar was present.

= 1.5.2 =
Release date 14 March 2022

* Fix: Global search would not redirect to the search results page.
* Dev: Updated knowledge base link in settings.
* Dev: Tested up to WordPress 5.9.2.

= 1.5.1 =
Release date 14 March 2022

* Tweak: The search results page is now noindexed.
* Fix: Table filter dropdowns styling issue with certain WooCommerce setups.
* Fix: No documents message was appearing outside of the search results page.
* Fix: The reset button option could not be unchecked in settings.
* Dev: Update Barn2 setup wizard library.

= 1.5 =
Release date 1 March 2022

* New: Search box for grid layout.
* New: Search box for folder view.
* New: Global document search, including widget and shortcode.
* New: SearchWP integration.
* Fix: The lightbox in the grid layout was displaying images with an incorrect aspect ratio.
* Fix: The grid layout cards were missing a top border when configured without an image.
* Fix: Path traversal in CSV importer.

= 1.4.2 =
Release date 7 January 2022

* Fix: CSV import of custom fields always converted keys to lowercase characters.

= 1.4.1 =
Release date 1 December 2021

* Fix: Terms order wrongly defaulting to ID instead of name.

= 1.4 =
Release date 29 November 2021

* New: Added support for Barn2 setup wizard.
* Fix: Add File button was not functional when Content was not selected as a Document data option.

= 1.3.4 =
Release date 16 November 2021

* Tweak: Better handling of large images in the preview modal.
* Fix: Custom filters would be cleared when setting Clickable fields to none.
* Dev: Updated DataTables library to 1.11.3.
* Dev: Tested up to WordPress 5.8.2

= 1.3.3 =
Release date 3 November 2021

* Fix: Replace file button was not visible on the Edit Document page in wp-admin.
* Fix: Term order was not respected for nested subcategories in folder mode.
* Fix: Bulk and CSV importing tools not initiating correctly when using string translation plugins.
* Dev: Updated library.

= 1.3.2 =
Release date 25 October 2021

* Fix: Updating settings would result in the [doc_library] shortcode being duplicated on the Document Library page.

= 1.3.1 =
Release date 21 October 2021

* Tweak: Improved the design of grid cards.
* Fix: Folders setting could not be unchecked via the settings page once enabled.
* Dev: Added various hooks and filters for customizing grid cards.

= 1.3 =
Release date 14 October 2021

* New: Display documents in a grid layout.
* Tweak: Improved mobile usability of the preview modal, particularly with media embeds.
* Fix: Certain PDFs would not render correctly in iOS browsers.
* Fix: The 'download' HTML attribute was incorrectly applied to links under certain conditions.

= 1.2.3 =
Release date 14 September 2021

* Tweak: Prevent duplication of existing files in the media library when using the CSV importer.
* Tweak: Further improvements to folder mode with large numbers of categories.
* Dev: Tested up to WordPress 5.8.1.

= 1.2.2 =
Release date 23 August 2021

* Tweak: Cleaner title names when bulk importing documents via drag and drop or the media library.
* Tweak: Allow higher post limits in folder mode to cater for large numbers of categories.

= 1.2.1 =
Release date 4 August 2021

* Fix: Document link buttons were visible before the document table loaded.
* Fix: CSS conflict with the Uncode theme affected the Document link column layout.

= 1.2 =
Release date 27 July 2021

* New: Option to allow previewing PDF and media files in a lightbox.
* New: Option to download multiple documents at once in a zip file.
* New: Options to change the appearance of the preview and download buttons.
* Fix: Custom taxonomies did not appear in the Documents admin menu.
* Fix: Folders option had unexpected results when the shortcode query options returned no documents.
* Dev: Added filters to allow icon replacement with custom SVGs.
* Dev: Tested up to WordPress 5.8.

= 1.1.2 =
Release date 29 June 2021

* Tweak: Support term ordering for the folders display option.

= 1.1.1 =
Release date 26 April 2021

* Tweak: Improved column sizing for download buttons on the document table.
* Fix: Document excerpts could display in the content column on document tables under certain conditions.

= 1.1 =
Release date 2 April 2021

* New: Added the ability to enable comments on the document post type.
* New: Options to choose what data should display on the single document page.
* Fix: Allow renaming of filter headings in the shortcode attributes.

= 1.0.2 =
Release date 15 March 2021

* Tweak: Automatically determine the file type for external urls.
* Tweak: Added the HTML5 download and type attribute for direct links in the document tables.
* Fix: Added missing translations for File Type and Link.

= 1.0.1 =
Release date 8 March 2021

* Fix: Hide document details sidebar when the document post is password protected.

= 1.0 =
Release date 24 February 2021

* New: Initial release.
