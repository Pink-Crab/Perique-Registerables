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
$meta = new Meta_Data('my_key');
$meta->meta_type('post')
$meta->object_subtype('page')

// Can be expressed as
$meta = new Meta_Data('my_key');
$meta->post_type('page');

## With Term Meta
$meta = new Meta_Data('my_key');
$meta->meta_type('term')
$meta->object_subtype('custom_tax')

// Can be expressed as
$meta = new Meta_Data('my_key');
$meta->taxonomy('custom_tax');
```
