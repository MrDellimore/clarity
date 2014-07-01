var getFileName = function (haystack, needle, bool) {
    //  discuss at: http://phpjs.org/functions/strstr/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // bugfixed by: Onno Marsman
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //   example 1: strstr('Kevin van Zonneveld', 'van');
    //   returns 1: 'van Zonneveld'
    //   example 2: strstr('Kevin van Zonneveld', 'van', true);
    //   returns 2: 'Kevin '
    //   example 3: strstr('name@example.com', '@');
    //   returns 3: '@example.com'
    //   example 4: strstr('name@example.com', '@', true);
    //   returns 4: 'name'
    var pos = 0;
    haystack += '';
    pos = haystack.indexOf(needle);
    if (pos == -1) {
        return false;
    } else {
        if (bool) {
            return haystack.substr(0, pos);
        } else {
            return haystack.slice(pos);
        }
    }
}

var getFileSize = function (file){
    console.log(file);
    var filesize = ($.browser.mozilla) ? file.get(0).files[0].size/1024 : file[0].files[0].size / 1024;
//            console.log(filesize);
//            console.log(filesize / 1024);
    if (filesize / 1024 > 1){
//                console.log(filesize);
        if (((filesize / 1024) / 1024) > 1){
            filesize = (Math.round(((filesize / 1024) / 1024) * 100) / 100);
            filesize += ' GB';
//                        $("#lblSize").html( filesize + "Gb");
        }
        else {
            filesize = (Math.round((filesize / 1024) * 100) / 100)
            filesize += ' MB';
        }
    } else{
//                console.log(filesize);
        filesize = (Math.round(filesize * 100) / 100)
        filesize += ' KB';
//                console.log(filesize);
    }
    return filesize;
}