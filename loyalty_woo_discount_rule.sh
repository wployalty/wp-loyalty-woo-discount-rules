echo "WPLoyalty: Woo discount rule integration auto compress pack"
current_dir="$PWD/"
pack_folder="wp-loyalty-woo-discount-rules"
plugin_pack_folder="wployalty_woo_discount_rules"
folder_sperate="/"
pack_compress_folder=$current_dir$pack_folder
composer_run(){
  # shellcheck disable=SC2164
  cd "$plugin_pack_folder"
  composer install --no-dev
  composer update --no-dev
  cd $current_dir
}
copy_folder(){
  cd $current_dir
  echo "Compress Dir $pack_compress_folder"
  from_folder="wployalty_woo_discount_rules"
  from_folder_dir=$current_dir$from_folder
  move_dir=("App" "i18n" "vendor" "readme.txt" "wp-loyalty-woo-discount-rules.php")
  if [ -d "$pack_compress_folder" ]
  then
      rm -r "$pack_folder"
      mkdir "$pack_folder"
      # shellcheck disable=SC2068
      for dir in ${move_dir[@]}
      do
        cp -r "$from_folder_dir/$dir" "$pack_compress_folder/$dir"
      done
  else
      mkdir "$pack_folder"
      # shellcheck disable=SC2068
      for dir in ${move_dir[@]}
      do
        cp -r "$from_folder_dir/$dir" "$pack_compress_folder/$dir"
      done
  fi
}
zip_folder(){
  rm "$pack_folder".zip
  zip -r "$pack_folder".zip $pack_folder -q
  zip -d "$pack_folder".zip __MACOSX/\* -q
  zip -d "$pack_folder".zip \*/.DS_Store -q
}
composer_run
copy_folder
zip_folder

echo "End"
