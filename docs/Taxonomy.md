---
description: >-
  Taxonomy is an Abstract Class which can be extended within your codebase, to
  create custom taxonomies, easily and cleanly.
---

# Taxonomy

As with all classes which implement the Registerable interface, adding the taxonomy to the registration config file, will handle all the registration for you.

## Fields

### $singular
> @var string  
> @required

The singular label for the taxonomy. **This is required**

### $plural
> @var string  
> @required

The plural label for the taxonomy. **This is required**

### $slug
> @var string  
> @required

The plural label for the taxonomy. **This is required**

### $label
> @var string  

xx

### $description
> @var string  
> @default ''

xx

### $object_type
> @var string[]  
> @default ['post'] (**post** post type)  

xx

### $hierarchical
> @var bool
> @default false

xx

### $show_ui
> @var bool
> @default true

xx

### $show_in_menu
> @var bool
> @default true

xx

### $show_admin_column
> @var bool
> @default true

xx

### $show_tagcloud
> @var bool
> @default false

xx

### $show_in_quick_edit
> @var bool
> @default true

xx

### $sort
> @var bool
> @default true

xx

### $meta_box_cb
> @var callable|null

xx

### $show_in_rest
> @var bool
> @default false

xx

### $rest_base
> @var string|null

xx


### $rest_controller_class
> @var string
> @default 'WP_REST_Terms_Controller'

xx

### $public
> @var bool
> @default true

xx

### $publicly_queryable
> @var bool
> @default true

xx

### $query_var
> @var bool|string
> @default false

xx

### $rewrite
> @var array<string, mixed>|bool
> @default true

xx

### $update_count_callback
> @var string|bool

xx

### $capabilities
> @var array<string, mixed>|null

xx

### $default_term
> @var array<string, mixed>|null

xx

### $meta_data
> @var array<Meta_Data>

xx