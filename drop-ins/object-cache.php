<?php
/*
Plugin Name: SpinupWP Redis Object Cache Drop-In
Plugin URI: http://wordpress.org/plugins/spinupwp/
Description: A persistent object cache backend powered by Redis. Supports Predis, PhpRedis, HHVM, replication, clustering and WP-CLI.
Version: 1.1.2
Author: Delicious Brains
Author URI: https://deliciousbrains.com/
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

// Based on Till Kruss Redis Object Cache:
// https://github.com/tillkruss/redis-cache/
*/

if ( ! defined( 'WP_REDIS_DISABLED' ) || ! WP_REDIS_DISABLED ) :

	/**
	 * Adds a value to cache.
	 *
	 * If the specified key already exists, the value is not stored and the function
	 * returns false.
	 *
	 * @param string $key The key under which to store the value.
	 * @param mixed $value The value to store.
	 * @param string $group The group value appended to the $key.
	 * @param int $expiration The expiration time, defaults to 0.
	 *
	 * @return bool              Returns TRUE on success or FALSE on failure.
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_add( $key, $value, $group = '', $expiration = 0 ) {
		global $wp_object_cache;

		return $wp_object_cache->add( $key, $value, $group, $expiration );
	}

	/**
	 * Closes the cache.
	 *
	 * This function has ceased to do anything since WordPress 2.5. The
	 * functionality was removed along with the rest of the persistent cache. This
	 * does not mean that plugins can't implement this function when they need to
	 * make sure that the cache is cleaned up after WordPress no longer needs it.
	 *
	 * @return  bool    Always returns True
	 */
	function wp_cache_close() {
		return true;
	}

	/**
	 * Decrement a numeric item's value.
	 *
	 * @param string $key The key under which to store the value.
	 * @param int $offset The amount by which to decrement the item's value.
	 * @param string $group The group value appended to the $key.
	 *
	 * @return int|bool      Returns item's new value on success or FALSE on failure.
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_decr( $key, $offset = 1, $group = '' ) {
		global $wp_object_cache;

		return $wp_object_cache->decrement( $key, $offset, $group );
	}

	/**
	 * Remove the item from the cache.
	 *
	 * @param string $key The key under which to store the value.
	 * @param string $group The group value appended to the $key.
	 * @param int $time The amount of time the server will wait to delete the item in seconds.
	 *
	 * @return bool           Returns TRUE on success or FALSE on failure.
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_delete( $key, $group = '', $time = 0 ) {
		global $wp_object_cache;

		return $wp_object_cache->delete( $key, $group, $time );
	}

	/**
	 * Invalidate all items in the cache.
	 *
	 * @param int $delay Number of seconds to wait before invalidating the items.
	 *
	 * @return bool             Returns TRUE on success or FALSE on failure.
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_flush( $delay = 0 ) {
		global $wp_object_cache;

		return $wp_object_cache->flush( $delay );
	}

	/**
	 * Retrieve object from cache.
	 *
	 * Gets an object from cache based on $key and $group.
	 *
	 * @param string $key The key under which to store the value.
	 * @param string $group The group value appended to the $key.
	 * @param bool $force Optional. Whether to force an update of the local cache from the persistent
	 *                                cache. Default false.
	 * @param bool        &$found Optional. Whether the key was found in the cache. Disambiguates a return of false,
	 *                                a storable value. Passed by reference. Default null.
	 *
	 * @return bool|mixed             Cached object value.
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_get( $key, $group = '', $force = false, &$found = null ) {
		global $wp_object_cache;

		return $wp_object_cache->get( $key, $group, $force, $found );
	}

	/**
	 * Retrieve multiple values from cache.
	 *
	 * Gets multiple values from cache, including across multiple groups
	 *
	 * Usage: array( 'group0' => array( 'key0', 'key1', 'key2', ), 'group1' => array( 'key0' ) )
	 *
	 * Mirrors the Memcached Object Cache plugin's argument and return-value formats
	 *
	 * @param array $groups Array of groups and keys to retrieve
	 *
	 * @return  bool|mixed           Array of cached values, keys in the format $group:$key. Non-existent keys false
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_get_multi( $groups ) {
		global $wp_object_cache;

		return $wp_object_cache->get_multi( $groups );
	}

	/**
	 * Increment a numeric item's value.
	 *
	 * @param string $key The key under which to store the value.
	 * @param int $offset The amount by which to increment the item's value.
	 * @param string $group The group value appended to the $key.
	 *
	 * @return int|bool      Returns item's new value on success or FALSE on failure.
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_incr( $key, $offset = 1, $group = '' ) {
		global $wp_object_cache;

		return $wp_object_cache->increment( $key, $offset, $group );
	}

	/**
	 * Sets up Object Cache Global and assigns it.
	 *
	 * @return  void
	 * @global  WP_Object_Cache $wp_object_cache WordPress Object Cache
	 *
	 */
	function wp_cache_init() {
		global $wp_object_cache;

		if ( ! ( $wp_object_cache instanceof WP_Object_Cache ) ) {
			$fail_gracefully = ! defined( 'WP_REDIS_GRACEFUL' ) || WP_REDIS_GRACEFUL;

			$wp_object_cache = new WP_Object_Cache( $fail_gracefully );
		}
	}

	/**
	 * Replaces a value in cache.
	 *
	 * This method is similar to "add"; however, is does not successfully set a value if
	 * the object's key is not already set in cache.
	 *
	 * @param string $key The key under which to store the value.
	 * @param mixed $value The value to store.
	 * @param string $group The group value appended to the $key.
	 * @param int $expiration The expiration time, defaults to 0.
	 *
	 * @return bool              Returns TRUE on success or FALSE on failure.
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_replace( $key, $value, $group = '', $expiration = 0 ) {
		global $wp_object_cache;

		return $wp_object_cache->replace( $key, $value, $group, $expiration );
	}

	/**
	 * Sets a value in cache.
	 *
	 * The value is set whether or not this key already exists in Redis.
	 *
	 * @param string $key The key under which to store the value.
	 * @param mixed $value The value to store.
	 * @param string $group The group value appended to the $key.
	 * @param int $expiration The expiration time, defaults to 0.
	 *
	 * @return bool              Returns TRUE on success or FALSE on failure.
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_set( $key, $value, $group = '', $expiration = 0 ) {
		global $wp_object_cache;

		return $wp_object_cache->set( $key, $value, $group, $expiration );
	}

	/**
	 * Switch the interal blog id.
	 *
	 * This changes the blog id used to create keys in blog specific groups.
	 *
	 * @param int $_blog_id Blog ID
	 *
	 * @return bool
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_switch_to_blog( $_blog_id ) {
		global $wp_object_cache;

		return $wp_object_cache->switch_to_blog( $_blog_id );
	}

	/**
	 * Adds a group or set of groups to the list of Redis groups.
	 *
	 * @param string|array $groups A group or an array of groups to add.
	 *
	 * @return  void
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_add_global_groups( $groups ) {
		global $wp_object_cache;

		$wp_object_cache->add_global_groups( $groups );
	}

	/**
	 * Adds a group or set of groups to the list of non-Redis groups.
	 *
	 * @param string|array $groups A group or an array of groups to add.
	 *
	 * @return  void
	 * @global WP_Object_Cache $wp_object_cache
	 *
	 */
	function wp_cache_add_non_persistent_groups( $groups ) {
		global $wp_object_cache;

		$wp_object_cache->add_non_persistent_groups( $groups );
	}

	class WP_Object_Cache {
		/**
		 * The Redis client.
		 *
		 * @var mixed
		 */
		private $redis;

		/**
		 * Track if Redis is available
		 *
		 * @var bool
		 */
		private $redis_connected = false;

		/**
		 * Check to fail gracefully or throw an exception.
		 *
		 * @var bool
		 */
		private $fail_gracefully = true;

		/**
		 * Holds the non-Redis objects.
		 *
		 * @var array
		 */
		public $cache = array();

		/**
		 * Name of the used Redis client
		 *
		 * @var bool
		 */
		public $redis_client = null;

		/**
		 * List of global groups.
		 *
		 * @var array
		 */
		public $global_groups = array(
			'blog-details',
			'blog-id-cache',
			'blog-lookup',
			'global-posts',
			'networks',
			'rss',
			'sites',
			'site-details',
			'site-lookup',
			'site-options',
			'site-transient',
			'users',
			'useremail',
			'userlogins',
			'usermeta',
			'user_meta',
			'userslugs',
		);

		/**
		 * List of groups not saved to Redis.
		 *
		 * @var array
		 */
		public $ignored_groups = array( 'counts', 'plugins' );

		/**
		 * Prefix used for global groups.
		 *
		 * @var string
		 */
		public $global_prefix = '';

		/**
		 * Prefix used for non-global groups.
		 *
		 * @var string
		 */
		public $blog_prefix = '';

		/**
		 * Track how many requests were found in cache
		 *
		 * @var int
		 */
		public $cache_hits = 0;

		/**
		 * Track how may requests were not cached
		 *
		 * @var int
		 */
		public $cache_misses = 0;

		/**
		 * Instantiate the Redis class.
		 *
		 * @param bool $fail_gracefully
		 */
		public function __construct( $fail_gracefully = true ) {
			global $blog_id, $table_prefix;

			$this->fail_gracefully = $fail_gracefully;

			$parameters = array(
				'scheme' => 'tcp',
				'host'   => '127.0.0.1',
				'port'   => 6379
			);

			foreach ( array( 'scheme', 'host', 'port', 'path', 'password', 'database' ) as $setting ) {
				$constant = sprintf( 'WP_REDIS_%s', strtoupper( $setting ) );

				if ( defined( $constant ) ) {
					$parameters[ $setting ] = constant( $constant );
				}
			}

			if ( defined( 'WP_REDIS_GLOBAL_GROUPS' ) && is_array( WP_REDIS_GLOBAL_GROUPS ) ) {
				$this->global_groups = WP_REDIS_GLOBAL_GROUPS;
			}

			if ( defined( 'WP_REDIS_IGNORED_GROUPS' ) && is_array( WP_REDIS_IGNORED_GROUPS ) ) {
				$this->ignored_groups = WP_REDIS_IGNORED_GROUPS;
			}

			$client = defined( 'WP_REDIS_CLIENT' ) ? WP_REDIS_CLIENT : null;

			if ( class_exists( 'Redis' ) && strcasecmp( 'predis', $client ) !== 0 ) {
				$client = defined( 'HHVM_VERSION' ) ? 'hhvm' : 'pecl';
			} else {
				$client = 'predis';
			}

			try {
				if ( strcasecmp( 'hhvm', $client ) === 0 ) {
					$this->redis_client = sprintf( 'HHVM Extension (v%s)', HHVM_VERSION );
					$this->redis        = new Redis();

					// Adjust host and port, if the scheme is `unix`
					if ( strcasecmp( 'unix', $parameters['scheme'] ) === 0 ) {
						$parameters['host'] = 'unix://' . $parameters['path'];
						$parameters['port'] = 0;
					}

					$this->redis->connect( $parameters['host'], $parameters['port'] );
				}

				if ( strcasecmp( 'pecl', $client ) === 0 ) {
					$this->redis_client = sprintf( 'PhpRedis (v%s)', phpversion( 'redis' ) );

					if ( defined( 'WP_REDIS_SHARDS' ) ) {
						$this->redis = new RedisArray( array_values( WP_REDIS_SHARDS ) );
					} elseif ( defined( 'WP_REDIS_CLUSTER' ) ) {
						$this->redis = new RedisCluster( null, array_values( WP_REDIS_CLUSTER ) );
					} else {
						$this->redis = new Redis();

						if ( strcasecmp( 'unix', $parameters['scheme'] ) === 0 ) {
							$this->redis->connect( $parameters['path'] );
						} else {
							$this->redis->connect( $parameters['host'], $parameters['port'] );
						}
					}
				}

				if ( strcasecmp( 'pecl', $client ) === 0 || strcasecmp( 'hhvm', $client ) === 0 ) {
					if ( isset( $parameters['password'] ) ) {
						$this->redis->auth( $parameters['password'] );
					}

					if ( isset( $parameters['database'] ) ) {
						$this->redis->select( $parameters['database'] );
					}
				}

				if ( strcasecmp( 'predis', $client ) === 0 ) {
					$this->redis_client = 'Predis';

					// Require PHP 5.4 or greater
					if ( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
						throw new Exception( 'Predis required PHP 5.4 or newer.' );
					}

					// Load bundled Predis library
					if ( ! class_exists( 'Predis\Client' ) ) {
						$predis = sprintf(
							'%s/redis-cache/includes/predis/autoload.php',
							defined( 'WP_PLUGIN_DIR' ) ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins'
						);

						if ( file_exists( $predis ) ) {
							require_once $predis;
						} else {
							throw new Exception( 'Predis library not found. Re-install Redis Cache plugin or delete object-cache.php.' );
						}
					}

					$options = array();

					if ( defined( 'WP_REDIS_SHARDS' ) ) {
						$parameters = WP_REDIS_SHARDS;
					} elseif ( defined( 'WP_REDIS_SENTINEL' ) ) {
						$parameters             = WP_REDIS_SERVERS;
						$options['replication'] = 'sentinel';
						$options['service']     = WP_REDIS_SENTINEL;
					} elseif ( defined( 'WP_REDIS_SERVERS' ) ) {
						$parameters             = WP_REDIS_SERVERS;
						$options['replication'] = true;
					} elseif ( defined( 'WP_REDIS_CLUSTER' ) ) {
						$parameters         = WP_REDIS_CLUSTER;
						$options['cluster'] = 'redis';
					}

					foreach ( array( 'WP_REDIS_SERVERS', 'WP_REDIS_SHARDS', 'WP_REDIS_CLUSTER' ) as $constant ) {
						if ( defined( 'WP_REDIS_PASSWORD' ) && defined( $constant ) ) {
							$options['parameters']['password'] = WP_REDIS_PASSWORD;
						}
					}

					$this->redis = new Predis\Client( $parameters, $options );
					$this->redis->connect();

					$this->redis_client .= sprintf( ' (v%s)', Predis\Client::VERSION );
				}

				if ( defined( 'WP_REDIS_CLUSTER' ) ) {
					$this->redis->ping( current( array_values( WP_REDIS_CLUSTER ) ) );
				} else {
					$this->redis->ping();
				}

				$this->redis_connected = true;
			} catch ( Exception $exception ) {
				$this->handle_exception( $exception );
			}

			// Assign global and blog prefixes for use with keys
			if ( function_exists( 'is_multisite' ) ) {
				$this->global_prefix = ( is_multisite() || defined( 'CUSTOM_USER_TABLE' ) && defined( 'CUSTOM_USER_META_TABLE' ) ) ? '' : $table_prefix;
				$this->blog_prefix   = ( is_multisite() ? $blog_id : $table_prefix );
			}
		}

		/**
		 * Is Redis available?
		 *
		 * @return bool
		 */
		public function status() {
			return $this->redis_connected;
		}

		/**
		 * Returns the Redis instance.
		 *
		 * @return mixed
		 */
		public function redis_instance() {
			return $this->redis;
		}

		/**
		 * Adds a value to cache.
		 *
		 * If the specified key already exists, the value is not stored and the function
		 * returns false.
		 *
		 * @param string $key The key under which to store the value.
		 * @param mixed $value The value to store.
		 * @param string $group The group value appended to the $key.
		 * @param int $expiration The expiration time, defaults to 0.
		 *
		 * @return  bool                   Returns TRUE on success or FALSE on failure.
		 */
		public function add( $key, $value, $group = 'default', $expiration = 0 ) {
			return $this->add_or_replace( true, $key, $value, $group, $expiration );
		}

		/**
		 * Replace a value in the cache.
		 *
		 * If the specified key doesn't exist, the value is not stored and the function
		 * returns false.
		 *
		 * @param string $key The key under which to store the value.
		 * @param mixed $value The value to store.
		 * @param string $group The group value appended to the $key.
		 * @param int $expiration The expiration time, defaults to 0.
		 *
		 * @return  bool                   Returns TRUE on success or FALSE on failure.
		 */
		public function replace( $key, $value, $group = 'default', $expiration = 0 ) {
			return $this->add_or_replace( false, $key, $value, $group, $expiration );
		}

		/**
		 * Add or replace a value in the cache.
		 *
		 * Add does not set the value if the key exists; replace does not replace if the value doesn't exist.
		 *
		 * @param bool $add True if should only add if value doesn't exist, false to only add when value already exists
		 * @param string $key The key under which to store the value.
		 * @param mixed $value The value to store.
		 * @param string $group The group value appended to the $key.
		 * @param int $expiration The expiration time, defaults to 0.
		 *
		 * @return  bool                   Returns TRUE on success or FALSE on failure.
		 */
		protected function add_or_replace( $add, $key, $value, $group = 'default', $expiration = 0 ) {
			$cache_addition_suspended = function_exists( 'wp_suspend_cache_addition' )
				? wp_suspend_cache_addition()
				: false;

			if ( $add && $cache_addition_suspended ) {
				return false;
			}

			$result      = true;
			$derived_key = $this->build_key( $key, $group );

			// save if group not excluded and redis is up
			if ( ! in_array( $group, $this->ignored_groups ) && $this->status() ) {
				try {
					$exists = $this->redis->exists( $derived_key );

					if ( $add == $exists ) {
						return false;
					}

					$expiration = apply_filters( 'redis_cache_expiration', $this->validate_expiration( $expiration ), $key, $group );

					if ( $expiration ) {
						$result = $this->parse_redis_response( $this->redis->setex( $derived_key, $expiration, $this->maybe_serialize( $value ) ) );
					} else {
						$result = $this->parse_redis_response( $this->redis->set( $derived_key, $this->maybe_serialize( $value ) ) );
					}
				} catch ( Exception $exception ) {
					$this->handle_exception( $exception );

					return false;
				}
			}

			$exists = isset( $this->cache[ $derived_key ] );

			if ( $add == $exists ) {
				return false;
			}

			if ( $result ) {
				$this->add_to_internal_cache( $derived_key, $value );
			}

			return $result;
		}

		/**
		 * Remove the item from the cache.
		 *
		 * @param string $key The key under which to store the value.
		 * @param string $group The group value appended to the $key.
		 *
		 * @return  bool               Returns TRUE on success or FALSE on failure.
		 */
		public function delete( $key, $group = 'default' ) {
			$result      = false;
			$derived_key = $this->build_key( $key, $group );

			if ( isset( $this->cache[ $derived_key ] ) ) {
				unset( $this->cache[ $derived_key ] );
				$result = true;
			}

			if ( $this->status() && ! in_array( $group, $this->ignored_groups ) ) {
				try {
					$result = $this->parse_redis_response( $this->redis->del( $derived_key ) );
				} catch ( Exception $exception ) {
					$this->handle_exception( $exception );

					return false;
				}
			}

			if ( function_exists( 'do_action' ) ) {
				do_action( 'redis_object_cache_delete', $key, $group );
			}

			return $result;
		}

		/**
		 * Invalidate all items in the cache.
		 *
		 * @param int $delay Number of seconds to wait before invalidating the items.
		 *
		 * @return  bool            Returns TRUE on success or FALSE on failure.
		 */
		public function flush( $delay = 0 ) {
			$delay = abs( intval( $delay ) );

			if ( $delay ) {
				sleep( $delay );
			}

			$results     = [];
			$this->cache = array();

			if ( $this->status() ) {
				$salt      = $this->get_cache_key_salt();
				$selective = true; // @deprecated WP_REDIS_SELECTIVE_FLUSH

				if ( $salt && $selective ) {
					$script = "
            local i = 0
            for _,k in ipairs(redis.call('keys', '{$salt}*')) do
                redis.call('del', k)
                i = i + 1
            end
            return i
        ";

					if ( defined( 'WP_REDIS_CLUSTER' ) ) {
						try {
							foreach ( $this->redis->_masters() as $master ) {
								$redis = new Redis;
								$redis->connect( $master[0], $master[1] );
								$results[] = $this->parse_redis_response( $this->redis->eval( $script ) );
								unset( $redis );
							}
						} catch ( Exception $exception ) {
							$this->handle_exception( $exception );

							return false;
						}
					} else {
						try {
							$results[] = $this->parse_redis_response(
								$this->redis->eval(
									$script,
									$this->redis instanceof Predis\Client ? 0 : []
								)
							);
						} catch ( Exception $exception ) {
							$this->handle_exception( $exception );

							return false;
						}
					}
				} else {
					if ( defined( 'WP_REDIS_CLUSTER' ) ) {
						try {
							foreach ( $this->redis->_masters() as $master ) {
								$results[] = $this->parse_redis_response( $this->redis->flushdb( $master ) );
							}
						} catch ( Exception $exception ) {
							$this->handle_exception( $exception );

							return false;
						}
					} else {
						try {
							$results[] = $this->parse_redis_response( $this->redis->flushdb() );
						} catch ( Exception $exception ) {
							$this->handle_exception( $exception );

							return false;
						}
					}
				}

				if ( function_exists( 'do_action' ) ) {
					do_action( 'redis_object_cache_flush', $results, $delay, $selective, $salt );
				}
			}

			if ( empty( $results ) ) {
				return false;
			}

			foreach ( $results as $result ) {
				if ( ! $result ) {
					return false;
				}
			}

			return true;
		}

		/**
		 * Retrieve object from cache.
		 *
		 * Gets an object from cache based on $key and $group.
		 *
		 * @param string $key The key under which to store the value.
		 * @param string $group The group value appended to the $key.
		 * @param string $force Optional. Whether to force a refetch rather than relying on the local
		 *                                    cache. Default false.
		 * @param bool          &$found Optional. Whether the key was found in the cache. Disambiguates a return of
		 *                                    false, a storable value. Passed by reference. Default null.
		 *
		 * @return  bool|mixed                Cached object value.
		 */
		public function get( $key, $group = 'default', $force = false, &$found = null ) {
			$derived_key = $this->build_key( $key, $group );

			if ( isset( $this->cache[ $derived_key ] ) && ! $force ) {
				$found = true;
				$this->cache_hits ++;

				return is_object( $this->cache[ $derived_key ] ) ? clone $this->cache[ $derived_key ] : $this->cache[ $derived_key ];
			} elseif ( in_array( $group, $this->ignored_groups ) || ! $this->status() ) {
				$found = false;
				$this->cache_misses ++;

				return false;
			}

			try {
				$result = $this->redis->get( $derived_key );
			} catch ( Exception $exception ) {
				$this->handle_exception( $exception );

				return false;
			}

			if ( $result === null || $result === false ) {
				$found = false;
				$this->cache_misses ++;

				return false;
			} else {
				$found = true;
				$this->cache_hits ++;
				$value = $this->maybe_unserialize( $result );
			}

			$this->add_to_internal_cache( $derived_key, $value );

			$value = is_object( $value ) ? clone $value : $value;

			if ( function_exists( 'do_action' ) ) {
				do_action( 'redis_object_cache_get', $key, $value, $group, $force, $found );
			}

			if ( function_exists( 'apply_filters' ) && function_exists( 'has_filter' ) ) {
				if ( has_filter( 'redis_object_cache_get_value' ) ) {
					return apply_filters( 'redis_object_cache_get_value', $value, $key, $group, $force, $found );
				}
			}

			return $value;
		}

		/**
		 * Retrieve multiple values from cache.
		 *
		 * Gets multiple values from cache, including across multiple groups
		 *
		 * Usage: array( 'group0' => array( 'key0', 'key1', 'key2', ), 'group1' => array( 'key0' ) )
		 *
		 * Mirrors the Memcached Object Cache plugin's argument and return-value formats
		 *
		 * @param array $groups Array of groups and keys to retrieve
		 *
		 * @return  bool|mixed                               Array of cached values, keys in the format $group:$key. Non-existent keys null.
		 */
		public function get_multi( $groups ) {
			if ( empty( $groups ) || ! is_array( $groups ) ) {
				return false;
			}

			// Retrieve requested caches and reformat results to mimic Memcached Object Cache's output
			$cache = array();

			foreach ( $groups as $group => $keys ) {
				if ( in_array( $group, $this->ignored_groups ) || ! $this->status() ) {
					foreach ( $keys as $key ) {
						$cache[ $this->build_key( $key, $group ) ] = $this->get( $key, $group );
					}
				} else {
					// Reformat arguments as expected by Redis
					$derived_keys = array();

					foreach ( $keys as $key ) {
						$derived_keys[] = $this->build_key( $key, $group );
					}

					// Retrieve from cache in a single request
					try {
						$group_cache = $this->redis->mget( $derived_keys );
					} catch ( Exception $exception ) {
						$this->handle_exception( $exception );
						$group_cache = array_fill( 0, count( $derived_keys ) - 1, false );
					}

					// Build an array of values looked up, keyed by the derived cache key
					$group_cache = array_combine( $derived_keys, $group_cache );

					// Restores cached data to its original data type
					$group_cache = array_map( array( $this, 'maybe_unserialize' ), $group_cache );

					// Redis returns null for values not found in cache, but expected return value is false in this instance
					$group_cache = array_map( array( $this, 'filter_redis_get_multi' ), $group_cache );

					$cache = array_merge( $cache, $group_cache );
				}
			}

			// Add to the internal cache the found values from Redis
			foreach ( $cache as $key => $value ) {
				if ( $value ) {
					$this->cache_hits ++;
					$this->add_to_internal_cache( $key, $value );
				} else {
					$this->cache_misses ++;
				}
			}

			return $cache;
		}

		/**
		 * Sets a value in cache.
		 *
		 * The value is set whether or not this key already exists in Redis.
		 *
		 * @param string $key The key under which to store the value.
		 * @param mixed $value The value to store.
		 * @param string $group The group value appended to the $key.
		 * @param int $expiration The expiration time, defaults to 0.
		 *
		 * @return  bool               Returns TRUE on success or FALSE on failure.
		 */
		public function set( $key, $value, $group = 'default', $expiration = 0 ) {
			$result      = true;
			$derived_key = $this->build_key( $key, $group );

			// save if group not excluded from redis and redis is up
			if ( ! in_array( $group, $this->ignored_groups ) && $this->status() ) {
				$expiration = apply_filters( 'redis_cache_expiration', $this->validate_expiration( $expiration ), $key, $group );

				try {
					if ( $expiration ) {
						$result = $this->parse_redis_response( $this->redis->setex( $derived_key, $expiration, $this->maybe_serialize( $value ) ) );
					} else {
						$result = $this->parse_redis_response( $this->redis->set( $derived_key, $this->maybe_serialize( $value ) ) );
					}
				} catch ( Exception $exception ) {
					$this->handle_exception( $exception );

					return false;
				}
			}

			// if the set was successful, or we didn't go to redis
			if ( $result ) {
				$this->add_to_internal_cache( $derived_key, $value );
			}

			if ( function_exists( 'do_action' ) ) {
				do_action( 'redis_object_cache_set', $key, $value, $group, $expiration );
			}

			return $result;
		}

		/**
		 * Increment a Redis counter by the amount specified
		 *
		 * @param string $key
		 * @param int $offset
		 * @param string $group
		 *
		 * @return int|bool
		 */
		public function increment( $key, $offset = 1, $group = 'default' ) {
			$derived_key = $this->build_key( $key, $group );
			$offset      = (int) $offset;

			// If group is a non-Redis group, save to internal cache, not Redis
			if ( in_array( $group, $this->ignored_groups ) || ! $this->status() ) {
				$value = $this->get_from_internal_cache( $derived_key, $group );
				$value += $offset;
				$this->add_to_internal_cache( $derived_key, $value );

				return $value;
			}

			// Save to Redis
			try {
				$result = $this->parse_redis_response( $this->redis->incrBy( $derived_key, $offset ) );

				$this->add_to_internal_cache( $derived_key, (int) $this->redis->get( $derived_key ) );
			} catch ( Exception $exception ) {
				$this->handle_exception( $exception );

				return false;
			}

			return $result;
		}

		/**
		 * Alias of `increment()`.
		 *
		 * @param string $key
		 * @param int $offset
		 * @param string $group
		 *
		 * @return bool
		 */
		public function incr( $key, $offset = 1, $group = 'default' ) {
			return $this->increment( $key, $offset, $group );
		}

		/**
		 * Decrement a Redis counter by the amount specified
		 *
		 * @param string $key
		 * @param int $offset
		 * @param string $group
		 *
		 * @return int|bool
		 */
		public function decrement( $key, $offset = 1, $group = 'default' ) {
			$derived_key = $this->build_key( $key, $group );
			$offset      = (int) $offset;

			// If group is a non-Redis group, save to internal cache, not Redis
			if ( in_array( $group, $this->ignored_groups ) || ! $this->status() ) {
				$value = $this->get_from_internal_cache( $derived_key, $group );
				$value -= $offset;
				$this->add_to_internal_cache( $derived_key, $value );

				return $value;
			}

			try {
				// Save to Redis
				$result = $this->parse_redis_response( $this->redis->decrBy( $derived_key, $offset ) );

				$this->add_to_internal_cache( $derived_key, (int) $this->redis->get( $derived_key ) );
			} catch ( Exception $exception ) {
				$this->handle_exception( $exception );

				return false;
			}

			return $result;
		}

		/**
		 * Render data about current cache requests
		 *
		 * @return string
		 */
		public function stats() {
			?>

            <p>
                <strong>Redis Status:</strong> <?php echo $this->status() ? 'Connected' : 'Not Connected'; ?><br/>
                <strong>Redis Client:</strong> <?php echo $this->redis_client; ?><br/>
                <strong>Cache Hits:</strong> <?php echo $this->cache_hits; ?><br/>
                <strong>Cache Misses:</strong> <?php echo $this->cache_misses; ?>
            </p>

            <ul>
			<?php foreach ( $this->cache as $group => $cache ) : ?>
                <li><?php printf( '%s - %sk', strip_tags( $group ), number_format( strlen( serialize( $cache ) ) / 1024, 2 ) ); ?></li>
			<?php endforeach; ?>
            </ul><?php
		}

		/**
		 * Builds a key for the cached object using the prefix, group and key.
		 *
		 * @param string $key The key under which to store the value.
		 * @param string $group The group value appended to the $key.
		 *
		 * @return  string
		 */
		public function build_key( $key, $group = 'default' ) {
			if ( empty( $group ) ) {
				$group = 'default';
			}

			$salt   = $this->get_cache_key_salt();
			$prefix = in_array( $group, $this->global_groups ) ? $this->global_prefix : $this->blog_prefix;

			return "{$salt}{$prefix}:{$group}:{$key}";
		}

		/**
		 * Convert data types when using Redis MGET
		 *
		 * When requesting multiple keys, those not found in cache are assigned the value null upon return.
		 * Expected value in this case is false, so we convert
		 *
		 * @param string $value Value to possibly convert
		 *
		 * @return  string          Converted value
		 */
		protected function filter_redis_get_multi( $value ) {
			if ( is_null( $value ) ) {
				$value = false;
			}

			return $value;
		}

		/**
		 * Convert Redis responses into something meaningful
		 *
		 * @param mixed $response
		 *
		 * @return mixed
		 */
		protected function parse_redis_response( $response ) {
			if ( is_bool( $response ) ) {
				return $response;
			}

			if ( is_numeric( $response ) ) {
				return $response;
			}

			if ( is_object( $response ) && method_exists( $response, 'getPayload' ) ) {
				return $response->getPayload() === 'OK';
			}

			return false;
		}

		/**
		 * Simple wrapper for saving object to the internal cache.
		 *
		 * @param string $derived_key Key to save value under.
		 * @param mixed $value Object value.
		 */
		public function add_to_internal_cache( $derived_key, $value ) {
			$this->cache[ $derived_key ] = $value;
		}

		/**
		 * Get a value specifically from the internal, run-time cache, not Redis.
		 *
		 * @param int|string $key Key value.
		 * @param int|string $group Group that the value belongs to.
		 *
		 * @return  bool|mixed              Value on success; false on failure.
		 */
		public function get_from_internal_cache( $key, $group ) {
			$derived_key = $this->build_key( $key, $group );

			if ( isset( $this->cache[ $derived_key ] ) ) {
				return $this->cache[ $derived_key ];
			}

			return false;
		}

		/**
		 * In multisite, switch blog prefix when switching blogs
		 *
		 * @param int $_blog_id
		 *
		 * @return bool
		 */
		public function switch_to_blog( $_blog_id ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				return false;
			}

			$this->blog_prefix = $_blog_id;

			return true;
		}

		/**
		 * Sets the list of global groups.
		 *
		 * @param array $groups List of groups that are global.
		 */
		public function add_global_groups( $groups ) {
			$groups = (array) $groups;

			if ( $this->status() ) {
				$this->global_groups = array_unique( array_merge( $this->global_groups, $groups ) );
			} else {
				$this->ignored_groups = array_unique( array_merge( $this->ignored_groups, $groups ) );
			}
		}

		/**
		 * Sets the list of groups not to be cached by Redis.
		 *
		 * @param array $groups List of groups that are to be ignored.
		 */
		public function add_non_persistent_groups( $groups ) {
			$groups = (array) $groups;

			$this->ignored_groups = array_unique( array_merge( $this->ignored_groups, $groups ) );
		}

		/**
		 * Wrapper to validate the cache keys expiration value
		 *
		 * @param mixed $expiration Incomming expiration value (whatever it is)
		 */
		protected function validate_expiration( $expiration ) {
			$expiration = is_int( $expiration ) || ctype_digit( $expiration ) ? (int) $expiration : 0;

			if ( defined( 'WP_REDIS_MAXTTL' ) ) {
				$max = (int) WP_REDIS_MAXTTL;

				if ( $expiration === 0 || $expiration > $max ) {
					$expiration = $max;
				}
			}

			return $expiration;
		}

		/**
		 * Unserialize value only if it was serialized.
		 *
		 * @param string $original Maybe unserialized original, if is needed.
		 *
		 * @return mixed Unserialized data can be any type.
		 */
		protected function maybe_unserialize( $original ) {
			if ( defined( 'WP_REDIS_IGBINARY' ) && WP_REDIS_IGBINARY && function_exists( 'igbinary_unserialize' ) ) {
				return igbinary_unserialize( $original );
			}

			// don't attempt to unserialize data that wasn't serialized going in
			if ( $this->is_serialized( $original ) ) {
				return @unserialize( $original );
			}

			return $original;
		}

		/**
		 * Serialize data, if needed.
		 *
		 * @param string|array|object $data Data that might be serialized.
		 *
		 * @return mixed A scalar data
		 */
		protected function maybe_serialize( $data ) {
			if ( defined( 'WP_REDIS_IGBINARY' ) && WP_REDIS_IGBINARY && function_exists( 'igbinary_serialize' ) ) {
				return igbinary_serialize( $data );
			}

			if ( is_array( $data ) || is_object( $data ) ) {
				return serialize( $data );
			}

			if ( $this->is_serialized( $data, false ) ) {
				return serialize( $data );
			}

			return $data;
		}

		/**
		 * Check value to find if it was serialized.
		 *
		 * If $data is not an string, then returned value will always be false.
		 * Serialized data is always a string.
		 *
		 * @param string $data Value to check to see if was serialized.
		 * @param bool $strict Optional. Whether to be strict about the end of the string. Default true.
		 *
		 * @return bool False if not serialized and true if it was.
		 */
		protected function is_serialized( $data, $strict = true ) {
			// if it isn't a string, it isn't serialized.
			if ( ! is_string( $data ) ) {
				return false;
			}

			$data = trim( $data );

			if ( 'N;' == $data ) {
				return true;
			}

			if ( strlen( $data ) < 4 ) {
				return false;
			}

			if ( ':' !== $data[1] ) {
				return false;
			}

			if ( $strict ) {
				$lastc = substr( $data, - 1 );

				if ( ';' !== $lastc && '}' !== $lastc ) {
					return false;
				}
			} else {
				$semicolon = strpos( $data, ';' );
				$brace     = strpos( $data, '}' );

				// Either ; or } must exist.
				if ( false === $semicolon && false === $brace ) {
					return false;
				}

				// But neither must be in the first X characters.
				if ( false !== $semicolon && $semicolon < 3 ) {
					return false;
				}

				if ( false !== $brace && $brace < 4 ) {
					return false;
				}
			}
			$token = $data[0];

			switch ( $token ) {
				case 's':
					if ( $strict ) {
						if ( '"' !== substr( $data, - 2, 1 ) ) {
							return false;
						}
					} elseif ( false === strpos( $data, '"' ) ) {
						return false;
					}
				// or else fall through
				// no break
				case 'a':
				case 'O':
					return (bool) preg_match( "/^{$token}:[0-9]+:/s", $data );
				case 'b':
				case 'i':
				case 'd':
					$end = $strict ? '$' : '';

					return (bool) preg_match( "/^{$token}:[0-9.E-]+;$end/", $data );
			}

			return false;
		}

		/**
		 * Handle the redis failure gracefully or throw an exception.
		 *
		 * @param \Exception $exception Exception thrown.
		 */
		protected function handle_exception( $exception ) {
			$this->redis_connected = false;

			// When Redis is unavailable, fall back to the internal cache by forcing all groups to be "no redis" groups
			$this->ignored_groups = array_unique( array_merge( $this->ignored_groups, $this->global_groups ) );

			if ( ! $this->fail_gracefully ) {
				throw $exception;
			}

			error_log( $exception );
		}

		/**
		 * Get the cache key salt
		 *
		 * @return string|null
		 */
		private function get_cache_key_salt() {
			if ( defined( 'SPINUPWP_CACHE_KEY_SALT' ) ) {
				return SPINUPWP_CACHE_KEY_SALT;
			}
			if ( getenv( 'SPINUPWP_CACHE_KEY_SALT' ) ) {
				return getenv( 'SPINUPWP_CACHE_KEY_SALT' );
			}
			if ( defined( 'WP_CACHE_KEY_SALT' ) ) {
				// @deprecated
				return WP_CACHE_KEY_SALT;
			}

			return null;
		}
	}

endif;
