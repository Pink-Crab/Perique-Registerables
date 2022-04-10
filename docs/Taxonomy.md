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
> @var string|null    
> @default Value of $plural  

The taxonomies label (Uses plural if not defined).

### $description
> @var string  
> @default ''

The taxonomy description.

### $object_type
> @var string[]  
> @default ['post'] (**post** post type)  

Which post types should this taxonomy be applied to.

### $hierarchical
> @var bool  
> @default false

Should this taxonomy have a hierarchy

### $show_ui
> @var bool  
> @default true

Should render WP_Admin UI

### $show_in_menu
> @var bool  
> @default true

Show in WP_Admin menu list.

### $show_admin_column
> @var bool  
> @default true

Show in Post Types list table of posts

### $show_tagcloud
> @var bool  
> @default false

Include in the tag cloud.

### $show_in_quick_edit
> @var bool  
> @default true

Include in quick edit.

### $sort
> @var bool  
> @default true

Should terms remain in the order added (if false will be alphabetical)

### $meta_box_cb
> @var callable|null  

Custom callback for rendering the Term meta box on edit post

### $show_in_rest
> @var bool  
> @default false

Include in rest

### $rest_base
> @var string|null

Base rest path. If not set, will use taxonomy slug


### $rest_controller_class
> @var string  
> @default 'WP_REST_Terms_Controller'  

Rest base controller.

### $public
> @var bool  
> @default true

Is this Taxonomy to be used frontend wise

### $publicly_queryable
> @var bool
> @default true

Whether the taxonomy is publicly queryable.

### $query_var
> @var bool|string
> @default false

Define a custom query var, if false with use $this->slug

### $rewrite
> @var array<string, mixed>|bool
> @default true

Rewrite the permalinks structure. If set to true will use the default of the slug.

### $update_count_callback
> @var string|bool

If blank string will use the internal counting functions. Must be a named function or invokeable class. Anonymous functions not allowed.

### $capabilities
> @var array<string, mixed>|null

Array of capabilities for the taxonomy

### $default_term
> @var array<string, mixed>|null

Sets the default term for the taxonomy

