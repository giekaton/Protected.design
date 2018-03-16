// Get data from url to identify pd and then load it
var href = location.href;
shortlink = href.match(new RegExp(".design/" + "(.*)"));
shortlink = shortlink[1].slice(0, 12);
preview_src_3 = href.substring(href.indexOf("?")+1);



// If shortlink has the correct length, then try to build protected design page
if (shortlink.length == 12) {
    app.home = false;
    app.protected_design = true;

    Vue.http.get('wp-json/get_protected_design/get', {params: {shortlink: shortlink}}).then(response => {
        // success callback
        data_protected_design.message_hex = response.body.message;
        data_protected_design.hash = response.body.hash;
        data_protected_design.text_design_url = home_url + response.body.shortlink;
        data_protected_design.text_status = response.body.status;
        data_protected_design.text_file_size = response.body.file_size + " bytes";
        data_protected_design.text_message_ascii = hex2ascii(data_protected_design.message_hex);
        data_protected_design.text_hash = data_protected_design.hash.substring(0,40);
        data_protected_design.text_tx_hash = response.body.tx_hash;
        data_protected_design.text_tx_url = "https://ropsten.etherscan.io/tx/" + data_protected_design.text_tx_hash;

        // Generate hash on the backend to compare it with the protected design hash
        if ((preview_src_3.endsWith('png')) || (preview_src_3.endsWith('jpg')) || (preview_src_3.endsWith('jpeg')) || (preview_src_3.endsWith('gif'))) {
            Vue.http.get('wp-json/php_hash/get', {params: {preview_src: preview_src_3}}).then(response => {
            // success callback
            console.log("1" + data_protected_design.hash.substring(0,12));
            console.log("1" + response.body.substring(0,12));
            if (response.body.substring(0,12) == data_protected_design.hash.substring(0,12)) {
                data_protected_design.preview_text = false;
                data_protected_design.preview_image = true;
                data_protected_design.preview_src = preview_src_3;
            }
            else {
                data_protected_design.preview_text = true;
                data_protected_design.text_file_input = 'The file provided in the URL does not belong to this protected design.'
            }
            }, response => {
            // error callback
            });
        }
    }, response => {
        // error callback
    });

    if ((preview_src_3.endsWith('png')) || (preview_src_3.endsWith('jpg')) || (preview_src_3.endsWith('jpeg')) || (preview_src_3.endsWith('gif'))) { 
        data_protected_design.preview_text = true;
        data_protected_design.text_file_input = 'Comparing IDs and generating preview. Please wait...'
    }
    else { 
        data_protected_design.preview_text = true;
        data_protected_design.text_file_input = 'Drag and drop or click to select your design file to verify it and generate its preview.'
    }
}







// // @todo:
// if (shortlink.length != 12) {
//     app.home = false;
//     app.notfound = true;
// }