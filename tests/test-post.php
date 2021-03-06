<?php

class WordPress_GitHub_Sync_Post_Test extends WP_UnitTestCase {

	/**
	 * @var int
	 */
	protected $id;

	/**
	 * @var WP_Post
	 */
	protected $post;

	public function setUp() {
		parent::setUp();
		update_option( 'wpghs_repository', 'owner/repo' );
		$this->id   = $this->factory->post->create();
		$this->post = get_post( $this->id );
	}

	public function test_should_return_correct_directory() {
		$post = new WordPress_GitHub_Sync_Post( $this->id );

		$this->assertEquals( '_posts/', $post->github_directory() );
	}

	public function test_should_get_post_name() {
		$post = new WordPress_GitHub_Sync_Post( $this->id );

		$this->assertEquals( get_post( $this->id )->post_name, $post->name() );
	}

	public function test_should_build_github_content() {
		$post = new WordPress_GitHub_Sync_Post( $this->id );

		$this->assertStringStartsWith( '---', $post->github_content() );
		$this->assertStringEndsWith( 'Post content 1', $post->github_content() );
	}

	public function test_should_build_github_view_url() {
		$post = new WordPress_GitHub_Sync_Post( $this->id );

		$this->assertEquals( 'https://github.com/owner/repo/blob/master/_posts/' . get_the_date( 'Y-m-d-', $this->id ) . $this->post->post_name . '.md', $post->github_view_url() );
	}

	public function test_should_build_github_edit_url() {
		$post = new WordPress_GitHub_Sync_Post( $this->id );

		$this->assertEquals( 'https://github.com/owner/repo/edit/master/_posts/' . get_the_date( 'Y-m-d-', $this->id ) . $this->post->post_name . '.md', $post->github_edit_url() );
	}

	public function test_should_export_unpublished_to_drafts_folder() {
		$id   = $this->factory->post->create( array( 'post_status' => 'draft' ) );
		$post = new WordPress_GitHub_Sync_Post( $id );

		$this->assertEquals( '_drafts/', $post->github_directory() );
	}

	public function test_should_export_published_post_to_posts_folder() {
		$id   = $this->factory->post->create();
		$post = new WordPress_GitHub_Sync_Post( $id );

		$this->assertEquals( '_posts/', $post->github_directory() );
	}

	public function test_should_export_published_page_to_pages_folder() {
		$id   = $this->factory->post->create( array( 'post_type' => 'page' ) );
		$post = new WordPress_GitHub_Sync_Post( $id );

		$this->assertEquals( '_pages/', $post->github_directory() );
	}

	public function test_should_export_published_unknown_post_type_to_root() {
		$id   = $this->factory->post->create( array( 'post_type' => 'unknown' ) );
		$post = new WordPress_GitHub_Sync_Post( $id );

		$this->assertEquals( '', $post->github_directory() );
	}

	public function test_should_export_published_post_type_to_plural_folder() {
		register_post_type( 'widget', array(
			'labels' => array( 'name' => 'Widgets' ),
		) );
		$id   = $this->factory->post->create( array( 'post_type' => 'widget' ) );
		$post = new WordPress_GitHub_Sync_Post( $id );

		$this->assertEquals( '_widgets/', $post->github_directory() );
	}
}

