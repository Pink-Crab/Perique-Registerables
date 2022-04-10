---
description: >-
  MetaBoxes can be constructed and used as either parts of registered post types
  or as independently for Users, Pages and anywhere else you can naively render
  one.
---

# MetaBox

### Basic Setup

There are 2 ways to create either kind of MetaBox

```php
use PinkCrab\Modules\Registerables\MetaBox;

// Manual Instancing
$metabox = new MetaBox('my_metabox_key_1');

// Create with normal (wider) context
$metabox = MetaBox::normal('my_metabox_key_1');

// Create with advanced (wider) context
$metabox = MetaBox::advanced('my_metabox_key_1');

// Create with side context
$metabox = MetaBox::side('my_metabox_key_1');
```

Depending on your preferences, you can use the static constru_c_tor to create your MetaBox a single chained method call.

### label

The MetaBox needs a label applying, this acts as the header value.

```php
// Depending on how you instantiated your metabox, the title can be added as.

$metabox = new MetaBox('my_metabox_key_1');
$metabox->label ='My First MetaBox';

// OR

$metabox = MetaBox::normal('my_metabox_key_1')
    ->label('My First MetaBox');
```

### Context

The MetaBox can be placed using the context property. By default, this is set as normal and can either be set using the static constructors or as follows.

```php
$metabox = new MetaBox('my_metabox_key_1');
$metabox->context = 'side';
$metabox->context = 'normal';
$metabox->context = 'advanced';

// OR

$metabox->as_side(); // for 'side'
$metabox->as_advanced(); // for 'advanced'
$metabox->as_normal(); // for 'normal'
```

### Screen

You can define whichever screen you wish to render the MetaBox on. This can be defined by-passing the screen id, post type, or WP\_Screen instance. These should be passed as single values.

```php
// To render on all post and page edit.php pages.
$metabox = MetaBox::normal('my_metabox_key_1')
    ->screen('post')
    ->screen('page');
```

If you are registering your MetaBox when defining a post type, the screen is automatically added when registered. So no need to pass the post type key.

### Priority

You can use the priority value to denote when the MetaBox is loaded in context with the rest of the page. By default, this is passed as 'default' but can be 

```php
$metabox = new MetaBox('my_metabox_key_1');
$metabox->priority = 'high';
$metabox->priority = 'core';
$metabox->priority = 'default';
$metabox->priority = 'low';

// OR

MetaBox::advanced('my_metabox_key_1')
    ->priority('high'); 
MetaBox::advanced('my_metabox_key_1')
    ->priority('core'); 
    
MetaBox::advanced('my_metabox_key_1')
    ->priority('default'); 
    
MetaBox::advanced('my_metabox_key_1')
    ->priority('low'); 
```

### Add Action

Actions can be applied to MetaBoxes,  this allows for the verification and processing of additional meta fields. Any form fields added, will be passed to the global POST array. _Care should be taken when using save\_post, as this is fired when the draft post is created and before the MetaBox is rendered._   
Multiple actions can be passed, allowing for granular control over your MetaBox data.

```php
// Inline
MetaBox::advanced('my_metabox_key_1')
    ->action(
        'post_updated', 
        function($id, $after_update, $before_update){
            if(isset($_POST['my_field']){
                update_post_meta($id, 'my_meta', sanitize_text_field($_POST['my_field']);
            }
        }, 
        10, 
        3
    ); 
    

// Part of class
public function register_metabox($loader): void {
    MetaBox::advanced('my_metabox_key_1')
    ->action('post_updated', [$this, 'post_updated_callback'], 10, 3)
    ->register($loader);
}

public function post_updated_callback($post_id, $after_update, $before_update): void {
    if(isset($_POST['my_field']){
        update_post_meta($id, 'my_meta', sanitize_text_field($_POST['my_field']);
    }
}

// Using the property.
$metabox = new MetaBox('my_metabox_key_1');
$metabox->action['post_updated'] = [
    'callback' => [$this, 'post_updated_callback'],
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
// Inline
$metabox = MetaBox::normal('my_metabox_key_1')
    ->view(static function($post, $args){
        echo 'Hi from my metabox, im called statically as i do not need to be bound to the class. Micro optimisations ;) ';
    });

// OR 

$metabox = new MetaBox('my_metabox_key_1');
$metabox->view = function($post, $args){
    echo 'Hi from my metabox';
};
```

### View Vars

Data can be passed through to the MetaBox view callable, unlike the native MetaBox functions. The view vars passed to the view callable are only those defined within the view\_vars\(\) method. _These are optional, can be omitted if you don't need to pass additional data._

```php
MetaBox::normal('my_metabox_key_1')
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