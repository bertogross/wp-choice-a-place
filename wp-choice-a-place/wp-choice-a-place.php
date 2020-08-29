<?php
/**
* Plugin Name: Choice a Place
* Plugin URI: https://github.com/bertogross/wp-plugin-choice-a-place
* Description: Full screen: select a place and redirect.
* Author: Daniel Gross
* Version: 1.0.0
* Author URI: https://danielgross.dev/
*/

if(!defined('ABSPATH')) exit;//Exit if accessed directly

/**
* Admin Scripts
*/
function wcyp_admin_scripts() {
  wp_register_style( 'wcyp-admin-style', plugins_url('css/admin.css',__FILE__));
  wp_enqueue_style( 'wcyp-admin-style' );
  wp_register_script( 'wcyp-admin-js', plugins_url('js/admin.js',__FILE__), array() );
  wp_enqueue_script( 'wcyp-admin-js' );
}
add_action('admin_enqueue_scripts', 'wcyp_admin_scripts');

/**
* Front Scripts
*/
function wcyp_front_scripts() {
  wp_register_script( 'wcyp-front-js', plugins_url('js/front.js',__FILE__), array() );
  wp_enqueue_script( 'wcyp-front-js' );
}
add_action( 'wp_enqueue_scripts', 'wcyp_front_scripts' );


/**
 * Front HTML
 */
function wcyp_front_html(){
  if(!empty( get_option('wcyp_custom_css'))){
    echo '<style>';
    echo get_option('wcyp_custom_css');
    echo '</style>';
  }

}
add_action( 'wp_head', 'wcyp_front_html', 0 );


function wcyp_front_footer_html() {
  $wcyp_options = maybe_unserialize( get_option("wcyp_place") );
 
  $wcyp_populate_options = '';
  
  if( is_array($wcyp_options)){
    array_filter($wcyp_options);
    asort($wcyp_options);
    $wcyp_index = 0;
    foreach($wcyp_options as $wcyp_value){
      if( $wcyp_value["locale"] != '' && $wcyp_value["url"] != '' ){
        $wcyp_index++;
        $wcyp_populate_options .= PHP_EOL.'<option value="'.$wcyp_value["url"].'">'.$wcyp_value["locale"].'</option>';
      }
    }
    
    if( !empty($wcyp_populate_options) ){
      echo '<div id="wcyp-toggle">
        <div class="wcyp-wrap">
          <form>
            <label class="wcyp-label" for="wcyp-select">Escolha sua cidade</label>
            <select id="wcyp-select" class="wcyp-select">
              <option selected disabled>Qual sua cidade?</option>
              '.$wcyp_populate_options.'
            </select>
          </form>
        </div>
      </div>';
    }
  }

}
add_action( 'wp_footer', 'wcyp_front_footer_html', 0 );

/**
* Add a new top level menu link to the Admin Control Panel
*/
//https://developer.wordpress.org/reference/functions/add_menu_page/
function wcyp_admin_menu(){
  add_menu_page(
    'Choice a Place Settings',
    'Choice a Place',
    'manage_options',//Capabilitie
    'wp-choice-a-place',//Slug
    'wcyp_admin_page',//Callback function to display the page
    'dashicons-admin-site-alt',//Admin menu icon https://developer.wordpress.org/resource/dashicons/
    1269317//Menu position
  );
}
add_action('admin_menu','wcyp_admin_menu');

/**
* Function to display in admin page
*/
if ( !function_exists( 'wcyp_admin_page' ) ):
  function wcyp_admin_page(){
    $wcyp_options = maybe_unserialize( get_option("wcyp_place") );

    if(array_key_exists('submit', $_POST)){
      //update options
      update_option('wcyp_place', array_filter( $_POST['wcyp_place'] ) );
      update_option('wcyp_custom_css', $_POST['wcyp_custom_css'] );        
      ?>
        <div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
          <p><strong>Settings have been save</strong></p>
        </div>
      <?php    
    }
    ?>
    <div class="wrap">

      <h1>Choice a Place - Settings</h1>

      <div class="wrap-wcyp">
        
          <?php
          /*echo "<pre>";
            print_r($wcyp_options);
          echo "</pre>";*/
    
          if(is_super_admin()){
          ?>
            <form method="post">

              <div style="float: right; position: absolute; right: 25px; width: auto;">
                <?php submit_button(); ?>
              </div>

              <p class="description">Enter a location and destination URL</p>

              <table class="form-table" style="width: auto">
                <tbody>
                  <tr valign="top">
                    <th scope="row">Place</th>
                    <td><input type="text" class="regular-text" name="wcyp_place[0][locale]" maxlength="200" placeholder="City/State"></td>
                  </tr>
                  <tr valign="top">
                    <th scope="row">URL</th>
                    <td><input type="url" class="regular-text" name="wcyp_place[0][url]" maxlength="200" placeholder="https://your-domain.com/place"></td>
                  </tr>
                </tbody>
              </table>

              <hr>

              <h2>Listing of locations</h2>

              <p class="description">You can edit or delete the place</p>

              <table class="form-table striped"  style="width: auto">
                <tbody>
                  <?php
                  if(is_array($wcyp_options)){
                    array_filter($wcyp_options);
                    asort($wcyp_options);
                    $wcyp_index = 0;
                    foreach($wcyp_options as $wcyp_value){
                      if( $wcyp_value["locale"] != '' && $wcyp_value["url"]!= '' ){
                        $wcyp_index++;
                        ?>
                        <tr valign="top">
                          <td>
                            <input type="text" class="regular-text" name="wcyp_place[<?php echo $wcyp_index;?>][locale]" maxlength="200" value="<?php echo $wcyp_value["locale"];?>" placeholder="City/State" required>
                          </td>
                          <td>
                            <input type="url" class="regular-text" name="wcyp_place[<?php echo $wcyp_index;?>][url]" maxlength="200" value="<?php echo $wcyp_value["url"];?>" placeholder="https://your-domain.com/place" required>
                          </td>
                          <td width="20"><button type="button" data-index="<?php echo $wcyp_index;?>" class="button button-danger" title="Remove" onClick="jQuery(this).closest('tr').remove();">X</button></td>
                        </tr>
                        <?php
                      }
                    }
                  }
                  ?>
                </tbody>
              </table>

              <hr>
        
              <h2>Custom CSS</h2>
              
              <textarea rows="10" name="wcyp_custom_css" class="regular-text" style="width: 100%"><?php echo (get_option('wcyp_custom_css') != '') ? get_option('wcyp_custom_css') : '#wcyp-toggle{
  display: none;
}
#wcyp-toggle.show{
  font-size: 20px;
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1050;
  width: 100vw;
  height: 100vh;
  background-color:#FF7721;
  display: flex;
  align-items: center;
}
#wcyp-toggle .wcyp-wrap{
  width: 70%; 
  margin: 0 auto;
  min-width:300px;
}
#wcyp-toggle .wcyp-label{
  margin-right: 30px;
  text-transform: uppercase;
  color: #FFF;
}
#wcyp-toggle .wcyp-select{
  padding: 0.1em 70px 1px 0em; 
  border: none; 
  border-radius: 0px; 
  border-bottom: 1px solid #ffe4d3; 
  outline: none; 
  margin: 0px; 
  background: none; 
  color: #ffe4d3; 
  text-transform: uppercase;
  appearance: none;
  float:right;
}
#wcyp-toggle .wcyp-select option{
  color:#333;
}';?></textarea>
              
            </form>

            <hr>

            <fieldset>
              <legend><h2>Instructions</h2></legend>
              <p class="description">
                Insert on your page any button or element that contains the class "wcyp-action" <br>
                Example: 
                <pre><code>&lt;button type=&quot;button&quot; class=&quot;<strong>wcyp-action</strong>&quot;&gt;Change the place&lt;/button&gt;</code></pre>
              </p>
              <p>When the visitor clicks, the select with the options will be displayed</p>
              <p><strong>Basically</strong>:<br>In the front, options will be displayed by default until the visitor chooses. The value of the choice will be stored in cookies.</p>
            </fieldset>

          <?php
          }else{
          ?>
            <p>Only administrators have permission to access</p>
          <?php
          }
          ?>

        
      </div>

    </div>
    <?php
  }
endif;
