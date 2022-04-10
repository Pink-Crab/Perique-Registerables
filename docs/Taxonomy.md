---
description: >-
  Taxonomy is an Abstract Class which can be extended within your codebase, to
  create custom taxonomies, easily and cleanly.
---

# Taxonomy

As with all classes which implement the Registerable interface, adding the taxonomy to the registration config file, will handle all the registration for you.

## Properties

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

## Methods



## Registering Meta_Data

You can easily add term meta to your taxonomies term.

```php
use PinkCrab\Registerables\Taxonomy;
use PinkCrab\Registerables\Meta_Data;

class Order_Type extends Taxonomy {
    
    public $singular = 'Order';
    public $plural   = 'Orders';
    public $slug     = 'acme_order';
    
    // Register meta_data
    public function meta_data(array $meta_data): void {
        $meta_data[] = ( new Meta_Data('meta_key'))
            ->type('string')
            ->description('Some term meta, that means something to someone')
            ->single(true)
            ->default('something');
        return $meta_data;
    }
}
```

## Using filter_labels()

filter_labels() can be used to either alter the predefined value or adding in new ones.

**[Default Label Values](#taxonomy-labels)**

```php
class Order_Type extends Taxonomy {
    ...
    public $singular = 'Order';
    public $plural   = 'Orders';
    ...
    
    // Show different labels based on settings.
    public function filter_labels(array $labels): array{
        
        // Alter based on a conditional
        if( (bool) get_option('use_custom_order_labels') ) {
            $labels['name'] = get_option('custom_order_label_name');
            $labels['singular_name'] = get_option('custom_order_label_singular_name');
        }
        
        // Can also be used to add in additional labels not included above.
        $labels['use_featured_image'] = 'Set as featured images';
        
        return $labels;
    }
}
```

## Using filter_args()

filter_args() can be used to alter the post types properties at run time, based on operations and current state.

```php
class Secret_Tax extends Taxonomy {
    ...
    // Assume its usually hidden.
    public $public = false;
    ...
    

    public function filter_args(array $args): array{
        
        // Get the users meta value and if true, change
        // the $public to true.
        $user_has_secret_access = get_user_meta(
             get_current_user_id(),
             'has_secret_tax_access',
             true
         );        
        
        if( (bool) $user_has_secret_access ){
            $args['public'] = true;
        }
        return $args;
    }
}
```

## Using App_Config

If you wish to make use of the App_Config class, for defining your cpt slug/key, you can do either of the following._

```php
use PinkCrab\Registerables\Taxonomy;
use PinkCrab\Perique\Application\App_Config;

class Secret_Tax extends Taxonomy {
    
    public $singular = 'Public Post';
    public $plural   = 'Public Posts';
    
    public function __construct(App_Config $config){
        $this->slug = $config->additional('secret_tax_slug');
    }
}  
```

## Taxonomy Labels