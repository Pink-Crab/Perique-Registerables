---
description: >-
  Post_Type is an Abstract Class which can be extended within your codebase, to
  create fully customisable custom post types.
---

# Post_Type

As with all classes which implement the Registerable interface, adding the post type to the registration config file, will handle all the registration for you.

## Fields

The core register_post_type() function takes a slug and an array of labels. To make this less complicated and messy, almost all args are defined as properties.

### $key

> @var string  
> @required

The Post Type key is the internal key used for your post type, this can be overwritten if you wish to use a more seo friendly slug for archives and permalinks (see $SLUG below).

### $singular

> @var string  
> @required

The Post Types singular label. Used for "Create new {$singular}"

### $plural

> @var string  
> @required

The Post Types plural label. Used for "View {$plural}"

### $dashicon

> @var string  
> @default 'dashicons-pets'  

You can set a custom dash icon for wp-admin, you can use either DashIcons or custom icons. If no dashicon is supplied, the pets (dog paw) will be used in its place.

### $menu_position

> @var int  
> @default 60  

Define the position in wp-admin menu.

### $map_meta_cap  
> @var bool|null  
> @default false  

If set to true all meta fields assigned to the post type will inherit the same capabilities as the post type itself.


### $public

> @var bool  
> @default TRUE

Should this post type be accessible by both the frontend and within wp-admin. If set to true, will be a hidden post type, with no admin UI, permalinks,  or queryable from frontend. 

### $show_in_nav_menus

> @var bool\|null  
> @default TRUE

Should post type be included in the menu selections.

### $show_in_menu

> @var bool\|null  
> @default TRUE

Should post type be included in the main wp-admin menu

### $show_in_admin_bar

> @var bool\|null  
> @default TRUE  

If set to true (which it is by default) the post type will be included in the admin bars helper actions.

### $show_ui

> @var bool\|null  
> @default TRUE

Should post type have the post list, create/edit/delete UI in wp-admin.

### $has_archive

> @var bool\|null  
> @default TRUE

Should post type have an archive created on the frontend.

### $hierarchical

> @var bool\|null  
> @default FALSE

Should this post type have hierarchical properties?

### $excluded_from_search

> @var bool\|null  
> @default FALSE

Should this post type be excluded from the site-wide search.

### $publicly_queryable

> @var bool\|null  
> @default TRUE

Allow the Post Type to be accessible from URL params.

### $can_export

> @var bool\|null  
> @default TRUE

Allow post type to be exportable.

### $query_var

> @var bool\|string  
> @default FALSE

This can be used to assign this post type to any public query vars. [See the codex for more details.](https://codex.wordpress.org/WordPress_Query_Vars)

### $rewrite

> @var bool\|array\|null  
> @default \['slug' =&gt; $key/$slug, 'with_front' =&gt; true, 'feeds'=&gt;false, 'pages'=&gt;false\]

This can be used to set the rewrite rules for the post type. If $rewrite is left as NULL, it will be resolved to the default of  \['slug' =&gt; $key/$slug, 'with_front' =&gt; true, 'feeds'=&gt;false, 'pages'=&gt;false\].  
If you wish to have no permalinks, you can pass FALSE here, else define with your own array.  
_Please note that we use the constructor to set default if left as null._

### $capability_type

> @var string\|array  
> @default 'post'

The string to use to build the read, edit, and delete capabilities. [See the WordPress codex for more details](https://developer.wordpress.org/reference/functions/register_post_type/#capability_type)

### $supports

> @var bool\|array  
> @default \[ \]

Denotes all the edit post features supplied. If left as an empty array will include (title and editor), passing false will remove all features.

### $taxonmies

> @var array  
> @default \[ \]

All taxonomies to include with this post type. Please note if you are adding custom taxonomies using the Taxonomies Registerable, it's best to list the post types in the taxonomy and use this for core or plugin taxonomies.

### $meta_boxes

> @var array\[Meta_Box\] // PinkCrab\Registerables\Meta_Box

This can be loaded with meta boxes for this post type. The array must be populated with pre-configured Meta_Box objects. The Meta_Boxes are registered in the Post Type registration process. While they can be added directly into this property, there is an overwritable method that makes this easier.  As you can not define an object as a property in a class, you will need to use either the meta_boxes() method in constructor or child obj&gt;ect.   
See the example below.

### $meta_data

> @var array\[Meta_Data\] // PinkCrab\Registerables\Meta_Data

All Post Meta definitions which should registered for the post type. By defining these, you can have full access to the WP Meta Data api for Theme and Rest purposes.

### $show_in_rest  

> @var bool\|null  
> @default TRUE

If set to true (the default), this post type will be registered using `$rest_controller_class` to handle the actions.

### $rest_base  

> @var string\|null   
> @default null  

Defines the base of the post types endpoints. If set as null (by default), the post types key will be used.  

### $rest_controller_class

> @var string\|class-string    
> @default `WP_REST_Posts_Controller` 

Allows the use of a custom rest controller, by default the WP_REST_Posts_Controller is used. 

### $gutenberg

> @var bool  
> @default FALSE  

This allows denoting if this post type should use `gutenberg`, set to FALSE by default. 

### $template  

> @var string[]\|null   
> @default []  

Allows for the definition of a Gutenberg Template for the post type. [More details](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-templates/#custom-post-types)

```php
class My_Cpt extends Post_Type {
    ... rest of properties
    public $template = array(
        array( 'core/image', array(
            'align' => 'left',
        ) ),
        array( 'core/heading', array(
            'placeholder' => 'Add Author...',
        ) ),
        array( 'core/paragraph', array(
            'placeholder' => 'Add Description...',
        ) ),
    );
}
```

### $template_lock  
> @var bool|'all'|'insert'  
> @default false  

Setting as false will not lock or restrict the blocks as defined by the template. Setting as `all` will not allow the adding/removing/moving of blocks. `insert` will allow the moving of blocks, but not adding/removing them.



## Methods

The Post_Type class comes with a few methods you can use for setting and modifying the defined values. 

### public function meta boxes(array): array
> @param Meta_Box[]   
> @return Meta_Box[]  

This method is used for creating and defining all the meta boxes used for this post type. The method should be used to populate the $meta boxes array with partially completed Meta_Box objects, then when the Post_Type is registered, the meta boxes are automatically added and rendered.

### public function meta_data(array): array
> @param Meta_Data[]   
> @return Meta_Data[] 

This method is used to push metadata to the post type. This allows for the creation of fully populated WP_Meta data, complete with validation, permission, rest schema and defaults. Just push populated Meta_Data instances to the $meta_data array. You do not need to set the type, or subclass (post type) as these are set automatically.

### public function slug(): ?string

> @return null\|string

This returns either the defined $slug or $key if the slug isn't defined.

### public function filter_labels(array $labels): array

> @param array $labels The compiled labels array.  
> @return array

Before the labels are passed to register_post_type(), they can be filtered through this method. This allows the altering of label values, based on the result of operations. Please note this is used before the core `post_type_labels_{$post_type}` filter.

### public function filter_args(array $args): array

> @param array $args The compiled args array.  
> @return array

Like the labels, the full args array can be altered at run time as well, by overwriting this method. Please note this is used before the core `register_post_type_args` filter.

## Registering Meta_Boxes

To register Meta_Boxes, populate the $this-&gt;meta boxes property (an array) with partially constructed Meta_Box objects. When the registration process is run, they will be bound to your post type and included.

```php
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Registerables\Meta_Box;

class Public_Post_Type extends Post_Type {
    
    public $key = 'public_post_type';
    public $singular = 'Public Post';
    public $plural   = 'Public Posts';
    
    // Register meta boxes
    public function meta_boxes(array $meta_boxes): array {
        $meta_boxes = Meta_Box::normal('custom_meta_box')
            ->label( 'This is the main meta box' )
            ->view([$this, 'meta_box_1_view'])
            ->view_vars(['key' => 'value'])
            ->add_action('edit_post', [$this, 'meta_box_edit_post'], 10, 2);
            ->add_action('delete_post', [$this, 'meta_box_delete_post'], 10, 2);
        
        // If you wish to add more than one.
        $meta_boxes = Meta_Box::side('another_meta_box')
            ->label('Etc etc')
            ->view([$this, 'meta_box_2_view'])
            ->view_vars(['key2' => 'value2'])
            ......

        return $meta_boxes
    }
        
    /**
     * Render meta box
     *
     * @param WP_Post $post The post
     * @paran array $view_vars Meta box view args
     */
    public function meta_box_1_view(WP_Post $post, array $view_vars): void{
        // The values found in view_vars.
        // ['key' => 'value']
        
        echo 'Whatever you want in the Meta_Box';
    }
    
    /**
     * Save meta box.
     *
     * @param int $post_id The post Id.
     * @param WP_Post $post The post
     */
    public function meta_box_edit_post(int $post_id, WP_Post $post): void{
        // Save any post meta, or fire off actions etc.
    }
}
```

Please note if your Meta_Box is to be displayed on other post types, it's often better to register them in a separate Controller. When registered in a Post_Type object, the screen is automatically defined as this post type.

If you are adding more than 1 meta box, it's best to use shared hooks, rather than calling the same hook multiple times.

## Registering Meta_Data

You can easily add post meta to your post type.

```php
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Registerables\Meta_Data;

class Public_Post_Type extends Post_Type {
    
    public $key = 'public_post_type';
    public $singular = 'Public Post';
    public $plural   = 'Public Posts';
    
    // Register meta_data
    public function meta_data(array $meta_data): void {
        $meta_data[] = ( new Meta_Data('meta_key'))
            ->type('string')
            ->description('Some post meta, that means something to someone')
            ->single(true)
            ->default('something');
        return $meta_data;
    }
}
```

## Using filter_labels()

filter_labels() can be used to either alter the predefined value or adding in new ones.

**[Default Label Values](#post-type-labels)**

```php
class Orders_CPT extends Post_Type {
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
class Secret_CPT extends Post_Type {
    ...
    // Assume its usually hidden.
    public $public = false;
    ...
    

    public function filter_args(array $args): array{
        
        // Get the users meta value and if true, change
        // the $public to true.
        $user_has_secret_access = get_user_meta(
             get_current_user_id(),
             'has_secret_cpt_access',
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
use PinkCrab\Registerables\Post_Type;
use PinkCrab\Perique\Application\App_Config;

class Public_Post_Type extends Post_Type {
    
    public $singular = 'Public Post';
    public $plural   = 'Public Posts';
    
    public function __construct(App_Config $config){
        $this->key = $config->post_types('public_post', 'slug');
    }
}  
```

## Post Type Labels

The following labels are automatically defined, but can be changed using [`filter_labels()`](#using-filter_labels)
| Label | Default Value |
| --- | ----------- |
| name                     | **{plural name}** |
| singular_name            | **{singular name}** |
| add_new                  | Add New |
| add_new_item             | Add New **{singular name}** |
| edit_item                | Edit **{singular name}** |
| new_item                 | New **{singular name}** |
| view_item                | View **{singular name}** |
| view_items               | View **{plural name}** |
| search_items             | Search **{plural name}** |
| not_found                | No **{plural name}** found |
| not_found_in_trash       | No **{plural name}** found in Trash |
| parent_item_colon        | Parent **{plural name}**: |
| all_items                | All **{plural name}** |
| archives                 | **{plural name}** Archives |
| attributes               | **{plural name}** Attributes |
| insert_into_item         | Insert into **{plural name}** |
| uploaded_to_this_item    | Uploaded to this **{plural name}** |
| featured_image           | Featured image |
| set_featured_image       | Set featured image |
| remove_featured_image    | Remove featured image |
| use_featured_image       | Use as featured image |
| menu_name                | **{plural name}** |
| filter_items_list        | Filter **{plural name}** list |
| filter_by_date           | Filter by date |
| items_list               | **{plural name}** list |
| item_published           | **{singular name}** published |
| item_published_privately | **{singular name}** published privately |
| item_reverted_to_draft   | **{singular name}** reverted to draft |
| item_scheduled           | **{singular name}** scheduled |
| item_updated             | **{singular name}** updated |
| item_link                | **{singular name}** Link |
| item_link_description    | A link to a **{singular name}** |

> Additional or missing labels can be added via the [`filter_labels()`](#using-filter_labels) `Post_Type` method