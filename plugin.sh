#!/bin/bash

echo "WPLoyalty: Woo discount rule integration auto compress pack"

current_dir="$PWD"
composer_lock_path=$current_dir"/composer.lock"
vendor_path=$current_dir"/vendor"

composer_run(){
  rm "$composer_lock_path"
  rm -r "$vendor_path"
  # shellcheck disable=SC2164
  cd "$current_dir"
  composer install --no-dev
  composer update --no-dev
  echo "Compress done"
  echo "WPLoyalty : Woo discount rule integration Pack done"
  # shellcheck disable=SC2164
  cd $current_dir
}
update_ini_file(){
  cd $current_dir
  wp i18n make-pot . "i18n/languages/wp-loyalty-woo-discount-rule.pot" --slug="wp-loyalty-woo-discount-rule" --domain="wp-loyalty-woo-discount-rule" --include="wp-loyalty-woo-discount-rules.php",/App/ --headers='{"Last-Translator":"WPloyalty","Language-Team":"WPLoyalty"}' --allow-root
  cd $current_dir
  echo "Update ini done"
}
copy_folder(){
  # shellcheck disable=SC2164
  cd $current_dir
  cd ..
  pack_folder=$PWD"/compressed_pack"
  compress_plugin_folder=$pack_folder/"wp-loyalty-woo-discount-rules"
  if [ -d "$pack_folder" ]; then
    rm -r "$pack_folder"
  fi
  mkdir "$pack_folder"
  mkdir "$compress_plugin_folder"
  move_dir=("App" "i18n" "vendor" "wp-loyalty-woo-discount-rules.php")
  # shellcheck disable=SC2068
  for dir in ${move_dir[@]}; do
    cp -r "$current_dir/$dir" "$compress_plugin_folder/$dir"
  done
  cd "$current_dir"
}
zip_folder(){
  cd "$current_dir"
  cd ..
  pack_compress_folder=$PWD"/compressed_pack"
  cd "$pack_compress_folder"
  pack_name="wp-loyalty-woo-discount-rules"
  zip_name="wp-loyalty-woo-discount-rules"
  rm "$zip_name".zip
  zip -r "$zip_name".zip $pack_name -q
  zip -d "$zip_name".zip __MACOSX/\*
  zip -d "$zip_name".zip \*/.DS_Store
}
echo "Composer Run:"
composer_run
echo "Update ini"
update_ini_file
echo "Copy Folder:"
copy_folder
echo "Zip Folder:"
zip_folder
echo "End"