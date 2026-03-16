# dalley-it/contao-catalogue

Schema-driven catalogues for **Contao 5.3++**.

This bundle provides:

- Backend catalogue containers (similar to "archives")
- Backend records with schema-defined virtual fields stored as JSON
- Optional nested record items (rows/children)
- Frontend list and reader modules
- Filtering via an indexed taxonomy column
- Prev/next navigation in the reader (within the current filter context)
- tl_news-like language linking via `languageMain`

The bundle is designed as a lightweight alternative to data-driven extensions, while keeping the editing experience close to Contao and DCA conventions.

## Package and namespaces

- Composer package: `dalley-it/contao-catalogue`
- PHP namespace: `DalleyIt\ContaoCatalogue\...`
- Bundle class: `DalleyIt\ContaoCatalogue\DaitContaoCatalogBundle`
- Database tables: `dait_cc_*`

## Database tables

- `dait_cc_catalogue` (catalogue container)
- `dait_cc_record` (records)
  - `data_json` stores schema fields
  - `idx_taxonomy` is used for filtering (single taxonomy value)
  - `idx_relation_id` is a generic relation pointer (optional)
- `dait_cc_record_item` (optional nested items for a record; supports nesting via `parent_id`)
- `dait_cc_dictionary` and `dait_cc_dictionary_item` (backend-managed option lists)

## Schemas

Schemas are PHP files located in:

`<project>/contao/catalog_schemas/<schema_key>.php`

A schema file returns an array with (typical) keys:

- `fields`: associative array of virtual field definitions
- `indexes`: mapping of database index columns to JSON paths
- `itemTypes`: optional definitions for `dait_cc_record_item.type`

An example schema is included:

`contao/catalog_schemas/example.php`

### Dictionary-backed select fields

Use `inputType => 'dictionarySelect'` to populate a select widget from a dictionary.

You can define a dictionary key either:

- per field (`dictionaryKey` in the field definition), or
- per catalogue (`dait_cc_catalogue.dictionaryKey`) as a default.

## Frontend modules

- `dait_catalogue_list` (template: `mod_dait_catalogue_list`)
  - Optional taxonomy filter via `?taxonomy=<CODE>`
- `dait_catalogue_reader` (template: `mod_dait_catalogue_reader`)
  - Reader parameter: `?item=<alias|id>`
  - Prev/next navigation respects the current `taxonomy` filter

## Installation

Install via Composer:

```bash
composer require dalley-it/contao-catalogue
```

Run the database update in Contao Manager / Install Tool.

## Configuration workflow (recommended)

1. Create a dictionary (optional) to hold taxonomy values.
2. Create a catalogue (`dait_cc_catalogue`) and set:
   - `schema_key`
   - `jumpTo` (reader page)
   - `dictionaryKey` (optional, for taxonomy select options)
3. Add records to the catalogue.
4. Place `dait_catalogue_list` on the list page and `dait_catalogue_reader` on the reader page.

## Extending

This bundle intentionally keeps the indexing model minimal (`idx_taxonomy`, `idx_relation_id`).
If you need additional filterable fields, extend the record table with more index columns and map them via `indexes` in your schema.
