<?php

class WP_Feed_Post_Thumbnail_Plugin_Test extends WP_Feed_Post_Thumbnail_TestCase {

	static $user;
	static $posts;

	public static function wpSetUpBeforeClass( $factory ) {
		self::$user  = $factory->user->create();
		self::$posts = $factory->post->create_many( 25, array(
			'post_author' => self::$user,
		) );
	}

	public static function wpTearDownAfterClass() {
		if ( is_multisite() ) {
			wpmu_delete_user( self::$user );
		} else {
			wp_delete_user( self::$user );
		}

		foreach ( self::$posts as $post ) {
			wp_delete_post( $post, true );
		}
	}

	public function set_up() {
		global $wp_rewrite;
		$this->permalink_structure = get_option( 'permalink_structure' );
		$wp_rewrite->set_permalink_structure( '' );
		$wp_rewrite->flush_rules();

		parent::set_up();

		$this->post_count   = get_option( 'posts_per_rss' );
		$this->excerpt_only = get_option( 'rss_use_excerpt' );
	}

	public function tear_down() {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( $this->permalink_structure );
		$wp_rewrite->flush_rules();

		parent::tear_down();
	}

	public function test_namespace() {
		$this->go_to( '/?feed=rss2' );
		$feed = $this->do_rss2();
		$xml  = xml_to_array( $feed );

		// Get the rss element
		$rss = xml_find( $xml, 'rss' );

		// There should only be one rss element
		$this->assertEquals( 1, count( $rss ) );

		// Make sure it's the RSS 2.0 feed
		$this->assertEquals( '2.0', $rss[0]['attributes']['version'] );

		// Make sure the MediaRSS namespace is there
		$this->assertEquals( 'http://search.yahoo.com/mrss/', $rss[0]['attributes']['xmlns:media'] );

		// The rss element should have exactly one child (channel)
		$this->assertEquals( 1, count( $rss[0]['child'] ) );
	}

	public function do_rss2() {
		ob_start();
		// nasty hack
		global $post;
		try {
			@require( ABSPATH . 'wp-includes/feed-rss2.php' );
			$out = ob_get_clean();
		} catch ( Exception $e ) {
			$out = ob_get_clean();
			throw( $e );
		}

		return $out;
	}

	public function test_items_no_thumbnails() {
		$this->go_to( '/?feed=rss2' );
		$feed = $this->do_rss2();
		$xml  = xml_to_array( $feed );

		// get all the rss -> channel -> item elements
		$items = xml_find( $xml, 'rss', 'channel', 'item' );

		// check each of the items against the known post data
		foreach ( $items as $key => $item ) {
			$this->assertEmpty( xml_find( $items[ $key ]['child'], 'media:content' ) );
			$this->assertEmpty( xml_find( $items[ $key ]['child'], 'media:title' ) );
			$this->assertEmpty( xml_find( $items[ $key ]['child'], 'media:thumbnail' ) );
			$this->assertEmpty( xml_find( $items[ $key ]['child'], 'media:description' ) );
			$this->assertEmpty( xml_find( $items[ $key ]['child'], 'media:copyright' ) );
		}
	}

	public function test_items() {
		// Add post thumbnail to the first post
		$this->add_post_thumbnail( end( self::$posts ) );

		$this->go_to( '/?feed=rss2' );
		$feed = $this->do_rss2();
		$xml  = xml_to_array( $feed );

		// get all the rss -> channel -> item elements
		$items = xml_find( $xml, 'rss', 'channel', 'item' );

		// check each of the items against the known post data
		foreach ( $items as $key => $item ) {
			// Get post for comparison
			$guid = xml_find( $items[ $key ]['child'], 'guid' );
			preg_match( '/\?p=(\d+)/', $guid[0]['content'], $matches );
			$post = get_post( $matches[1] );

			if ( has_post_thumbnail( $post->ID ) ) {
				$thumbnail      = get_post_thumbnail_id( $post->ID );
				$img_attr       = wp_get_attachment_image_src( $thumbnail, 'full' );
				$img_attr_thumb = wp_get_attachment_image_src( $thumbnail, 'thumbnail' );

				// Full image
				$media_content = xml_find( $items[ $key ]['child'], 'media:content' );
				$this->assertEquals( $img_attr[0], $media_content[0]['attributes']['url'] );
				$this->assertEquals( 'image', $media_content[0]['attributes']['medium'] );
				$this->assertEquals( 'image/jpeg', $media_content[0]['attributes']['type'] );
				$this->assertEquals( $img_attr[1], $media_content[0]['attributes']['width'] );
				$this->assertEquals( $img_attr[2], $media_content[0]['attributes']['height'] );

				// Thumbnail
				$media_thumb = xml_find( $media_content[0]['child'], 'media:thumbnail' );
				$this->assertEquals( $img_attr_thumb[0], $media_thumb[0]['attributes']['url'] );
				$this->assertEquals( $img_attr_thumb[1], $media_thumb[0]['attributes']['width'] );
				$this->assertEquals( $img_attr_thumb[2], $media_thumb[0]['attributes']['height'] );
			}
		}
	}

	public function add_post_thumbnail( $post ) {
		add_theme_support( 'post-thumbnails' );

		// create attachment
		$filename = dirname( __FILE__ ) . '/images/a2-small.jpg';
		$contents = file_get_contents( $filename );
		$upload   = wp_upload_bits( 'a2-small.jpg', null, $contents );

		$attachment = array(
			'post_title'     => 'Post Thumbnail',
			'post_type'      => 'attachment',
			'post_mime_type' => 'image/jpeg',
			'guid'           => $upload['url']
		);

		$attachment_id = wp_insert_attachment( $attachment, $upload['file'] );
		set_post_thumbnail( $post, $attachment_id );
	}

	public function test_items_options() {
		// Add post thumbnail to the first post
		$this->add_post_thumbnail( end( self::$posts ) );

		// Disable author and description options
		update_option( 'wp-feed-post-thumbnail_options', array(
			'author'      => false,
			'description' => false,
		) );

		$this->go_to( '/?feed=rss2' );
		$feed = $this->do_rss2();
		$xml  = xml_to_array( $feed );

		// get all the rss -> channel -> item elements
		$items = xml_find( $xml, 'rss', 'channel', 'item' );

		// check each of the items against the known post data
		foreach ( $items as $key => $item ) {
			// Get post for comparison
			$guid = xml_find( $items[ $key ]['child'], 'guid' );
			preg_match( '/\?p=(\d+)/', $guid[0]['content'], $matches );
			$post = get_post( $matches[1] );

			if ( has_post_thumbnail( $post->ID ) ) {
				// Full image
				$media_content = xml_find( $items[ $key ]['child'], 'media:content' );

				$this->assertEmpty( xml_find( $media_content[0]['child'], 'media:description' ), 'Description should be empty' );
				$this->assertEmpty( xml_find( $media_content[0]['child'], 'media:copyright' ), 'Copyright should be empty' );
			}
		}
	}

	public function test_disabled_namespace_option() {
		// Disable media namespace option.
		update_option( 'wp-feed-post-thumbnail_options', array(
			'disable_namespace' => true,
		) );

		$this->go_to( '/?feed=rss2' );
		$feed = $this->do_rss2();
		$xml  = xml_to_array( $feed );

		// Get the rss element
		$rss = xml_find( $xml, 'rss' );

		// Check that the media namespace is not present.
		$this->assertArrayNotHasKey( 'xmlns:media', $rss[0]['attributes'] );
	}
}
