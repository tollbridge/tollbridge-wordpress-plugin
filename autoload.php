<?php
/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the
 * plugin.
 *
 * @package Tollbridge\Paywall
 */

spl_autoload_register('tollbridge_paywall_namespace_autoload');

/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the
 * plugin by looking at the $class_name parameter being passed as an argument.
 *
 * The argument should be in the form: Tollbridge\Paywall. The
 * function will then break the fully-qualified class name into its pieces and
 * will then build a file to the path based on the namespace.
 *
 * The namespaces in this plugin map to the paths in the directory structure.
 *
 * @param string $class_name The fully-qualified name of the class to load.
 */
function tollbridge_paywall_namespace_autoload($class_name)
{
    // If the specified $class_name does not include our namespace, duck out.
    if (false === strpos($class_name, 'Tollbridge\Paywall')) {
        return;
    }

    // Split the class name into an array to read the namespace and class.
    $file_parts = explode('\\', str_replace('Tollbridge\\Paywall\\', '', $class_name));
    $file = implode(DIRECTORY_SEPARATOR, $file_parts);

    // Now build a path to the file using mapping to the file location.
    $filepath  = trailingslashit(dirname(__FILE__)).'classes/'.$file.'.php';

    // If the file exists in the specified path, then include it.
    if (file_exists($filepath)) {
        include_once($filepath);
    } else {
        echo $class_name;
        wp_die(
            esc_html("The file attempting to be loaded at $filepath does not exist.")
        );
    }
}
