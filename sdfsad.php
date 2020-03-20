<?php
  /*
	* Plugin name: Project Portal
	* Plugin URI: https://waaS-pro.com
	* Description: manage projects 
	* Version:     	1.0.0
	* Author:      	WaaS-Pro.com
	* Author URI:  	http://www.WaaS-Pro.com
	* License:     	GPL2 etc
  */

//--------------------------------------------------------------------//
//--------------------------Define Constants--------------------------//
//--------------------------------------------------------------------//

defined('ABSPATH') || die();
define('PRO_DIR_STOCK', 	dirname(__FILE__) . '/');
define( 'PRO_URL_STOCK', plugin_dir_url( __FILE__ ) );

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

register_activation_hook( __FILE__, array( 'Projects_Portal_Pro', 'plugin_activation' ) );
register_uninstall_hook( __FILE__, array( 'Projects_Portal_Pro', 'plugin_uninstall' ) );

//--------------------------------------------------------------------//
//----------------------------Define Class----------------------------//
//--------------------------------------------------------------------//	

if (!class_exists('Projects_Portal_Pro'))
{
	class Projects_Portal_Pro
	{
		public function __construct()
		{
			//----------------------Plugin Intialistion---------------------//
			add_action('init', array(&$this, 'Plugin'), 0);
		}
		
		
				
		public function Plugin()
		{
			
			
			
			$role = get_role( 'deve' );

$role->remove_cap( 'edit_others_posts' );
$role->remove_cap( 'read' );


			$service_slug = 'blitz-project';
			$menu_name = esc_html__('Project Portal', 'blitz-core');
			$services = "Project";
 
			// Register custom post type - Team
			register_post_type('blitz-project',
				array(
					'labels' => array(
					'name' => $menu_name,
					'singular_name' => sprintf(esc_html__('%s Post', 'blitz-core' ), $services),
					'all_items' => sprintf(esc_html__('%s', 'blitz-core' ), 'Projects'),
					'add_new' => esc_html__('Add New', 'blitz-core') ,
					'add_new_item' => sprintf(esc_html__('Add New %s', 'blitz-core' ), $services),
					'edit' => esc_html__('Edit', 'blitz-core') ,
					'edit_item' => sprintf(esc_html__('Edit %s', 'blitz-core' ), $services),
					'new_item' => sprintf(esc_html__('New %s', 'blitz-core' ), $services),
					'view_item' => sprintf(esc_html__('View %s', 'blitz-core' ), $services),
					'search_items' => sprintf(esc_html__('Search %s', 'blitz-core' ), $services),
					'not_found' => esc_html__('Nothing found in the Database.', 'blitz-core') ,
					'not_found_in_trash' => esc_html__('Nothing found in Trash', 'blitz-core') ,
					'parent_item_colon' => ''
				) ,
					'public' => true,
					'publicly_queryable' => true,
					'exclude_from_search' => false,
					//'show_ui' => true,
					'query_var' => true,
					'menu_position' => 11,
					'menu_icon' => 'dashicons-welcome-add-page',
					'rewrite' => array(
						'slug' => $service_slug,
						'with_front' => false
				),
					'has_archive' => true,
					'capability_type' => array(
  'edit_post'          => 'edit_blitz-project', 
  'read_post'          => 'read_blitz-project', 
  'delete_post'        => 'delete_blitz-project', 
  'edit_posts'         => 'edit_blitz-projects', 
  'edit_others_posts'  => 'edit_others_blitz-projects', 
  'publish_posts'      => 'publish_blitz-projects',       
  'read_private_posts' => 'read_private_blitz-projects', 
  'create_posts'       => 'edit_blitz-projects', 
),
					'hierarchical' => true,
					'supports' => array(
						'title',
						'editor',
						'thumbnail',
						'excerpt',
						//'revisions',
						'comments',
						//~ 'custom-fields',
						//~ 'page-attributes',
						//'post-formats' ,
						//~ 'author',
						//~ 'trackbacks'
					)
				)
			);

			//add_action('add_meta_boxes', array( $this,'wporg_add_custom_box'));
			add_action('add_meta_boxes', array($this, 'add'));
			add_action('save_post', array($this, 'save'));
			
			add_action( 'admin_enqueue_scripts', array( $this, 'project_pr_enqueue' ) );
			
			
		}
		
		
public function add(){
	
        $screens = ['blitz-project'];
        foreach ($screens as $screen) {
            add_meta_box(
                'pppCredentials',          // Unique ID
                'Credentials', // Box title
                array($this, 'html_credentials'),   // Content callback, must be of type callable
                $screen, "side"                // Post type
            );
            
            add_meta_box('pppFiles', 'Files', array($this, 'html_files'), $screen);
            add_meta_box('pppDevelopers', 'Assign Developers', array($this, 'html_assignDeveloper'), $screen);
            add_meta_box('pppStatus', 'Project Status', array($this, 'html_projectStatus'), $screen,'side');
            add_meta_box('pppBudget', 'Project Budget', array($this, 'html_projectBudget'), $screen,'side');
            add_meta_box('pppDeadline', 'Project Deadline', array($this, 'html_projectDeadline'), $screen,'side');
        }
    }
    
 
    public function save($post_id)
    {
        if (array_key_exists('wporg_field', $_POST)) {
            update_post_meta($post_id,'_wporg_meta_key',$_POST['wporg_field']);
        }
        if (array_key_exists('project-credentials-one', $_POST)) {
            update_post_meta($post_id,'project-credentials',$_POST['project-credentials-one']);
        }
        
        if (array_key_exists('project-status', $_POST)) {
            update_post_meta($post_id,'project-status',$_POST['project-status']);
        } 
        
        if (array_key_exists('project-attachment', $_POST)) {
            update_post_meta($post_id,'project-attachment',$_POST['project-attachment']);
        } 
        if (array_key_exists('project-developers', $_POST)) {
            update_post_meta($post_id,'project-developers',$_POST['project-developers']);
        } 
        
        if (array_key_exists('project-budget-actual', $_POST)) {
            update_post_meta($post_id,'project-budget-actual',$_POST['project-budget-actual']);
        } 
        
        if (array_key_exists('project-budget', $_POST)) {
            update_post_meta($post_id,'project-budget',$_POST['project-budget']);
        } 
        if (array_key_exists('project-deadline-actual', $_POST)) {
            update_post_meta($post_id,'project-deadline-actual',$_POST['project-deadline-actual']);
        } 
        
        if (array_key_exists('project-deadline', $_POST)) {
            update_post_meta($post_id,'project-deadline',$_POST['project-deadline']);
        } 
        
        
    }
    

 
	public function html_credentials($post)
	{
		$projectCredentials = "";
		$projectCredentials = get_post_meta($post->ID, 'project-credentials', true);
        ?>
        
        <div class="form-group">
			<div class="project-credentials-one">
												
				<?php 
				wp_editor( $projectCredentials,'project-credentials-one', array('textarea_name'=>'project-credentials-one','editor_class'=>'project-credentials form-control','textarea_rows'=>4) ); ?>
			</div>
		</div>
        <?php
    }
    
    public function html_files($post)
    {
		$projectfiles = array();
         $projectfiles = get_post_meta($post->ID, 'project-attachment', true);
        ?>
        
        <div class="form-group">
			<div class="project-files-group">
											
				<input type="button" name="upload-btn" id="upload-btn" class="btn" value="Upload Files">
									<div id="display-images">
										<?php
											if(!empty($projectfiles)){
												foreach($projectfiles as $file){
													$src = wp_get_attachment_url($file);
													$files_pr 			= get_post($file);
													$description_pr			= $files_pr->post_content;
													$caption_pr			= $files_pr->post_excerpt;
													echo'<div class="attachment"><img src="'.$src.'"><p>'.$caption_pr.'</p><p>'.$description_pr.'</p><input type="hidden" name="project-attachment[]" value="'.$file.'"></div>';
												}
											}									
										?>
									</div>
			</div>
		</div>
        <?php
    }
    
	public function html_assignDeveloper($post){
		
		$projectDevelopers = array();
		$projectDevelopers = get_post_meta($post->ID, 'project-developers', true);
		
		
        ?>
        
		<div class="form-group">
			<div class="devlopers-group">
				<select id="project-developers" name="project-developers[]" class="form-control project-developers" multiple='multiple' > 										
					<?php 
						$args = array(
							'role'    => 'developer',
							'orderby' => 'user_nicename',
							'order'   => 'ASC'
						);
						$users = get_users( $args );
						
						$sel = '';
						foreach($users as $user){
							if(in_array($user->ID, $projectDevelopers)){ $sel = 'selected'; 
								echo '<option value="'.$user->ID.'" '.$sel.' >'.$user->user_nicename.'</option>';
							}else{
								echo '<option value="'.$user->ID.'">'.$user->user_nicename.'</option>';
							}
						}
					 ?>
				</select>
			</div>
		</div>
        <?php
    }
    
    public function html_projectStatus($post)
    {
		$projectStatus = "";
		$projectStatus = get_post_meta($post->ID, 'project-status', true);
        ?>
        
		<div class="project-status-group">
			<div class="form-group">
				
				<select class="form-control project-status" name="project-status" id="project-status"  >
					<option value="started" <?php if($projectStatus == 'started'){ echo 'selected'; } ?> >Started</option>
					<option value="not-started" <?php if($projectStatus == 'not-started'){ echo 'selected'; } ?> >Not started</option>
					<option value="in-progress" <?php if($projectStatus == 'in-progress'){ echo 'selected'; } ?> >In progress</option>
					<option value="cancelled" <?php if($projectStatus == 'cancelled'){ echo 'selected'; } ?> >Cancelled</option>
					<option value="pending" <?php if($projectStatus == 'pending'){ echo 'selected'; } ?> >Pending</option>
				</select>
			</div>
		</div>
        <?php
    }
    
    public function html_projectBudget($post)
    {
		$projectActualBudget = "";
		$projectBudget = "";
		$projectActualBudget = get_post_meta($post->ID, 'project-budget-actual', true);
		$projectBudget = get_post_meta($post->ID, 'project-budget', true);
        ?>
        
		<div class="budget-group">
			
			<div class="form-group">
				<label for="project-budget-actual">Actual Budget</label>
				<input type="number" class="form-control project-budget-actual" id="project-budget-actual" name="project-budget-actual" placeholder="Actual Project Budget" value="<?php echo $projectActualBudget; ?>">
			</div>
			<div class="form-group">
				<label for="project-budget">Budget</label>
				<input type="number" class="form-control project-budget" id="project-budget" name="project-budget" placeholder="Budget" value="<?php echo $projectBudget; ?>" >
			</div>
		</div>
        <?php
    }
    
     public function html_projectDeadline($post)
    {
		$projectActualDeadline = "";
		$projectDeadline ="";
         $projectActualDeadline = get_post_meta($post->ID, 'project-deadline-actual', true);
         $projectDeadline = get_post_meta($post->ID, 'project-deadline', true);
        ?>
        
		<div class="deadline-group">
			
			<div class="form-group">
				<label for="project-deadline-actual">Actual Deadline</label>
				<input type="date" class="form-control project-deadline-actual" id="project-deadline-actual" name="project-deadline-actual" value="<?php echo $projectActualDeadline; ?>" >
			</div>
			<div class="form-group">
				<label for="project-deadline">Deadline</label><br/>d
				<input type="date" class="form-control project-deadline" id="project-deadline" name="project-deadline" value="<?php echo $projectDeadline; ?>">
			</div>
		</div>
		<script>
				jQuery(document).ready(function(){
					jQuery("#project-developers").select2();
					jQuery("#project-status").select2();
					
					Projects.init();
				});
			</script>
        <?php
    }


 
//~ public function add_new_project($post)
		//~ {
		
			
		//~ }


		public function project_pr_enqueue(){
			
			$postType= get_post_type( get_the_ID());

			if($postType=="blitz-project")
				{				
					wp_enqueue_style( 'project_bootstrap_pr_5', PRO_URL_STOCK.'/assets/select2/select2.min.css');				
					wp_enqueue_style( 'project_custom_style', PRO_URL_STOCK.'/assets/custom/css/mystyle.css');		
					wp_enqueue_script( 'project_bootstrap_pr_6', PRO_URL_STOCK.'/assets/select2/select2.full.min.js');
					wp_enqueue_script( 'project_bootstrap_pr_7', PRO_URL_STOCK.'/assets/select2/select2.min.js');
					wp_enqueue_media();
					wp_enqueue_script( 'project_custom_script', PRO_URL_STOCK.'/assets/custom/js/myscript.js');
					
				}
		}
		
		
		
		
		
		
		
		//----------------------Plugin Activation---------------------//
		public static function plugin_activation()
		{
			flush_rewrite_rules();
			$result = add_role(
				'developer',
				__( 'Developer' ),				
				
				array(
					'read'            => true, // Allows a user to read
					'create_posts'      => true, // Allows user to create new posts
					'edit_posts'        => true, // Allows user to edit their own posts
					'edit_others_posts' => true, // Allows user to edit others posts too
					'publish_posts' => true, // Allows the user to publish posts
					'manage_categories' => true, // Allows user to manage post categories
				)

			);
		}
		
		//-----------------------Plugin Uninstall---------------------//
		public static function plugin_uninstall()
		{
			flush_rewrite_rules();
		}		
	}
}

$ProjectPro = new Projects_Portal_Pro;
