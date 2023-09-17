<?php
// Create admin menu
add_action('admin_menu', 'custom_woocommerce_folders_menu');
function custom_woocommerce_folders_menu() {
    add_menu_page(
        'Custom WooCommerce Folders',
        'Custom WooCommerce Folders',
        'manage_options',
        'custom-woocommerce-folders',
        'custom_woocommerce_folders_admin_page'
    );
}

// Admin page content
function custom_woocommerce_folders_admin_page() {
    $message = '';
    $upload_dir = wp_upload_dir();
    $folder_name = get_option('custom_woocommerce_folder', 'products');
    $base_folder = $upload_dir['basedir'] . '/' . $folder_name;

    // Save folder name
    if (isset($_POST['submit'])) {
        update_option('custom_woocommerce_folder', sanitize_text_field($_POST['folder_name']));
        $message = 'Changes saved successfully!';
    }

    // Delete folder
    if (isset($_POST['delete_folder'])) {
        $folder_to_delete = sanitize_text_field($_POST['folder_to_delete']);
        $folder_path = $base_folder . '/' . $folder_to_delete;
        if (is_dir($folder_path)) {
            rmdir($folder_path);
            $message = 'Folder deleted successfully!';
        }
    }

    // Rename folder
    if (isset($_POST['rename_folder'])) {
        $old_folder_name = sanitize_text_field($_POST['old_folder_name']);
        $new_folder_name = sanitize_text_field($_POST['new_folder_name']);
        rename($base_folder . '/' . $old_folder_name, $base_folder . '/' . $new_folder_name);
        $message = 'Folder renamed successfully!';
    }

    // List folders
    if (file_exists($base_folder)) {
        $folders = array_diff(scandir($base_folder), array('.', '..'));
    } else {
        $folders = array();
    }

    ?>
    <div class="wrap">
        <h1>Custom WooCommerce Folders</h1>
        <?php if ($message): ?>
            <div id="message" class="updated notice is-dismissible">
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="folder_name">Folder Name</label></th>
                    <td><input type="text" id="folder_name" name="folder_name" value="<?php echo $folder_name; ?>" /></td>
                </tr>
            </table>
            <input type="submit" name="submit" class="button button-primary" value="Save Changes" />
        </form>

        <h2>Existing Folders</h2>
        <ul>
            <?php foreach ($folders as $folder): ?>
                <li>
                    <?php echo $folder; ?>
                    <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="folder_to_delete" value="<?php echo $folder; ?>">
                        <input type="submit" name="delete_folder" value="Delete" class="button button-small">
                    </form>
                    <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="old_folder_name" value="<?php echo $folder; ?>">
                        <input type="text" name="new_folder_name" placeholder="New Name">
                        <input type="submit" name="rename_folder" value="Rename" class="button button-small">
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}
