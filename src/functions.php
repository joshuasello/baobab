<?php
/**
 * Global Functions File
 *
 * This file contains the declarations for routines that are used
 * throughout the framework. This script should not us any external clases
 * or have a namespace.
 *
 * @package Baobab
 * @author  Joshua Sello <joshuasello@gmail.com>
 * @since   1.0.0
 */


/**
 * Gets the arguments presented to a running php script.
 *
 * Example Usage:
 *  $ php myscript.php user=nobody password=secret p
 *  Array
 *  (
 *      [user] => nobody
 *      [password] => secret
 *      [p] => true
*   )
*/
function args($argv) {
    $args = [];
    if ($argv) {
        for ($i=1; $i < count($argv); $i++) {
            $arg_parts = explode("=",$argv[$i]);
            if (isset($it[1])) {
                $args[$arg_parts[0]] = $arg_parts[1];
            } else {
                $args[$arg_parts[0]] = true;
            }
        }
    }
  return $args;
}


/**
* Evaluates whether a given array is associative or not.
*/
function is_assoc(array $arr) {
    if (!is_array($arr)) {
        return false;
    }
    if ([] === $arr) {
        return false;
    }
    return array_keys($arr) !== range(0, count($arr) - 1);
}



/**
 * Recursively gets the location of a element in a multi-dimensional
 * array
 *
 * A fullstop is used to signify a new layer in the elements location.
 *
 * @param   array/string    $location   Location of the element.
 * @param   array           $data       Array where the element is located.
 *
 * @return  mixed  Element at the provided location.
 *
 */
function data_get($location, array $data) {
    $parts = (\is_array($location)) ? $location : \explode(".", $location);
    $key = \trim(\array_shift($parts));
    // cast to int if the string is a numeric int
    $key = \is_numeric($key) ? (int) $key : $key;
    if (!array_key_exists($key, $data)) {
        return null;
    }
    if (\count($parts) === 0) {
        return $data[$key];
    }
    $sub_data = null;
    if ($key === "*") {
        $list_element = [];
        foreach ($data[$key] as $sub_data) {
            $list_element[] = data_get($parts, $sub_data);
        }
        return $list_element;
    }
    return data_get($parts, $data[$key]);
}


/**
 * Set a value in a muli-dimensional array
 *
 * A fullstop is used to signify a new layer in the elements location.
 *
 * @param   array/string    $location   Location to set.
 * @param   mixed           $value      Value to insert.
 * @param   array           $data       Array where the element should be set.
 *
 * @return  void
 *
 */
function data_set($location, $value, array $data) {
    $parts = (\is_array($location)) ? $location : \explode(".", $location);
    $key = \trim(\array_shift($parts));
    // cast to int if the string is a numeric int
    $key = \is_numeric($key) ? (int) $key : $key;
    if (\count($parts) === 0) {
        $data[$key] = $value;
    }
    $sub_data = null;
    if ($key === "*") {
        $list_element = [];
        foreach ($data[$key] as $sub_data) {
            $list_element[] = data_set($parts, $value, $sub_data);
        }
        // * check if this works
        $data[$key] = $list_element;
    }
    return data_set($parts, $value, $data[$key]);
}


/**
* Gets the last element in a given array.
*/
function last(array $arr) {
    return $arr[\array_key_last($arr)];
}


/**
* Gets the first element in a given array.
*/
function first(array $arr) {
    return \array_pop(\array_reverse($arr));
}


/**
* Concatenates a list of path string by adding each path head to the previous
* paths tail.
*/
function add_paths(...$paths) {
	$joined_paths = rtrim($paths[0], "/\\");
	for ($i=1; $i < count($paths); $i++) {
		$joined_paths .= DIRECTORY_SEPARATOR . trim($paths[$i], "/\\");
	}
	return $joined_paths;
}


/**
 * Joins a list of paths.
 *
 * NOTE: While joining, any slash prefix is lost.
 *
 * @param   array   $join_paths List of paths.
 *
 * @return  string  Returns Joined path.
 *
 */
function join_paths(...$paths) {
    $join_paths = [];
    foreach ($paths as $path)
        $join_paths = array_merge($join_paths, (array)$path);
    $join_paths = array_filter(array_map(create_function('$p', 'return trim($p, "/");'), $join_paths));
    return join("/", $join_paths);
}


/**
 * Recursively list the contents of a given directory.
 *
 * @param   string  $dir       Path to the directory.
 * @param   array   $results   List of items in the current directory.
 *
 * @return  array   Returns a list of the directory contents.
 *
 */
function rec_dir_contents($dir, &$results=[]){
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
        if(!is_dir($path)) {
            $results[] = $path;
        } else if($value != "." && $value != "..") {
            recursive_get_dir_contents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}
