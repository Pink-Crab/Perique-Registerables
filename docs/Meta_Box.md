---
description: >-
  Meta_Boxes can be constructed and used as either parts of registered post types
  or as independently for Users, Pages and anywhere else you can naively render
  one.
---

# Meta_Box

### Basic Setup

There are 2 ways to create either kind of Meta_Box

```php
use PinkCrab\Modules\Registerables\Meta_Box;

// Manual Instancing
$meta_box = new Meta_Box('my_meta_box_key_1');

// Create with normal (wider) context
$meta_box = Meta_Box::normal('my_meta_box_key_1');

// Create with advanced (wider) context
$meta_box = Meta_Box::advanced('my_meta_box_key_1');

// Create with side context
$meta_box = Meta_Box::side('my_meta_box_key_1');
```

Depending on your preferences, you can use the static constructor to create your Meta_Box a single chained method call.

## Properties

### $key
> @var string  
> @required  

This is set when creating the meta box `new Meta_Box('my_meta_box_key_1')` or `Meta_Box::normal('my_meta_box_key_1')`

### $label 
> @var string  
> @required

This defines the label of the Meta Box.

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->label ='My First Meta_Box';
```

### $screen
> @var string[]  

Defines which post types this meta box should be rendered as part of.

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->screen = ['post', 'page', 'my_cpt'];
```

### $context
> @var string 
> @default 'normal'  (options 'advanced'|'normal'|'side')

Defines where the Meta Box should render 

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->context = 'side';
```

### $priority 
> @var string 
> @default 'default'  ('core'|'default'|'high'|'low')

Defines the loading priority of meta boxes in the same context.

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->context = 'side';
$meta_box->priority = 'high';
```


### $view
> @var null|callable(\WP_Post, array):void  

The callback to render the view. This can be omitted if using `Renderable` to render from a template file.

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->view = function(\WP_Post $post, array $args): void {
    echo 'Hi from my meta_box';
};
```
> Any additional vars passed using `view_vars` would be accessible as `$args['args']['custom_key1']`

### $view_vars
> @var array<string, mixed>  

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->view_vars = [
    'key1' => 'value1',
    'key2' => 'value2',
]
```

> **To make use of `Renderable` and custom template files please use the following**

### $view_template
> @var string|null  

This is the path to the template, it should be defined in relation to the base view path defined at Perique setup.

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->view_template = 'path/to/template';
```

> Please note when using a template, any additional view vars are accessible via the key defined.

```php
$meta_box->view_vars = [
    'key1' => 'value1',
    'key2' => 'value2',
]

###################################
// In template
echo $key1; // 'value1'
```

### $view_data_filter
> @var callable(\WP_Post, array): array   

This allows setting a callable to be called when the args are passed to the template file. This gives a chance to add additional args and edit them at render time (to avoid race conditions caused by hook timings)

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->view_vars(['key1' => 'value1']);
$meta_box->view_data_filter = function(\WP_Post $post, array $args): array {
    $args['meta_value1'] => get_post_meta($post->ID, 'foo', true);
    // $args = ['key1' => 'value1', 'meta_value1'=> 'value from meta']
    return $args;
};
```

### $actions
> @var array<string, array{callback:callable,priority:int,params:int}>   

Defines actions which wil only be registered if the meta box is currently rendered on the edit.php for the post.

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->actions['init'] = ['callable' => 'some_function', 'priority'=> 10,'params'=> 1];
```
> Please note only a single action can be defined per hook!


## Methods

There are also a collection of helper methods which can be used to define the Meta Box fluently.

### label(string): Meta_Box
> @param string  
> @return Meta_Box  

The Meta_Box needs a label applying, this acts as the header value.

```php
$meta_box = Meta_Box::normal('my_meta_box_key_1')
    ->label('My First Meta_Box');
```

### as_side(): Meta_Box
> @return Meta_Box

These act as collection of helper methods for defining the content.

```php
$meta_box->as_side(); // for 'side'
$meta_box->as_advanced(); // for 'advanced'
$meta_box->as_normal(); // for 'normal'
```

### as_advanced(): Meta_Box
> [See as_side()](#as_side-meta_box)  

### as_normal(): Meta_Box
> [See as_side()](#as_side-meta_box)  

### Screen

You can define whichever screen you wish to render the Meta_Box on. This can be defined by-passing the screen id, post type, or WP\_Screen instance. These should be passed as single values.

```php
// To render on all post and page edit.php pages.
$meta_box = Meta_Box::normal('my_meta_box_key_1')
    ->screen('post')
    ->screen('page');
```

If you are registering your Meta_Box when defining a post type, the screen is automatically added when registered. So no need to pass the post type key.

### Priority

You can use the priority value to denote when the Meta_Box is loaded in context with the rest of the page. By default, this is passed as 'default' but can be 

```php
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->priority = 'high';
$meta_box->priority = 'core';
$meta_box->priority = 'default';
$meta_box->priority = 'low';

// OR

Meta_Box::advanced('my_meta_box_key_1')
    ->priority('high'); 
Meta_Box::advanced('my_meta_box_key_1')
    ->priority('core'); 
    
Meta_Box::advanced('my_meta_box_key_1')
    ->priority('default'); 
    
Meta_Box::advanced('my_meta_box_key_1')
    ->priority('low'); 
```

### Add Action

Actions can be applied to Meta_Boxes,  this allows for the verification and processing of additional meta fields. Any form fields added, will be passed to the global POST array. _Care should be taken when using save\_post, as this is fired when the draft post is created and before the Meta_Box is rendered._   
Multiple actions can be passed, allowing for granular control over your Meta_Box data.

```php
// Inline
Meta_Box::advanced('my_meta_box_key_1')
    ->action(
        'post_updated', 
        function($id, $after_update, $before_update){
            if( isset( $_POST['my_field'] ) {
                update_post_meta($id, 'my_meta', sanitize_text_field($_POST['my_field'])
            }
        }, 
        10, 
        3
    ); 
    

// Using the property.
$meta_box = new Meta_Box('my_meta_box_key_1');
$meta_box->action['post_updated'] = [
    'callback' => [$this, 'some_callback_method'],
    'priority' => 10,
    'params' => 3
];
```
_Priority has a default of 10 and params of 1._

## Rendering the Meta Box View

There are 2 ways to render the Meta Box view, this can either be done with a simple `callable` or `\Closure` (as per core WP) or making use the `Renderable` service.

### View

Each Meta Box has its own definable view callback, this can either be called inline or a separate method within your class.

```php
$meta_box = Meta_Box::normal('my_meta_box_key_1')
    ->view(static function($post, $args){
        echo 'Hi from my meta_box, im called statically as i do not need to be bound to the class. Micro optimisations ;) ';
    });

```

### View Vars

Data can be passed through to the Meta_Box view callable, unlike the native Meta_Box functions. The view vars passed to the view callable are only those defined within the view\_vars\(\) method. _These are optional, can be omitted if you don't need to pass additional data._

```php
Meta_Box::normal('my_meta_box_key_1')
    ->view_vars(['user' => get_current_user_id(),...])
    ->view(function( WP_Post $post, args $args): void {
        printf("Hello user with ID:%d", $args['user']);
    });
```

The data passed into your view is a merge or all view\_var and the current post as `['post']=> get_post()`

```php
<?php /** Template */ ?>

<p> This is a template for <?php echo $post->post_title;?>, 
we can access the current post using $post and all of our other passed vars as
<?php echo $user;?> & <?php echo $foo;?></p>
```

> As the post is auto added to the 'post' key, care should be taken to not overwrite it with your view vars.