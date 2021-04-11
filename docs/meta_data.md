---
description: >-
  Meta_Data can be created directly for Post Types, Taxonomy Terms and Users, but also for custom object types. Easiy created as part of the registration process with a fluent api.
---

# Meta_Data

## Setup


```php

// Basic post meta definition.
$meta = new Meta_Data('meta_key');
$meta->post_type('post');
$meta->default('unknown');
$meta->register();

// Can be chained.
(new Meta_Data('term_meta'))
    ->taxonomy('product_cat')
    ->default(0.00)
    ->register();

```

## Public Methods

All public methods which can be used to define the post meta.

### meta_type(string): self

Meta Data can be set against Post, Term, User and Custom (see WP Docs for more details)

```php
$meta = new Meta_Data('meta_key');

$meta->meta_type('post');
$meta->meta_type('term');
$meta->meta_type('user');
$meta->meta_type('custom');

```

### object_subtype(string): self

Post, Term and Custom meta all make use of a subtype, to apply them to post types, taxonomies etc.

```php
$post_meta = new Meta_Data('post_meta');
$post_meta->meta_type('post');
$post_meta->object_subtype('page');

$term_meta = new Meta_Data('term_meta');
$term_meta->meta_type('term');
$term_meta->object_subtype('custom_tax');

```

### post_type(string): self & taxonomy(string): self

Post and Term meta are by far the most commonly used through most WordPress sites, to prevent the need to declare both meta_type and object_subtype. We have 2 useful shortcut methods, for defining either in a single expression.

```php
// Post Type
$post_meta = new Meta_Data('post_meta');
$post_meta->post_type('page');
// Replaces
// $post_meta->meta_type('post');
// $post_meta->object_subtype('page');

// Term
$term_meta = new Meta_Data('term_meta');
$term_meta->taxonomy('custom_tax');
// Replaces
// $term_meta->meta_type('term');
// $term_meta->object_subtype('custom_tax');
```

### type(string): self

Defines the scalar type for the value saved to the meta key
accepts 'string', 'boolean', 'integer', 'number', 'array', and 'object'

```php
$meta = new Meta_Data('post_meta');
$meta->type('string');
```

### description(string): self

A description can be set for this meta key

```php
$meta = new Meta_Data('post_meta');
$meta->description('represents something about something');
```

### single(bool): self

Is the meta a single value or should it stored as an indexed array
> *Defaults to false*

```php
$meta = new Meta_Data('post_meta');
$meta->single(); // Sets as true if noting passed.
$meta->single(true);
$meta->single(false);
```

### default(mixed): self

Allows the setting of a default value if not meta has been set for the item (post, term, user etc). Should match the type defined using ```type()```

```php
$meta = new Meta_Data('post_meta');
$meta->type(string);
$meta->default('Not set'); 
```

### sanitize(callable): self

A sanitisation function can be passed which the value will be passed through before setting.

```php
$meta = new Meta_Data('post_meta');
$meta->type('number'); 
$meta->sanitize('floatval');

// A callable can be used.
$meta->sanitize(static function($val){
    .. do something
    return $val;
});
```

### permissions(callable): self

A sanitisation function can be passed which the value will be passed through before setting.

```php
$meta = new Meta_Data('post_meta');
$meta->type('number'); 
$meta->permissions(static function( bool $allowed, string $meta_key, int $post_ID, int $user_id, string $cap, array $caps ): bool {
    retrun current_user_can( 'edit_others_posts' );
});
```

### rest_schema(array): self

A custom schema can be defined for the meta data.

```php
$meta = new Meta_Data('post_meta');
$meta->type('boolean'); 
$meta->default(true); 
$meta->rest_schema( array(
    'schema' => array(
        'type'  => 'boolean', 
        'default' => true,
    ) 
));
```

### parse_args(): array

The args array used to register the meta_data can be recalled with this. Mostly used internally, but useful for testing and debugging.

```php

var_dump($meta->parse_args());
/*[ 'type'              => 'string',
    'description'       => 'This is something',
    'default'           => 'something',
    'single'            => true,
    'sanitize_callback' => 'strtolower',
    'auth_callback'     => 'some_function',
    'show_in_rest'      => false,
    'object_subtype'    => 'page' ]; */

```

### get_meta_key(): string

Returns the defined meta key for the meta data.

```php
$meta = new Meta_Data('post_meta');
var_dump($meta->get_meta_key()); // 'post_meta'
```

### register(?Loader): void

When you are ready to register your meta data, you can just call the ```register()``` method. Unlike many of the abstract classes in this package, an instance of the Hook_Loader doesnt need to be passed. Although it recommended to call this as early as possible, but using a controller (or CPT/Taxonomy) that implements the Registerable interface and calling through its ```register(Loader $loader)```

```php

$meta = new Meta_Data('post_meta');
$meta->type('number'); 
$meta->post_type('post');
$meta->sanitize('floatval');
$meta->default(3.14);

$meta->register();

// As part of another controller.
class Some_Controller implements Registerable {

    /** Registers all post meta fields */
    protected function register_post_meta(): void{
        ( new Meta_Data('some_float') )
            ->type('number') 
            ->post_type('post')
            ->sanitize('floatval')
            ->default(3.14)
            ->register();

        ( new Meta_Data('some_string') )
            ->type('string') 
            ->post_type('post')
            ->default('Something')
            ->register();
    }

    /** The standard Registerable entry point */
    public function register(Loader $loader): void{
        $loader->action('something_else', [$this, 'some_other_method_in_class']);
        $this->register_post_meta();
    }
}

```
