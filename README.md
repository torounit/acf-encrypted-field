-----------------------

# ACF { Encrypted Field

Adds a 'Encrypted text' field type for the [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/) WordPress plugin.

-----------------------

### Overview

+ データベースに暗号化したデータを保存するフィールドを追加します。
+ get_post_meta等では複合化されません。ACFのthe_field()等のAPIを通すと複合化されます。
+ キーは、フィールドごとに生成されたもの + 投稿ごとに生成されたものの両方を使うので、同じ文面を暗号化しても別々の暗号文としてカスタムフィールドに保存されます。
+ パスワード欄や、Search Everythingで検索されたくない値等に使えると思います。
+ 個人情報だとか、クレジットカードの情報を保存する用途には向きません。htpasswdのパスワードくらいかと思います。



### Compatibility

This add-on will work with:

* version 4 and up


### Installation

This add-on can be treated as both a WP plugin and a theme include.

**Install as Plugin**

1. Copy the 'acf-encrypted' folder into your plugins folder
2. Activate the plugin via the Plugins admin page

**Include within theme**

1.	Copy the 'acf-encrypted' folder into your theme folder (can use sub folders). You can place the folder anywhere inside the 'wp-content' directory
2.	Edit your functions.php file and add the code below (Make sure the path is correct to include the acf-encrypted.php file)

```php
add_action('acf/register_fields', 'my_register_fields');

function my_register_fields()
{
	include_once('acf-encrypted/acf-encrypted.php');
}
```

### More Information

Please read the readme.txt file for more information