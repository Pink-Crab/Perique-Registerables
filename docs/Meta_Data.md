---
description: >-
  Meta Data can be defined and registered as part of Post Type or Taxonomy definitions, but also standalone. Stand alone Meta Data can be assigned to existing Post Types or Taxonomies, but also User and Comment.
---

# Meta Data

## __construct(string $meta_key)
> @param string $meta_key

Creates a new Meta Data instance with the defined meta key.

```php
$meta = new Meta_Data('my_key');
```

## meta_type(string $type): Meta_Data
> @param string['post','term','comment','user'] $type  
> @return Meta_Data  

A valid Meta_Data type must be defined.

```php
$meta = new Meta_Data('my_key');

$meta->meta_type('post');    // get_post_meta(...)
$meta->meta_type('term');    // get_term_meta(...)
$meta->meta_type('user');    // get_user_meta(...)
$meta->meta_type('comment'); // get_comment_meta(...)
```

## object_subtype(string $type): Meta_Data
> @param string $type    
> @return Meta_Data  

Post and Term meta requires a sub type being defined. This would be the post type for post meta and taxonomy for term meta. 

> You can use the `post_type()` and `taxonomy()` helper methods to set both meta_type and subtype in a single method.

```php
## With Post Meta

$meta = new Meta_Data('my_key');
$meta->meta_type('post')
$meta->object_subtype('page')

// Can be expressed as
$meta = ( new Meta_Data('my_key') )->post_type('page');

## With Term Meta

$meta = new Meta_Data('my_key');
$meta->meta_type('term')
$meta->object_subtype('custom_tax')

// Can be expressed as
$meta = ( new Meta_Data('my_key') )->taxonomy('custom_tax');
```

## type($type): Meta_Data
> @param string['string', 'boolean', 'integer', 'number', 'array', 'object'] $type   
> @return Meta_Data  

Defines the scala type of the meta data's value.

```php
$meta = ( new Meta_Data('my_key') )->type('number');
```

## description(string $description): Meta_Data
> @param string $description    
> @return Meta_Data  

Allows for defining a description for the Meta Data. This is used for Schema definition as part of WP Rest.

```php
$meta = new Meta_Data('my_key');
$meta->description('This is a description for a meta data field.');
```

## single(bool $single): Meta_Data
> @param bool $single    
> @return Meta_Data  

Denotes if this is a single meta value or an array of values.

```php
$meta = new Meta_Data('my_key');
$meta->single();
```

## default(mixed $default): Meta_Data
> @param mixed $default    
> @return Meta_Data 

Lets you define a default value. If you are planning to use this meta field as part an endpoint schema, set a valid default.

```php
$meta = ( new Meta_Data('my_key') )->default('fallback');

update_post_meta($id, 'your_key', 'apple');
get_post_meta($id, 'my_key', true); // 'fallback'
get_post_meta($id, 'your_key', true); // 'apple'
```

## sanitize( callable $sanitize_callback): Meta_Data
> @param callable(mixed $meta_value, string $meta_key, string $meta_type ):<T>  
> @return Meta_Data 

Sets a sanitize callback which is used when getting the meta value.

```php
$meta = ( new Meta_Data('my_key') )->sanitize('sanitize_text_field');

$meta = ( new Meta_Data('my_key') )
  ->type('integer')
  ->sanitize(fn($value, $key, $type) => absint($value));
```
> The callback

```php
/**
 * @param mixed $meta_value The unsanitized value.
 * @param string $meta_key  The fields meta key
 * @param string $meta_type The meta type (post, user, term or comment)
 */
function(mixed $meta_value, string $meta_key, string $meta_type){
  return something($meta_value);
}
```

## permissions()