---
description: >-
  Creates a fully self contained ajax calls, including validation, action
  registration and callbacks. All from an extendable Abstract class.
---

# Ajax

## Basic Setup

You can create simple ajax calls with only 2 properties and a callback.

```php
use PinkCrab\HTTP\HTTP;
use PinkCrab\Registerables\Ajax;
use Psr\Http\Message\ResponseInterface;

class Simple_Ajax extends Ajax {

	/**	
	 * The nonce key, must be passed in the call to the server.
	 * If not set, will not check for nonce value.
	 * @var string
	 */
	protected $nonce_handle = 'basic_ajax_get';
	
	/**	
	 * The action value given to the ajax call. 
	 * This must be passed in your call as "action" : "basic_ajax_get" 
	 * @var string
	 * @required
	 */
	protected $action       = 'basic_ajax_get';

	/**
	 * Handles the callback.
	 *
	 * @param ResponseInterface $request
	 * @return void
	 */
	public function callback( ResponseInterface $response ): ResponseInterface {
		
		// Do all of your actions here
		
		// You can add an array to the repsonse stream by using the 
		// stream_from_scala helper (creates a stream with a JSON of 
		// passed data (array or object).
		$response_array = array( 'result' => 'something' );
		return $response->withBody(
			HTTP_Helper::stream_from_scala( $response_array )
		);
	}
}
```

Once your Ajax call has been created, its just a case of adding it to the registration array and it will be registered on init.

## Fields

The core register\_post\_type\(\) function takes a slug and an array of labels. To make this less complicated and messy, almost all args are defined as properties.

### _protected_ $action

> @var string  
> @required  
> @default '

This is your primary key for the ajax call. This will be used when registering them with WordPress. Whatever value you set here, will be used in your call data.

### _protected_ $nonce\_handle

> @var string\|null  
> @default null

This is the handle used to create the Nonce for your call. Whatever you set here must be passed as part of your call using either the "nonce" key `"nonce" : "none_`_`key"`_ or whatever cause nonce\_key value you set.  
_**If it isn't set or set as null or false, it will skip the nonce check, meaning you have an insecure call.**_

### _protected_ $nonce\_field

> @var string  
> @default 'nonce'

Represents the field key of the nonce value. If you are passing as **`"securityKey": "blah"`**in your JS call, then set this to **`"securityKey": "blah"`** and the auto validation will look for the correct value.

### _protected_ $logged\_in

> @var bool  
> @default true

Will add the ajax call if the user is logged in _`wp_ajax_{$action}`_ 

### _protected_ $logged\_out

> @var bool  
> @default true

Will add the ajax call if the user is logged out _`wp_ajax_nopriv_{$action}`_

### _protected_ $request

> @var Psr\Http\Message\ServerRequestInterface  
> @default _null_

Holds the request for the call. This is set at construction and should not be overwritten.

## Methods

### _public_ function \_\_construct\( ServerRequestInterface $request \)

> @pram Psr\Http\Message\ServerRequestInterface $request

The Ajax methods are constructed with the DI Container, so you can inject dependencies, just ensure you still inject request and pass that to the parent.

```php
class Ajax_With_WPDB extends Ajax {

	protected $nonce_handle = 'basic_ajax_get';
	protected $action       = 'basic_ajax_get';
	protected $wpdb;

	// Passes wpdb in as extra depenedency
	public function __construct( ServerRequestInterface $request, wpdb $wpdb ) {
		// You must call parent constructor
		parent::__construct( $request );
		$this->wpdb = $wpdb;
	}
}
```

{% hint style="info" %}
You do not need to redeclare the dependency rules, DICE will still inject the addtionial dependencies.
{% endhint %}

### _public_ function callback\( ResponseInterface $response\)

> @param Psr\Http\Message\ResponseInterface $response  
> @return Psr\Http\Message\ResponseInterface  
> @required **Is an Abstract method.**

This is your callback for the ajax call, validation is carried out against the nonce key before this is called. However, none of the request data has been sanitized so the user is responsible for this.

{% hint style="info" %}
You can access the calls payload from the request, as per any PS7 Request using the following.  
  
GET                                `$this->request->getQueryParams();`       // array  
POST                            `(string) $this->request->getBody();`    // json string  
URL Form Encoded    `(array) $request->getParsedBody();`      //array
{% endhint %}

```php
/**
 * @param ResponseInterface $response New response instance
 * @return ResponseInterface
 */
public function callback( ResponseInterface $response ): ResponseInterface {
	
	// Get request (do some validation & sanitization!
	$request = $this->request->getQueryParams();
	
	// Do something.
	$result = do_something($request['some_key']);
	
	// Create a new Stream with our result and return to client.
	return $response->withBody(
		HTTP_Helper::stream_from_scala( array( 'result' => $result ) )
	);
}
```

By default, the **Response** is returned with a **200** status and **JSON** headers. You can alter these using the `withHeader()` and `withStatus()`.

```php
public function callback( ResponseInterface $response ): ResponseInterface {
		return $response->withHeader( 'Encoding', 'gzip,deflate' )
				->withBody( HTTP_Helper::stream_from_scala( $data )	)				
		 		->withStatus(418);
	}
```

> You can cut the **ResponseInterface** out altogether if you want and just the wp\__send\__json\(\) method.

### _public_ function set\_up\(\)

> @return void

This is run right before the Ajax call is added to the Loader, and is the last chance to make any final changes to the setup of your class. This is the ideal place to add any scripts to the scripts collection.

```php
public function set_up(): void {
   // Adds a script to enqueued
   $this->scripts->push(
      Enqueue::script( 'my_script' )
         ->src( __DIR__ . '/file.js' )
         ->deps( 'jquery' )
         ->ver( '0.1.2' )
         ->footer() // DO NOT CALL Enqueue::register() HERE!
   );
}
```

### _public_ function tare\_down\(\)

> @return void

This is  run after the ajax call and any scripts/styles have been enqueued.

### _public_ function conditional\(\)

> @return bool

Optional conditional check that can be run before enqueuing any scripts or styles. Allows for control over which pages your scripts are loaded on.

```php
public function conditional(): bool {
   // Only load the JS on cart/checkout pages.
   retrun is_cart() || is_checkout();
}
```

## Static Helper Methods

### Ajax::nonce\_field\(\)

> @return void

Prints a hidden input with the name set from `$instance->nonce_field`, value with a nonce based on the `$instance->nonce_handle` defined in the class.  
_Uses DI to construct the object to produce this field._

### Ajax::nonce\_value\(\)

> @return string

Returns the current nonce value based on the `$instance->nonce_handle` defined in the class  
_Uses DI to contsruct the object to produce this field._

### Ajax::action\(\)

> @return string

Returns the current action value based on the `$instance->action` defined in the class  
_Uses DI to construct the object to produce this field._

## Examples

```php
class Get_Users_Favourites_Ajax extends Ajax {

	// Nonce and action
	protected $nonce_handle = 'prefix_get_user_favourites';
	protected $action       = 'prefix_get_user_favourites';

	/**
	 * @var Favourites_Repository
	 */
	protected $favourites;

	public function __construct(
		ServerRequestInterface $request,
		Favourites_Repository $favourites
	) {
		parent::__construct( $request );
		$this->favourites = $favourites;
	}

	/**
	 * @return void
	 */
	public function set_up(): void {
		$this->scripts->push(
			Enqueue::script( 'user_favourites' )
				->src( Config::url('assets') . '/js/favourites/user_favourites.js' )
				->deps( 'jquery' )
				->ver( '2.1.8' )
		);
	}

	/**
	 * @return boolean
	 */
	public function conditional(): bool {
		return is_singular( 'some_cpt' );
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return ServerRequestInterface
	 */
	public function callback( ResponseInterface $response ): ResponseInterface {
		// Get the payload from the request.
		$request = $this->request->getQueryParams();
		
		// Do sanitization and validation checks.
		$user = (int) $request['user_id']; 

		// Do whatever is needed.
		$favourites = $this->favourites->get_favourites( $user ) ?? [];
		$grouped_by_type = $this->favourites->get_groups($user ) ?? [];

		// Populate our response.
		return $response->withBody(
			HTTP_Helper::stream_from_scala(
				array(
					'favourites' => $favourites,
					'count' => count($favourites),
					'groups' => $grouped_by_type
				)
			)
		);
	}
}
```

