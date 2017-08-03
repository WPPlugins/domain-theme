<?php
/*
Copyright 2008  Stephen Carroll  (email : scarroll@virtuosoft.net)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
                                                                 
Plugin Name: Domain Theme
Plugin URI: http://www.virtuosoft.net/domaintheme
Description: This version supports 2.6.1. This plugin allows you to specify more then one domain name with your WordPress installation and associate a specific theme for each domain. *Please note that accessing wp-admin pages from any other domain then your primary domain name specified in 'General Settings' is not recommended as 'General Settings' and the 'Domain Theme' plugin administration panel will report incorrect settings.
Author: Stephen Carroll
Version: 1.3
Author URI: http://www.steveorevo.com
*/


//$useServerName = true; // If you have issues resolving the server name, try uncommenting this line by removing the first two slashes
if ($useServerName){
    $domainTheme = new domainTheme($_SERVER['SERVER_NAME']);
}else{
    $domainTheme = new domainTheme($_SERVER['HTTP_HOST']);
}
class domainTheme {   
    var $blogdescription;
    var $currentdomain;
    var $stylesheet;
    var $template;
    var $blogname;
    var $siteurl;
    var $options;
    var $home;
    var $uri;
            
    // Based on the current domain name, load the associated theme data
    function domainTheme($domain){
        
        // Get default settings
        $this->blogdescription = get_option("blogdescription");
        $this->stylesheet = get_option("stylesheet");
        $this->template = get_option("template");
        $this->blogname = get_option("blogname");
        $this->siteurl = get_option("siteurl"); // WordPress url
        $this->home = get_option("home"); // Blog address url
        $this->currentdomain = strtolower($domain);
        
        // Load domain option settings
        global $wp_version;
        if ($wp_version=="2.6"){
            
            // 2.6 fix, this version unserializes data for some odd reason.
            $this->options = get_option("domainTheme_options");
        }else{
            $this->options = unserialize(get_option("domainTheme_options"));
        }
        
        if (gettype($this->options)!="array"){
            $this->options = array();
        }
        
        // Locate the matching index for the current domain
        foreach($this->options as $dt){
            if ($this->currentdomain==$dt['url']){
                
                // Update the settings for the matching domain
                $this->blogdescription = $dt['blogdescription'];
                $this->blogname = $dt['blogname'];
                $this->stylesheet = $dt['theme'];
                $this->template = $dt['theme'];
                $url = $this->getLeftMost($this->siteurl,'//').'//'.$dt['url'];
                $this->siteurl = $this->delLeftMost($this->siteurl,'//');
                if (strpos($this->siteurl,'/')>0){
                    $url .= '/'.$this->delLeftMost($this->siteurl, '/');
                }
                $this->siteurl = $url;
                
                $url = $this->getLeftMost($this->home,'//').'//'.$dt['url'];
                $this->home = $this->delLeftMost($this->home,'//');
                if (strpos($this->home,'/')>0){
                    $url .= '/'.$this->delLeftMost($this->home, '/');
                }
                $this->home = $url;
            }   
        }

        
        // Apply filters and actions
        add_filter('pre_option_blogdescription', array(&$this, 'getBlogdescription'));
        add_filter('pre_option_stylesheet', array(&$this, 'getStylesheet'));
        add_filter('pre_option_template', array(&$this, 'getTemplate'));
        add_filter('pre_option_blogname', array(&$this, 'getBlogname'));
        add_filter('pre_option_siteurl', array(&$this, 'getSiteurl'));
        add_filter('pre_option_home', array(&$this, 'getHome'));
        add_action('admin_menu', array(&$this, 'displayAdminMenu'));
        
        // Specify uri for admin panels
        $this->uri = '?page=' . $this->getRightMost(__FILE__, 'plugins/');
    }
    
    // Common string functions
    function getRightMost($sSrc, $sSrch) {        
        for ($i = strlen($sSrc); $i >= 0; $i = $i - 1) {
            $f = strpos($sSrc, $sSrch, $i);
            if ($f !== FALSE) {
               return substr($sSrc,$f + strlen($sSrch), strlen($sSrc));
            }
        }
        return $sSrc;
    }
    function delLeftMost($sSource, $sSearch) {
      for ($i = 0; $i < strlen($sSource); $i = $i + 1) {
        $f = strpos($sSource, $sSearch, $i);
        if ($f !== FALSE) {
           return substr($sSource,$f + strlen($sSearch), strlen($sSource));
           break;
        }
      }
      return $sSource;
    }
    function getLeftMost($sSource, $sSearch) {
      for ($i = 0; $i < strlen($sSource); $i = $i + 1) {
        $f = strpos($sSource, $sSearch, $i);
        if ($f !== FALSE) {
           return substr($sSource,0, $f);
           break;
        }
      }
      return $sSource;
    }    function getThemeTitleByTemplate($template){
        
        // Return descriptive name for a given template name
        $themes = get_themes();
        foreach($themes as $theme){
            if ($template==$theme["Template"]){
                break;
            }
        }
        return $theme["Title"];
    }
    function displayAdminMenu(){
        add_options_page('Domain Theme Options', 'Domain Theme', 8, __FILE__, array(&$this, 'createAdminPanel'));
    }
    
    // Return modified data based on the current domain name
    function getBlogdescription(){
        return $this->blogdescription;
    }
    function getStylesheet(){
        return $this->stylesheet;
    }
    function getTemplate(){
        return $this->template;
    }
    function getBlogname(){
        return $this->blogname;
    }
    function getSiteurl(){
        return $this->siteurl;
    }
    function getHome(){
        return $this->home;
    }
    
    // Create the administration panel
    function createAdminPanel(){
        
        // Check if we need to add a domain
        if ($_GET['action']=="addDomain"){
            $domain['url']=strtolower($_POST['domain']);
            $domain['theme']=$_POST['theme'];
            $domain['blogname']=stripslashes($_POST['blogname']);
            $domain['blogdescription']=stripslashes($_POST['blogdescription']);
            array_push($this->options, $domain);

            update_option("domainTheme_options", serialize($this->options));
        }
        
        // Check if we need to edit a domain
        if ($_GET['action']=="editDomain"){
            $id = $_GET['id'];
            $this->options[$id]['url']=strtolower($_POST['domain']);
            $this->options[$id]['theme']=$_POST['theme'];
            $this->options[$id]['blogname']=stripslashes($_POST['blogname']);
            $this->options[$id]['blogdescription']=stripslashes($_POST['blogdescription']);
            update_option("domainTheme_options", serialize($this->options));
        }
        
        // Check if we need to delete one or more domains
        if ($_GET['action']=="del" && $_POST['chkDelete']){
            foreach(array_reverse($_POST['chkDelete']) as $id){
                array_splice($this->options,$id,1);
            }
            update_option("domainTheme_options", serialize($this->options));
        }
        
        // Check if we should display the edit panel
        if ($_GET['action']=="domainProps"){
            $id = $_GET['id'];
            echo '<div class="wrap">
                    <form name="editDomain" id="editDomain" action="'.$this->uri.'&action=editDomain&id='.$id.'" method="post">
                    <h2>' . __('Edit Domain Theme') . '</h2>
                    <br class="clear" />
                    <div class="tablenav">
                        <br class="clear" />
                    </div>
                    <br class="clear" />
                    <table class="form-table">
                        <tr class="form-field">
                            <th scope="row" valign="top"><label for="domain">Domain</label></th>
                            <td><input name="domain" id="domain" type="text" value="'.$this->options[$id]['url'].'" size="40" /><br />
                            The domain that is used to access the site (i.e. www.example.com).</td>
                        </tr>
                        <tr class="form-field">
                            <th scope="row" valign="top"><label for="theme">Theme</label></th>
                            <td>
                                <select name="theme" id="theme" class="postform" >';
                                $themes = get_themes();
                                foreach($themes as $theme){
                                    if ($theme["Template"]==$this->options[$id]['theme']){
                                        echo '<option value="'.$theme["Template"].'" selected>'.$theme["Name"].'</option>';
                                    }else{
                                        echo '<option value="'.$theme["Template"].'">'.$theme["Name"].'</option>';
                                    }
                                }
            echo '              </select>
                                <br />
                                Specify the theme to use when the site is accessed by the given domain.
                            </td>
                        </tr>
                        <tr class="form-field">
                            <th scope="row" valign="top"><label for="blogname">Blog Title</label></th>
                            <td><input name="blogname" id="blogname" type="text" value="'.htmlspecialchars ($this->options[$id]['blogname']).'" size="40" /><br />
                            The blog title that will be used when the site is accessed by the given domain.</td>
                        </tr>
                        <tr class="form-field">
                            <th scope="row" valign="top"><label for="blogname">Tagline</label></th>
                            <td><input name="blogdescription" id="blogdescription" type="text" value="'.htmlspecialchars ($this->options[$id]['blogdescription']).'" size="45" /><br />
                            In a few words, the blogs description when accessed by the given domain.</td>
                        </tr>
                    </table>
                    <p class="submit"><input type="submit" class="button" name="submit" value="Edit Domain" /></p>
                    </form>
                 </div>';
            return;
        }
        
        // Inject the javascript for delete check all option
        echo '<script language="Javascript">
                (function($){
                    $(function(){
                        $("#chkAll").click(function(){
                            c=this.checked;
                            $(".chkDelete").each(function(i){
                                this.checked=c;
                            })
                        });
                    })
                })(jQuery);
              </script>';
        
        // Create the list
        echo '<div class="wrap">
                <form name="domainList" id="domainList" action="'.$this->uri.'&action=del'.'" method="post">
                <h2>' . __('Domain Theme') . ' (<a href="#addDomain">add new</a>)</h2>
                <br class="clear" />
                <div class="tablenav">
                    <div class="alignleft">
                        <input type="submit" value="Delete" name="deleteit" class="button-secondary delete" />
                    </div>
                    <br class="clear" />
                </div>
                <br class="clear" />
                <table class="widefat">
                <thead>
                    <tr>
                        <th scope="col" class="check-column"><input type="checkbox" id="chkAll" /></th>
                        <th scope="col">Domain</th>
                        <th scope="col">Theme</th>
                        <th scope="col">Blog Title</th>
                        <th scope="col">Tagline</th>
                    </tr>
                </thead>
                <tbody id="the-list" class="list:domain">
                    <tr id="domain-default" class="alternate">             
                        <th scope="row" class="check-column"><input type="checkbox" class="chkDefault" disabled /></th>
                        <td><a href="options-general.php"/>'.$this->currentdomain.'</a></td>
                        <td>'.$this->getThemeTitleByTemplate($this->template).'</td>
                        <td>'.$this->blogname.'</td>
                        <td>'.$this->blogdescription.'</td>
                    </tr>';
        $i=0;
        foreach($this->options as $domain){
            echo'   <tr id="domain-'.$i.'" ';
            if (!fmod($i,2)){
                echo '>';
            }else{
                echo 'class="alternate">'; 
            }
            echo'       <th scope="row" class="check-column"><input type="checkbox" name="chkDelete[]" class="chkDelete" value="'.$i.'" /></th>
                        <td><a href="'.$this->uri.'&action=domainProps&id='.$i.'"/>'.$domain['url'].'</a></td>
                        <td>'.$this->getThemeTitleByTemplate($domain['theme']).'</td>
                        <td>'.$domain['blogname'].'</td>
                        <td>'.$domain['blogdescription'].'</td>
                    </tr>
                </tbody>
                ';            
            $i++;
        }
        
        // Create the add form
        echo '  </table>
                </form>
                <div class="tablenav">
                    <br class="clear" />
                </div>
                </div>
                <br class="clear" />
                <br class="clear" />
                <div class="wrap">
                    <h2>Add Domain</h2>
                    <form name="addDomain" id="addDomain" action="'.$this->uri.'&action=addDomain" method="post">
                        <table class="form-table">
                            <tr class="form-field">
                                <th scope="row" valign="top"><label for="domain">Domain</label></th>
                                <td><input name="domain" id="domain" type="text" value="" size="40" /><br />
                                The domain that is used to access the site (i.e. www.example.com).</td>
                            </tr>
                            <tr class="form-field">
                                <th scope="row" valign="top"><label for="theme">Theme</label></th>
                                <td>
                                    <select name="theme" id="theme" class="postform" >';
                                    $themes = get_themes();
                                    foreach($themes as $theme){
                                        echo '<option value="'.$theme["Template"].'">'.$theme["Name"].'</option>';
                                    }
         echo '                     </select>
                                    <br />
                                    Specify the theme to use when the site is accessed by the given domain.
                                </td>
                            </tr>
                            <tr class="form-field">
                                <th scope="row" valign="top"><label for="blogname">Blog Title</label></th>
                                <td><input name="blogname" id="blogname" type="text" value="" size="40" /><br />
                                The blog title that will be used when the site is accessed by the given domain.</td>
                            </tr>
                            <tr class="form-field">
                                <th scope="row" valign="top"><label for="blogname">Tagline</label></th>
                                <td><input name="blogdescription" id="blogdescription" type="text" value="" size="45" /><br />
                                In a few words, the blogs description when accessed by the given domain.</td>
                            </tr>
                        </table>
                    <p class="submit"><input type="submit" class="button" name="submit" value="Add Domain" /></p>
                    </form>
                </div>
        ';
    }
}       
?>
