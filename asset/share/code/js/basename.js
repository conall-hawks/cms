function basename(path){
   var basename = path.substring(path.lastIndexOf("/") + 1);
    if(basename.lastIndexOf(".") !== -1){
        basename = basename.substring(0, basename.lastIndexOf("."));
    }
   return basename;
}
