# PinkCrab Modules : Admin Pages

The PinkCrab framework package for creating admin pages/groups using inheritance very easyly from plugins and themes.

**Requires the PinkCrab loader and hasRegistrationHook interface to be used.**

## Structure

Admin pages are created as a part of group. Be them a single page or a collection

### Menu_Page_Group *::(abstract)class*
This is the main group which registers all pages.

**Implements** *HasRegisterHook*

#### Properties
The following properties should be set when creating a child of
* $this->key              (string) **required**
* $this->menu_title       (string) **required**
* *Optional*
* $this->capability       (string) *default:'manage_options'*
* $this->icon_url  (string) *default:'dashicons-admin-generic'*
* $this->position  (int) *default:85*

The following are accessable within the group, via dependency injection
and internal instantiation using the DI container.
* $this->parent (Page::class)
* $this->children (Page_Collection::class)
* $this->request (Request::class)
* $this->view (View::class)
* $this->page_validator (Page_Validator::class)

#### Define Pages
The parent page will inherit the Key and Menutitle from the group. Leaving only the Title and view to be defined. This is done in the set_parent_age() method.

#### Example
```php
class My_Page_Group extends Menu_Page_Group {

    // Required
    public $key        = 'my_page_group';
    public $menu_title = 'My Group';
    
    // Optional (with fallbacks.)
    public $capability  = (default) 'manage_options';
    public $icon_url    = (default) 'dashicons-admin-generic';
    public $position    = (default) 85;

    /**
     * Setup the primary page.
     * Page accessed when main menu item clicked.
     */
    public function set_parent_page( Page $parent ): Page {
        
        // Required
        $parent->view_template( 'path' );
        $parent->view_data( [ 'key' => 'value' ] );
    
        // Optional
        $parent->title( 'Page Title' );

        return $parent;
    }

    /**
     * Register the child pages
     * Omit if you only want a single page.
     * See blow on how to create a Page.
     */
    public function set_child_pages( Page_Collection $children ): Page_Collection {
		
        // Create child page (see below)
        $child_page_1 = ....;
        $child_page_2 = ....;
        
        $children->add($child_page_1);
        $children->add($child_page_2);

        // Return the child collection.
        return $children;
	}


```