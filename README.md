# iDC Citation and Export Metadata Module
This module will do / does a few things:
  * provide an endpoint to get bibliographic citations for repository items in Islandora
     * This feature is not yet implemented, but the wiring for it is. 
  * currently this provides support for metadata export
     * Field Formatters for CSV export (in `/src/Plugin/Field/FieldFormatters`)

## Bibliographic Citations

More soon - it's a work in progress.

## Metadata Export
Metadata export is performed via a few tools. One is the [Views Data Export](https://www.drupal.org/project/views_data_export) Drupal module, which provides the functionality to do the export itself, where the data is exported using a CSV serializer. Views Data Export helps create a REST endpoint through which a Solr query can be sent and data can be serialized into CSV.  A file link is returned and the user can download the results. 

The endpoint for Repository Items is available at the path `/export_items`. 
The endpoint for Collection Objects is available at the path `/export_collections`. 

Right now the export uses batch functionality, which means it will create a file on the server and share with the user a file link.  We may decide to do this another way, but if you are exported files with thousands of entries, we will want to use some sort of batch functionality.   Admittedly, the current flow is a little odd and we may want to change it. 

### Example metadata export queries

#### Export all the collection objects that have the word ‘zoo’ in them:
`https://islandora-idc.traefik.me/export_collections?query=zoo

#### Export single item: 
`https://islandora-idc.traefik.me/export_items?query=(its_nid:82)`

#### Export all repository items, down through sub collections: 
`https://islandora-idc.traefik.me/export_items?query=(itm_field_member_of:33)

You'll end up with _all_ the repository item descendents of the collection, including those that are part of all sub collections.

#### Export all direct descendants of a collection:

`https://islandora-idc.traefik.me/export_items?query=(its_field_member_of:33)

You'll end up with all the items directly in the collection specified.

### Field Formatters
There are a few field formatters, which help format particular fields during export.  This enables us to export fields in the format we want for our particular export. 


