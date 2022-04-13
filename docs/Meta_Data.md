---
description: >-
  MetaData can be defined and registered as part of Post Type or Taxonomy definitions, but also standalone. Stand alone Meta ata can be assigned to existing Post Types or Taxonomies, but also User and Comment.
---

# Meta Data

## __construct(string $meta_key)
> @param string $meta_key

Creates a new MetaData instance with the defined meta key.

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

Post and Term meta requires a subtype being defined. This would be the post type for post meta and taxonomy for term meta. 

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

Defines the scala type of the metadata value.

```php
$meta = ( new Meta_Data('my_key') )->type('number');
```

## description(string $description): Meta_Data
> @param string $description    
> @return Meta_Data  

Allows for defining a description for the Met Data. This is used for Schema definition as part of WP Rest.

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
> @param callable(mixed $meta_value, string $meta_key, string $meta_type ): mixed  
> @return Meta_Data 

Sets a sanitizer callback which is used when getting the meta value.

```php
$meta = ( new Meta_Data('my_key') )->sanitize('sanitize_text_field');

$meta = ( new Meta_Data('my_key') )
  ->type('integer')
  ->sanitize(fn($value, $key, $type, $subtype) => absint($value));
```
> The callback

```php
/**
 * @param mixed $meta_value     The unsanitized value.
 * @param string $meta_key      The fields meta key
 * @param string $meta_type     The meta type (post, user, term or comment)
 * @param string $meta_sub_type The meta type (post type or taxonomy)
 * @return mixed
 */
function(mixed $meta_value, string $meta_key, string $meta_type string $meta_subtype){
  return something($meta_value);
}
```

## permissions(callable $auth_callback): Meta_Data
> @param callable $auth_callback  
> @return Meta_Data 

This allows for the setting of a custom auth method, to ensure the current logged-in user can add/edit the value of this meta field.

```php
$meta = ( new Meta_Data('my_key') )->permissions('__return_true');

$meta = ( new Meta_Data('my_key') )
  ->type('integer')
  ->permissions(function($allowed, $meta_key, $object_id, $user_id, $cap, $caps){
    return current_user_can( 'edit_resume', $object_id );
  });
```
```php
/**
 * @param  boolean   $allowed    Can add/update meta value.
 * @param  string    $meta_key   The meta key.
 * @param  integer   $object_id  The post/user/term/comment ID.
 * @param  integer   $user_id    The user ID.
 * @param  string    $cap        The meta capability.
 * @param  string[]  $caps       An array of capabilities.
 * @return boolean
 */
function(
  bool $allowed, 
  string $meta_key, 
  int $object_id, 
  int $user_id, 
  string $cap, 
  array $caps
){
  return something($user_id);
}
```

## rest_schema( $rest_schema ): Meta_Data
> @param array<string, mixed>|PinkCrab\WP_Rest_Schema\Argument $rest_schema  
> @return Meta_Data 

Allows for the setting of REST schema for the meta field. Defining this will automatically define the rest field. You can use an array as per [core WP](https://developer.wordpress.org/rest-api/extending-the-rest-api/schema/) or make use of the [PinkCrab\WP_Rest_Schema](https://github.com/Pink-Crab/WP_Rest_Schema) library.

```php
$meta = ( new Meta_Data('my_key') )
  ->rest_schema([
    'type' => 'string',
    'minLength' => 2,
  ]);

// Using WP_Rest_Schema
$meta = ( new Meta_Data('my_key') )
  ->rest_schema(String_Type::on('my_key')->minimum(2));
  
```

> If defining a method as `required`, ensure you set a default in the Meta_Data definition.

## rest_view(?callable $callback): Meta_Data
> @param null|?callable(array $object):mixed  
> @return Meta_Data 

The callback used to get the meta value when called via REST. 

```php
$meta = ( new Meta_Data('my_key') )
  ->rest_view(function($object){
    return get_post_meta($object['id'], 'my_key', true);
  });
```
> Will call get_{type}_meta() by default if no callback is defined. If you wish to disable this field being accessible when doing a `GET`, define the context in schema.

```php
// Callback
/**
 * @param array $object A basic array representation of the Post, Comment, Term or User
 */
function( array $object ){
  // $object['id'] will give access to the Post, Comment, Term or User ID. 
}
```

## rest_update?callable $callback): Meta_Data
> @param null|?callable(array $object):mixed  
> @return Meta_Data 

Defines the callback when updating a value from a POST/PUT request.
```php
$meta = ( new Meta_Data('my_key') )
  ->rest_update(function($value, $object){
    return update_user_meta($object['id'], 'my_key', $value);
  });
```
> Will call update_{type}_meta() by default if no callback is defined. If you wish to disable this field being accessible when doing a `POST`, define the context in schema.


```php
// Callback
/**
 * @param mixed $value  The value being updated/set
 * @param array $object A basic array representation of the Post, Comment, Term or User
 */
function( $value, array $object ){
  // $object['id'] will give access to the Post, Comment, Term or User ID. 
}
```