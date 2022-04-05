---
description: >-
  Taxonomy is an Abstract Class which can be extended within your codebase, to
  create custom taxonomies, easily and cleanly.
---

# Taxonomy

## Basic Setup

You can create a very simple, public facing post type with only the need to use a key, singluar and plural titles.

```php
//@file src/Taxonomy/Simple_Tag_Taxonmy.php

class Simple_Tag_Taxonmy extends Taxonomy {
    public $slug         = 'basic_tag_tax';
	public $singular     = 'Basic Tag Taxonomy';
	public $plural       = 'Basic Tag Taxonomies';
	public $description  = 'The Basic Tag Taxonomy.';
	public $hierarchical = false;
	public $object_type = array( 'basic_cpt' );
}

/** Then add to registration array */

//@file config/registration.php
use My_Plugin\Taxonomy\Simple_Tag_Taxonmy;

return array(
    .......
    Simple_Tag_Taxonmy::class,
    .......
);
```
As with all classes which implement the Registerable interface, adding the taxonomy to the registration config file, will handle all the registration for you.

## Fields

### $singular

> @var string  
> @required

xx

### $plural

> @var string  
> @required

xx

### $slug

> @var string  
> @required

xx

### $label

> @var string  

xx

### $description

> @var string  

xx

### $object_type

> @var array<int, mixed> 

xx

### $hierarchical

> @var bool

xx

### $show_ui

> @var bool

xx

### $show_in_menu

> @var bool

xx

### $show_admin_column

> @var bool

xx

### $show_tagcloud

> @var bool

xx

### $show_in_quick_edit

> @var bool

xx

### $sort

> @var bool

xx

### $meta_box_cb

> @var callable|null

xx

### $show_in_rest

> @var bool

xx

### $rest_base

> @var string|null

xx


### $rest_controller_class

> @var string

xx

### $public

> @var bool

xx

### $publicly_queryable

> @var bool

xx

### $query_var

> @var bool|string

xx

### $rewrite

> @var array<string, mixed>|bool

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
