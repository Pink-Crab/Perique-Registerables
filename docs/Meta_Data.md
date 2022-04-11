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